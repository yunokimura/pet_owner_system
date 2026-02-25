<?php

namespace App\Http\Controllers;

use App\Models\Pet;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class PetRegistrationController extends Controller
{
    /**
     * Store a newly created pet in storage.
     */
    public function store(Request $request)
    {
        // Debug: Log all input data
        Log::info('Pet Registration Input:', $request->all());
        
        $validated = $request->validate([
            'pet_name' => 'required|string|max:255',
            'pet_type' => 'required|in:dog,cat',
            'gender' => 'required|in:male,female',
            'pet_breed' => 'required|string|max:255',
            'pet_birthdate' => 'nullable|date',
            'estimated_age' => 'nullable|string',
            'pet_weight' => 'nullable|string|max:50',
            'body_mark_details' => 'nullable|string',
        ]);

        // Get the authenticated user's pet owner profile
        $user = Auth::user();
        
        // Debug: Check if user has petOwner
        Log::info('User ID: ' . $user->id);
        Log::info('User petOwner relation:', ['petOwner' => $user->petOwner]);

        if (!$user->petOwner) {
            return back()->with('error', 'Please complete your pet owner profile first before registering a pet.')->withInput();
        }

        $petOwner = $user->petOwner;

        // Handle file uploads
        $petImagePath = null;
        $bodyMarkImagePath = null;

        if ($request->hasFile('pet_image')) {
            $petImagePath = $request->file('pet_image')->store('pets', 'public');
        }

        if ($request->hasFile('body_mark_image')) {
            $bodyMarkImagePath = $request->file('body_mark_image')->store('pets/body-marks', 'public');
        }

        // Create the pet
        $pet = Pet::create([
            'owner_id' => $petOwner->owner_id,
            'pet_name' => $validated['pet_name'],
            'species' => $validated['pet_type'],
            'breed' => $validated['pet_breed'],
            'sex' => $validated['gender'],
            'birthdate' => $validated['pet_birthdate'] ?? null,
            'estimated_age' => $validated['estimated_age'] ?? null,
            'pet_weight' => $validated['pet_weight'] ?? null,
            'body_mark_details' => $validated['body_mark_details'] ?? null,
            'pet_image' => $petImagePath,
            'body_mark_image' => $bodyMarkImagePath,
            'is_neutered' => $request->has('is_neutered') ? 'yes' : 'no',
            'is_crossbreed' => $request->has('is_crossbreed') ? 'yes' : 'no',
            // JSON fields - encode arrays
            'training' => json_encode($request->input('training', [])),
            'insurance' => json_encode($request->input('insurance', [])),
            'behavior' => json_encode($request->input('behavior', [])),
            'likes' => json_encode($request->input('likes', [])),
            'dislikes' => json_encode($request->input('dislikes', [])),
            'diet' => json_encode($request->input('diet', [])),
            'allergy' => json_encode($request->input('allergy', [])),
        ]);
        
        Log::info('Pet created successfully with ID: ' . $pet->pet_id);

        return redirect()->route('owner.dashboard')->with('success', 'Pet registered successfully!');
    }
}
