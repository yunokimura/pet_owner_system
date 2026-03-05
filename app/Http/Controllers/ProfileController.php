<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        return view('profile.edit', [
            'user' => $request->user(),
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(Request $request): RedirectResponse
    {
        $user = $request->user();
        $editSection = $request->input('edit_section', 'personal');

        if ($editSection === 'personal') {
            // Validate personal information
            $request->validate([
                'first_name' => ['required', 'string', 'max:255'],
                'middle_name' => ['nullable', 'string', 'max:255'],
                'last_name' => ['required', 'string', 'max:255'],
                'phone_number' => ['required', 'string', 'max:255'],
                'dob_year' => ['nullable', 'integer', 'min:1900', 'max:2100'],
                'dob_month' => ['nullable', 'integer', 'min:1', 'max:12'],
                'dob_day' => ['nullable', 'integer', 'min:1', 'max:31'],
            ]);

            // Update User name
            $user->name = $request->first_name . ' ' . $request->last_name;

            // Handle date of birth
            if ($request->filled('dob_year') && $request->filled('dob_month') && $request->filled('dob_day')) {
                $dateOfBirth = $request->dob_year . '-' . str_pad($request->dob_month, 2, '0', STR_PAD_LEFT) . '-' . str_pad($request->dob_day, 2, '0', STR_PAD_LEFT);
                $user->date_of_birth = $dateOfBirth;
            }

            $user->save();

            // Update PetOwner
            if ($user->petOwner) {
                $user->petOwner->first_name = $request->first_name;
                $user->petOwner->middle_name = $request->middle_name;
                $user->petOwner->last_name = $request->last_name;
                $user->petOwner->phone_number = $request->phone_number;
                
                if ($request->filled('dob_year') && $request->filled('dob_month') && $request->filled('dob_day')) {
                    $user->petOwner->date_of_birth = $dateOfBirth;
                }
                
                $user->petOwner->save();
            }
        } elseif ($editSection === 'address') {
            // Validate address information
            $request->validate([
                'house_no' => ['required', 'string', 'max:255'],
                'street' => ['required', 'string', 'max:255'],
                'subdivision' => ['required', 'string', 'max:255'],
                'barangay' => ['required', 'string', 'max:255'],
            ]);

            // Update PetOwner address
            if ($user->petOwner) {
                $user->petOwner->house_no = $request->house_no;
                $user->petOwner->street = $request->street;
                $user->petOwner->subdivision = $request->subdivision;
                $user->petOwner->barangay = $request->barangay;
                $user->petOwner->save();
            }
        } elseif ($editSection === 'email') {
            // Validate email
            $request->validate([
                'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:users,email,' . $user->id],
            ]);

            $user->email = $request->email;
            $user->email_verified_at = null;
            $user->save();
        }

        return Redirect::route('profile.edit')->with('status', 'profile-updated');
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
}
