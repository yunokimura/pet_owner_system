<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Appointment extends Model
{
    use HasFactory;

    protected $fillable = [
        'appointment_date',
        'appointment_time',
        'service_type',
        'service_id',
        'status',
        'total_weight',
        'metadata',
    ];

    protected $casts = [
        'appointment_date' => 'date',
        'appointment_time' => 'time',
        'metadata' => 'array',
        'total_weight' => 'integer',
    ];

    // Max 2 units per hour slot (2 vets working)
    const HOURLY_CAPACITY = 2;
    // Max 12 units per day (manageable workload)
    const DAILY_CAPACITY = 12;
    
    const WEIGHTS = [
        'vaccination' => 1,
        'kapon' => 2,
        'adoption_interview' => 1,
    ];

    const AVAILABLE_SLOTS = ['08:00', '09:00', '10:00', '11:00', '13:00', '14:00', '15:00'];
    const BLOCKED_SLOTS = ['12:00', '16:00'];

    public function service(): MorphTo
    {
        return $this->morphTo('service', 'service_type', 'service_id');
    }

    public function getWeightUsedAttribute(): int
    {
        return $this->total_weight;
    }

    public function getRemainingCapacityAttribute(): int
    {
        return self::HOURLY_CAPACITY - $this->total_weight;
    }

    public function isFull(): bool
    {
        return $this->total_weight >= self::HOURLY_CAPACITY;
    }

    public function isLimited(): bool
    {
        return $this->total_weight >= 1 && $this->total_weight < self::HOURLY_CAPACITY;
    }

    public function isAvailable(): bool
    {
        return $this->total_weight < self::HOURLY_CAPACITY;
    }

    public function isBlocked(): bool
    {
        return in_array($this->appointment_time, self::BLOCKED_SLOTS);
    }

    public static function getDailyWeightUsed(string $date): int
    {
        return self::where('appointment_date', $date)->sum('total_weight');
    }

    public static function isDailyFull(string $date): bool
    {
        return self::getDailyWeightUsed($date) >= self::DAILY_CAPACITY;
    }

    public static function getDailyRemainingCapacity(string $date): int
    {
        return self::DAILY_CAPACITY - self::getDailyWeightUsed($date);
    }

    public static function getWeight(string $serviceType): int
    {
        return self::WEIGHTS[$serviceType] ?? 1;
    }

    public static function getAvailableSlots(): array
    {
        return self::AVAILABLE_SLOTS;
    }

    public static function getBlockedSlots(): array
    {
        return self::BLOCKED_SLOTS;
    }

    public static function getSlotStatus(string $time, ?int $currentWeight, string $date = null): string
    {
        if (in_array($time, self::BLOCKED_SLOTS)) {
            return 'blocked';
        }
        
        // Check daily capacity first
        if ($date && self::isDailyFull($date)) {
            return 'full';
        }
        
        if ($currentWeight === null) {
            // No appointment exists yet, check daily capacity
            if ($date && self::isDailyFull($date)) {
                return 'full';
            }
            return 'available';
        }
        
        // Hourly capacity check
        if ($currentWeight >= self::HOURLY_CAPACITY) {
            return 'full';
        }
        
        if ($currentWeight >= 1 && $currentWeight < self::HOURLY_CAPACITY) {
            return 'limited';
        }
        
        return 'available';
    }

    public function scopeForDate($query, $date)
    {
        return $query->where('appointment_date', $date);
    }

    public function scopeForDateAndTime($query, $date, $time)
    {
        return $query->where('appointment_date', $date)
                     ->where('appointment_time', $time);
    }

    public function scopeScheduled($query)
    {
        return $query->where('status', 'scheduled');
    }

    public function scopeByServiceType($query, string $type)
    {
        return $query->where('service_type', $type);
    }

    public function addMergedPet(int $petId): void
    {
        $metadata = $this->metadata ?? [];
        $mergedPets = $metadata['merged_pet_ids'] ?? [];
        
        if (!in_array($petId, $mergedPets)) {
            $mergedPets[] = $petId;
            $metadata['merged_pet_ids'] = $mergedPets;
            $this->metadata = $metadata;
            $this->save();
        }
    }

    public function hasMergedPet(int $petId): bool
    {
        $mergedPets = $this->metadata['merged_pet_ids'] ?? [];
        return in_array($petId, $mergedPets);
    }

    public function setMeetingLink(string $link): void
    {
        $metadata = $this->metadata ?? [];
        $metadata['meeting_link'] = $link;
        $this->metadata = $metadata;
        $this->save();
    }

    public function getMeetingLinkAttribute(): ?string
    {
        return $this->metadata['meeting_link'] ?? null;
    }
}