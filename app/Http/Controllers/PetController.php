<?php

namespace App\Http\Controllers;

use App\Models\Pet;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PetController extends Controller
{
    /**
     * Show the edit form for a pet.
     */
    public function edit($id)
    {
        $user = Auth::user();
        $petOwner = $user->petOwner;
        
        if (!$petOwner) {
            return redirect()->route('owner.dashboard')->with('error', 'Please complete your profile first.');
        }
        
        $pet = Pet::where('pet_id', $id)
            ->where('owner_id', $petOwner->owner_id)
            ->first();
        
        if (!$pet) {
            return redirect()->route('owner.pets')->with('error', 'Pet not found.');
        }
        
        return view('edit_pet', compact('pet'));
    }
    
    /**
     * Update the pet.
     */
    public function update(Request $request, $id)
    {
        $user = Auth::user();
        $petOwner = $user->petOwner;
        
        if (!$petOwner) {
            return redirect()->route('owner.dashboard')->with('error', 'Please complete your profile first.');
        }
        
        $pet = Pet::where('pet_id', $id)
            ->where('owner_id', $petOwner->owner_id)
            ->first();
        
        if (!$pet) {
            return redirect()->route('owner.pets')->with('error', 'Pet not found.');
        }
        
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
        
        // Handle file uploads
        if ($request->hasFile('pet_image')) {
            $pet->pet_image = $request->file('pet_image')->store('pets', 'public');
        }
        
        if ($request->hasFile('body_mark_image')) {
            $pet->body_mark_image = $request->file('body_mark_image')->store('pets/body-marks', 'public');
        }
        
        // Update pet
        $pet->update([
            'pet_name' => $validated['pet_name'],
            'species' => $validated['pet_type'],
            'breed' => $validated['pet_breed'],
            'sex' => $validated['gender'],
            'birthdate' => $validated['pet_birthdate'] ?? null,
            'estimated_age' => $validated['estimated_age'] ?? null,
            'pet_weight' => $validated['pet_weight'] ?? null,
            'body_mark_details' => $validated['body_mark_details'] ?? null,
            'is_neutered' => $request->has('is_neutered') ? 'yes' : 'no',
            'is_crossbreed' => $request->has('is_crossbreed') ? 'yes' : 'no',
            // Handle array inputs from pill boxes
            'training' => $request->has('training') ? json_encode($request->input('training')) : json_encode([]),
            'insurance' => $request->has('insurance') ? json_encode($request->input('insurance')) : json_encode([]),
            'behavior' => $request->has('behavior') ? json_encode($request->input('behavior')) : json_encode([]),
            'likes' => $request->has('likes') ? json_encode($request->input('likes')) : json_encode([]),
            'dislikes' => $request->has('dislikes') ? json_encode($request->input('dislikes')) : json_encode([]),
            'diet' => $request->has('diet') ? json_encode($request->input('diet')) : json_encode([]),
            'allergy' => $request->has('allergy') ? json_encode($request->input('allergy')) : json_encode([]),
        ]);
        
        return redirect()->route('owner.pets')->with('success', 'Pet updated successfully!');
    }
    
    /**
     * Delete the pet.
     */
    public function destroy($id)
    {
        $user = Auth::user();
        $petOwner = $user->petOwner;
        
        if (!$petOwner) {
            return redirect()->route('owner.dashboard')->with('error', 'Please complete your profile first.');
        }
        
        $pet = Pet::where('pet_id', $id)
            ->where('owner_id', $petOwner->owner_id)
            ->first();
        
        if (!$pet) {
            return redirect()->route('owner.pets')->with('error', 'Pet not found.');
        }
        
        // Delete the pet image if exists
        if ($pet->pet_image) {
            \Storage::disk('public')->delete($pet->pet_image);
        }
        
        $pet->delete();
        
        return redirect()->route('owner.pets')->with('success', 'Pet deleted successfully!');
    }
}
