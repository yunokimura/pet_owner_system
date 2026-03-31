<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\OtpController;
use App\Http\Controllers\PetRegistrationController;
use App\Http\Controllers\PetController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Mail;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::get('/test-email', function () {
    Mail::raw('Test email from Laravel!', function ($message) {
        $message->to('kevinryansaw@gmail.com')->subject('Test Email');
    });
    return 'Email sent! Check your Gmail inbox.';
});

// OTP Routes
Route::prefix('otp')->group(function () {
    Route::get('/verify', [OtpController::class, 'showVerifyForm'])->name('otp.verify.form');
    Route::post('/send', [OtpController::class, 'sendOtp'])->name('otp.send');
    Route::post('/verify', [OtpController::class, 'verifyOtp'])->name('otp.verify');
    Route::get('/resend', [OtpController::class, 'resendOtp'])->name('otp.resend');
});

// Password Reset OTP Routes
Route::prefix('password')->group(function () {
    Route::get('/otp/verify', [OtpController::class, 'showResetVerifyForm'])->name('password.otp.form');
    Route::post('/otp/send', [OtpController::class, 'sendResetOtp'])->name('password.otp.send');
    Route::post('/otp/verify', [OtpController::class, 'verifyResetOtp'])->name('password.otp.verify');
    Route::get('/otp/resend', [OtpController::class, 'resendResetOtp'])->name('password.otp.resend');
});

// Services Page Route
Route::get('/services', function () {
    return view('services');
});

// About Us Page Route
Route::get('/about-us', function () {
    return view('about_us');
});

// Kapon Page Route
Route::get('/kapon', function () {
    return view('kapon');
});

// Kapon Form Page Route
Route::get('/kapon/form', function () {
    $user = auth()->user();
    $petOwner = $user ? $user->petOwner : null;
    $pets = $petOwner ? $petOwner->pets : collect([]);
    
    // Convert pets to simple array to avoid Blade @json issues
    $petsArray = $pets->map(function($pet) {
        return [
            'id' => $pet->pet_id,
            'name' => $pet->pet_name,
            'species' => $pet->species,
            'breed' => $pet->breed,
            'age' => $pet->estimated_age,
            'weight' => $pet->pet_weight,
            'image' => $pet->pet_image
        ];
    })->toArray();
    
    return view('kapon_form', compact('user', 'petOwner', 'petsArray'));
});

// Adoption Page Route
Route::get('/adoption', function () {
    $adoptionPets = \App\Models\AdoptionPet::paginate(10);
    return view('adoption', compact('adoptionPets'));
});

// Adoption Form Page Route
Route::get('/adoption/form', function () {
    $user = auth()->user();
    $petOwner = $user ? $user->petOwner : null;
    return view('adoption_form', compact('user', 'petOwner'));
});

// Animal Cruelty Page Route
Route::get('/animal-cruelty', function () {
    return view('animal_cruelty_page');
});

// Animal Cruelty Form Page Route
Route::get('/animal-cruelty/form', function () {
    return view('animal_cruelty_form');
});

// Missing Pets Page Route
Route::get('/missing-pets', function () {
    return view('missing_pets_page');
});

// Pet Registration Page Route
Route::get('/pet-registration', function () {
    return view('pet_registration_page');
})->name('pet.registration');

// Pet Registration Form Page Route
Route::get('/pet-registration/form', function () {
    return view('pet_registration_form');
});

// Pet Registration Form POST Route
Route::post('/pet-registration/form', [PetRegistrationController::class, 'store'])->name('pet.registration.store');

// Vaccination Page Route
Route::get('/vaccination', function () {
    return view('vaccination_page');
});

// Vaccination Form Page Route
Route::get('/vaccination/form', function () {
    $user = auth()->user();
    $petOwner = $user ? $user->petOwner : null;
    $pets = $petOwner ? $petOwner->pets : collect([]);
    
    // Convert pets to simple array to avoid Blade @json issues
    $petsArray = $pets->map(function($pet) {
        return [
            'id' => $pet->pet_id,
            'name' => $pet->pet_name,
            'species' => $pet->species,
            'breed' => $pet->breed,
            'age' => $pet->estimated_age,
            'weight' => $pet->pet_weight,
            'image' => $pet->pet_image
        ];
    })->toArray();
    
    return view('vaccination_form', compact('user', 'petOwner', 'petsArray'));
});

// Owner Dashboard Route
Route::get('/owner/dashboard', function () {
    return view('owner_dashboard');
})->middleware(['auth', 'verified'])->name('owner.dashboard');

// View Pets Route
Route::get('/owner/pets', function () {
    return view('view_pets');
})->middleware(['auth', 'verified'])->name('owner.pets');

// Edit Pet Route
Route::get('/owner/pets/{id}/edit', [PetController::class, 'edit'])->middleware(['auth', 'verified'])->name('pet.edit');

// Update Pet Route
Route::put('/owner/pets/{id}', [PetController::class, 'update'])->middleware(['auth', 'verified'])->name('pet.update');

// Delete Pet Route
Route::delete('/owner/pets/{id}', [PetController::class, 'destroy'])->middleware(['auth', 'verified'])->name('pet.destroy');

// Default Dashboard Route - redirects to owner dashboard for now
Route::get('/dashboard', function () {
    return redirect()->route('owner.dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
