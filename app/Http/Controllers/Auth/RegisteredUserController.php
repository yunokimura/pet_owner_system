<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Mail\OtpMail;
use App\Models\User;
use App\Models\PetOwner;
use App\Providers\RouteServiceProvider;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\Rules;
use Illuminate\View\View;
use Carbon\Carbon;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'middle_name' => ['nullable', 'string', 'max:255'],
            'suffix' => ['nullable', 'string', 'max:255'],
            'dob_year' => ['required', 'integer', 'min:1900', 'max:2100'],
            'dob_month' => ['required', 'integer', 'min:1', 'max:12'],
            'dob_day' => ['required', 'integer', 'min:1', 'max:31'],
            'phone_number' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'block_lot_phase_house_no' => ['required', 'string', 'max:255'],
            'street_name' => ['required', 'string', 'max:255'],
            'subdivision' => ['required', 'string', 'max:255'],
            'barangay' => ['required', 'string', 'max:255'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        // Create user with 'owner' role
        $user = User::create([
            'name' => $request->first_name . ' ' . $request->last_name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'owner',
            'is_verified' => false,
            'date_of_birth' => $request->dob_year . '-' . str_pad($request->dob_month, 2, '0', STR_PAD_LEFT) . '-' . str_pad($request->dob_day, 2, '0', STR_PAD_LEFT),
        ]);

        // Create pet owner profile
        PetOwner::create([
            'user_id' => $user->id,
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'middle_name' => $request->middle_name,
            'suffix' => $request->suffix,
            'phone_number' => $request->phone_number,
            'house_no' => $request->block_lot_phase_house_no,
            'street' => $request->street_name,
            'subdivision' => $request->subdivision,
            'barangay' => $request->barangay,
            'city' => 'Dasmariñas City',
            'province' => 'Cavite',
            'date_of_birth' => $request->dob_year . '-' . str_pad($request->dob_month, 2, '0', STR_PAD_LEFT) . '-' . str_pad($request->dob_day, 2, '0', STR_PAD_LEFT),
        ]);

        // Generate OTP
        $otp = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
        
        // Save OTP to user (expires in 10 minutes)
        $user->update([
            'otp_code' => $otp,
            'otp_expires_at' => Carbon::now()->addMinutes(10)
        ]);

        // Send OTP email
        Mail::to($user->email)->send(new OtpMail($otp));

        // Store email in session for OTP verification
        session(['email' => $user->email]);

        // Logout the user (they need to verify email first)
        Auth::logout();

        // Redirect to OTP verification page with success message
        return redirect()->route('otp.verify.form')->with('otp_sent', true);
    }
}
