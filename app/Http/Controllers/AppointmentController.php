<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\MedicalRecord;
use App\Models\AdoptionApplication;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

class AppointmentController extends Controller
{
    /**
     * Check availability for a specific date/time slot
     */
    public function checkAvailability(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'date' => 'required|date|after_or_equal:today',
            'time' => 'required|date_format:H:i',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $date = $request->input('date');
        $time = $request->input('time');

        // Check if blocked
        if (in_array($time, Appointment::getBlockedSlots())) {
            return response()->json([
                'success' => true,
                'available' => false,
                'status' => 'blocked',
                'message' => 'This time slot is not available'
            ]);
        }

        $appointment = Appointment::forDateAndTime($date, $time)->first();

        if (!$appointment) {
            return response()->json([
                'success' => true,
                'available' => true,
                'status' => 'available',
                'remaining_capacity' => Appointment::DAILY_CAPACITY
            ]);
        }

        return response()->json([
            'success' => true,
            'available' => $appointment->isAvailable(),
            'status' => Appointment::getSlotStatus($time, $appointment->total_weight),
            'remaining_capacity' => $appointment->remainingCapacity,
            'current_weight' => $appointment->total_weight
        ]);
    }

    /**
     * Get all slots for a specific date (for calendar UI)
     */
    public function getSlotsForDate(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'date' => 'required|date|after_or_equal:today',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $date = $request->input('date');
        $dailyWeightUsed = Appointment::getDailyWeightUsed($date);
        $dailyRemaining = Appointment::DAILY_CAPACITY - $dailyWeightUsed;
        $slots = [];

        foreach (Appointment::getAvailableSlots() as $time) {
            $appointment = Appointment::forDateAndTime($date, $time)->first();
            $weight = $appointment ? $appointment->total_weight : 0;

            $slots[] = [
                'time' => $time,
                'display_time' => Carbon::parse($time)->format('g:i A'),
                'status' => Appointment::getSlotStatus($time, $weight, $date),
                'remaining_capacity' => Appointment::HOURLY_CAPACITY - $weight,
                'current_weight' => $weight,
                'is_blocked' => in_array($time, Appointment::getBlockedSlots()),
                'hourly_full' => $weight >= Appointment::HOURLY_CAPACITY,
            ];
        }

        // Add blocked slots
        foreach (Appointment::getBlockedSlots() as $time) {
            $slots[] = [
                'time' => $time,
                'display_time' => Carbon::parse($time)->format('g:i A'),
                'status' => 'blocked',
                'remaining_capacity' => 0,
                'current_weight' => 0,
                'is_blocked' => true,
                'hourly_full' => false,
            ];
        }

        // Sort by time
        usort($slots, function ($a, $b) {
            return strcmp($a['time'], $b['time']);
        });

        return response()->json([
            'success' => true,
            'date' => $date,
            'slots' => $slots,
            'capacity_limit' => Appointment::DAILY_CAPACITY,
            'daily_weight_used' => $dailyWeightUsed,
            'daily_remaining' => $dailyRemaining,
            'hourly_capacity' => Appointment::HOURLY_CAPACITY,
        ]);
    }

    /**
     * Create or merge appointment (with database transaction)
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'appointment_date' => 'required|date|after_or_equal:today',
            'appointment_time' => 'required|date_format:H:i',
            'service_type' => 'required|in:vaccination,kapon,adoption_interview',
            'service_id' => 'required|integer',
            'pet_ids' => 'required|array|min:1',
            'pet_ids.*' => 'integer',
            'meeting_link' => 'nullable|url',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $date = $request->input('appointment_date');
        $time = $request->input('appointment_time');
        $serviceType = $request->input('service_type');
        $serviceId = $request->input('service_id');
        $petIds = $request->input('pet_ids');
        $meetingLink = $request->input('meeting_link');

        // Validate service exists
        if (!$this->validateService($serviceType, $serviceId)) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid service reference'
            ], 422);
        }

        // Check blocked time
        if (in_array($time, Appointment::getBlockedSlots())) {
            return response()->json([
                'success' => false,
                'message' => 'This time slot is not available'
            ], 422);
        }

        try {
            $result = DB::transaction(function () use ($date, $time, $serviceType, $serviceId, $petIds, $meetingLink) {
                // Calculate new weight
                $newWeight = Appointment::getWeight($serviceType) * count($petIds);

                // ===== HOURLY CAPACITY CHECK =====
                $slotAppointment = Appointment::forDateAndTime($date, $time)
                    ->lockForUpdate()
                    ->first();

                if ($slotAppointment) {
                    // Check hourly capacity (max 2 units per slot)
                    if ($slotAppointment->total_weight + $newWeight > Appointment::HOURLY_CAPACITY) {
                        throw new \Exception('This time slot has reached hourly capacity (max 2 units)');
                    }
                } else {
                    // For new slot, check if adding would exceed hourly capacity
                    if ($newWeight > Appointment::HOURLY_CAPACITY) {
                        throw new \Exception('Service weight exceeds hourly capacity');
                    }
                }

                // ===== DAILY CAPACITY CHECK =====
                $dailyWeightUsed = Appointment::getDailyWeightUsed($date);
                if ($dailyWeightUsed + $newWeight > Appointment::DAILY_CAPACITY) {
                    throw new \Exception('Daily appointment capacity reached (max 12 units). Please select another date.');
                }

                // Try to find existing appointment for merge check (same day, overlapping pets)
                $existingAppointment = $this->findMergeCandidate($date, $petIds, $serviceType);

                if ($existingAppointment) {
                    // Merge: add weight and update metadata
                    // Check hourly capacity for merge
                    if ($existingAppointment->total_weight + $newWeight > Appointment::HOURLY_CAPACITY) {
                        throw new \Exception('Adding this would exceed hourly capacity (max 2 units)');
                    }

                    $existingAppointment->total_weight += $newWeight;
                    
                    // Add merged pet IDs to metadata
                    foreach ($petIds as $petId) {
                        $existingAppointment->addMergedPet($petId);
                    }

                    // Add meeting link if provided (adoption interview)
                    if ($serviceType === 'adoption_interview' && $meetingLink) {
                        $existingAppointment->setMeetingLink($meetingLink);
                    }

                    $existingAppointment->save();

                    return [
                        'action' => 'merged',
                        'appointment' => $existingAppointment
                    ];
                }

                // No merge - check if slot exists
                $slotAppointment = Appointment::forDateAndTime($date, $time)
                    ->lockForUpdate()
                    ->first();

                if ($slotAppointment) {
                    // Slot exists - check hourly capacity
                    if ($slotAppointment->total_weight + $newWeight > Appointment::HOURLY_CAPACITY) {
                        throw new \Exception('This time slot has reached hourly capacity (max 2 units)');
                    }

                    // Add to existing slot
                    $slotAppointment->total_weight += $newWeight;
                    
                    // Add merged pet IDs
                    foreach ($petIds as $petId) {
                        $slotAppointment->addMergedPet($petId);
                    }

                    // Add meeting link if provided
                    if ($serviceType === 'adoption_interview' && $meetingLink) {
                        $slotAppointment->setMeetingLink($meetingLink);
                    }

                    $slotAppointment->save();

                    return [
                        'action' => 'updated',
                        'appointment' => $slotAppointment
                    ];
                }

                // Create new appointment
                $appointment = Appointment::create([
                    'appointment_date' => $date,
                    'appointment_time' => $time,
                    'service_type' => $serviceType,
                    'service_id' => $serviceId,
                    'status' => 'scheduled',
                    'total_weight' => $newWeight,
                    'metadata' => [
                        'merged_pet_ids' => $petIds,
                        'meeting_link' => $serviceType === 'adoption_interview' ? $meetingLink : null,
                    ]
                ]);

                return [
                    'action' => 'created',
                    'appointment' => $appointment
                ];
            });

            return response()->json([
                'success' => true,
                'message' => $result['action'] === 'merged' 
                    ? 'Appointment merged with existing slot' 
                    : ($result['action'] === 'updated' 
                        ? 'Added to existing time slot' 
                        : 'Appointment created successfully'),
                'action' => $result['action'],
                'appointment_id' => $result['appointment']->id,
                'appointment_date' => $result['appointment']->appointment_date,
                'appointment_time' => $result['appointment']->appointment_time,
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 422);
        }
    }

    /**
     * Validate that the service exists
     */
    private function validateService(string $type, int $id): bool
    {
        switch ($type) {
            case 'vaccination':
            case 'kapon':
                return MedicalRecord::where('id', $id)
                    ->where('type', $type)
                    ->exists();
            case 'adoption_interview':
                return AdoptionApplication::where('id', $id)->exists();
            default:
                return false;
        }
    }

    /**
     * Find appointment that can be merged with
     */
    private function findMergeCandidate(string $date, array $petIds, string $serviceType): ?Appointment
    {
        // Find appointments on same date with merged pets
        $appointments = Appointment::forDate($date)
            ->where('service_type', $serviceType)
            ->where('status', 'scheduled')
            ->get();

        foreach ($appointments as $appointment) {
            foreach ($petIds as $petId) {
                if ($appointment->hasMergedPet($petId)) {
                    return $appointment;
                }
            }
        }

        return null;
    }

    /**
     * Get all appointments (for admin dashboard)
     */
    public function index(Request $request)
    {
        $query = Appointment::query();

        if ($request->has('date')) {
            $query->forDate($request->input('date'));
        }

        if ($request->has('service_type')) {
            $query->byServiceType($request->input('service_type'));
        }

        if ($request->has('status')) {
            $query->where('status', $request->input('status'));
        }

        $appointments = $query->orderBy('appointment_date')
            ->orderBy('appointment_time')
            ->paginate(20);

        return response()->json([
            'success' => true,
            'appointments' => $appointments
        ]);
    }

    /**
     * Update appointment status
     */
    public function updateStatus(Request $request, int $id)
    {
        $validator = Validator::make($request->all(), [
            'status' => 'required|in:pending,scheduled,completed,cancelled,no_show',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $appointment = Appointment::findOrFail($id);
        $appointment->status = $request->input('status');
        $appointment->save();

        return response()->json([
            'success' => true,
            'message' => 'Appointment status updated',
            'appointment' => $appointment
        ]);
    }
}