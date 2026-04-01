<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\OtpController;
use App\Http\Controllers\PetRegistrationController;
use App\Http\Controllers\PetController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Mail;
use Illuminate\Http\Request;

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
Route::get('/adoption', function (\Illuminate\Http\Request $request) {
    // Get user's existing pets for filtering recommendations
    $userPets = [];
    $petOwner = null;
    $hasPets = false;
    
    if (auth()->check()) {
        $petOwner = auth()->user()->petOwner;
        if ($petOwner) {
            $userPets = $petOwner->pets()->get(['species', 'sex', 'behavior', 'likes', 'dislikes']);
            $hasPets = $petOwner->pets()->exists();
        }
    }
    
    // Apply smart filtering based on existing pets
    $adoptionPets = \App\Models\AdoptionPet::with('traits');
    
    // Get unique breeds for the filter dropdown
    $availableBreeds = \App\Models\AdoptionPet::whereNotNull('breed')
        ->where('breed', '!=', '')
        ->distinct()
        ->orderBy('breed')
        ->pluck('breed');
    
    // Get all available traits for the filter dropdown
    $availableTraits = \App\Models\AdoptionTrait::orderBy('name')->pluck('name');
    
    // Handle filter parameter (special filters like recommended)
    $filter = $request->input('filter', 'all');
    
    // Handle species filter
    $species = $request->input('species', 'all');
    
    // Handle gender filter
    $gender = $request->input('gender', 'all');
    
    // Handle age filter
    $age = $request->input('age', 'all');
    
    // Handle breed filter (can be multiple, comma-separated)
    $breeds = $request->input('breeds', 'all');
    
    // Apply species filter
    if ($species === 'Dog') {
        $adoptionPets = $adoptionPets->where('species', 'Dog');
    } elseif ($species === 'Cat') {
        $adoptionPets = $adoptionPets->where('species', 'Cat');
    }
    
    // Apply gender filter
    if ($gender === 'Male') {
        $adoptionPets = $adoptionPets->where('gender', 'Male');
    } elseif ($gender === 'Female') {
        $adoptionPets = $adoptionPets->where('gender', 'Female');
    }
    
    // Apply age filter (using date_of_birth)
    if ($age === '0-6') {
        $adoptionPets = $adoptionPets->whereNotNull('date_of_birth')
                                   ->where('date_of_birth', '>=', now()->subMonths(6));
    } elseif ($age === '6-12') {
        $adoptionPets = $adoptionPets->whereNotNull('date_of_birth')
                                   ->where('date_of_birth', '<', now()->subMonths(6))
                                   ->where('date_of_birth', '>=', now()->subMonths(12));
    } elseif ($age === '1-3') {
        $adoptionPets = $adoptionPets->whereNotNull('date_of_birth')
                                   ->where('date_of_birth', '<', now()->subMonths(12))
                                   ->where('date_of_birth', '>=', now()->subYears(3));
    } elseif ($age === '3+') {
        $adoptionPets = $adoptionPets->whereNotNull('date_of_birth')
                                   ->where('date_of_birth', '<', now()->subYears(3));
    }
    
    // Apply breed filter
    if ($breeds !== 'all' && !empty($breeds)) {
        $breedArray = explode(',', $breeds);
        $adoptionPets = $adoptionPets->whereIn('breed', $breedArray);
    }
    
    // Handle traits filter
    $traits = $request->input('traits', 'all');
    if ($traits !== 'all' && !empty($traits)) {
        $traitArray = explode(',', $traits);
        $adoptionPets = $adoptionPets->whereHas('traits', function($query) use ($traitArray) {
            $query->whereIn('name', $traitArray);
        });
    }
    
    // Handle special filters
    if ($filter === 'recommended' && auth()->check() && $petOwner && $hasPets) {
        // Recommended filter - show pets that would work well with user's existing pets
        $userSpecies = $petOwner->pets()->pluck('species')->unique()->toArray();
        
        // Get all unique species user already has
        if (!empty($userSpecies)) {
            // Show pets with different species (to avoid same-species conflicts)
            // OR pets with "Good with other pets" trait
            $adoptionPets = $adoptionPets->where(function($query) use ($userSpecies) {
                $query->whereNotIn('species', $userSpecies)
                      ->orWhereHas('traits', function($q) {
                          $q->whereIn('name', ['Good with other pets', 'Friendly', 'Gentle', 'Calm']);
                      });
            });
        }
    }
    
    $adoptionPets = $adoptionPets->paginate(10);
    return view('adoption', compact('adoptionPets', 'userPets', 'hasPets', 'availableBreeds', 'availableTraits'));
});

// AJAX Pagination Route
Route::get('/adoption/paginate', function (\Illuminate\Http\Request $request) {
    \Illuminate\Support\Facades\Log::info('Filter received: ' . $request->input('filter'));
    
    // Get user's existing pets for filtering recommendations
    $userPets = [];
    $petOwner = null;
    
    if (auth()->check()) {
        $petOwner = auth()->user()->petOwner;
        if ($petOwner) {
            $userPets = $petOwner->pets()->get(['species', 'sex', 'behavior', 'likes', 'dislikes']);
        }
    }
    
    // Apply smart filtering based on existing pets
    $adoptionPets = \App\Models\AdoptionPet::with('traits');
    
    // Handle filter parameter
    $filter = $request->input('filter', 'all');
    
    // Handle species filter
    $species = $request->input('species', 'all');
    
    // Handle gender filter
    $gender = $request->input('gender', 'all');
    
    // Handle age filter
    $age = $request->input('age', 'all');
    
    // Handle breed filter (can be multiple, comma-separated)
    $breeds = $request->input('breeds', 'all');
    
    // Apply species filter
    if ($species === 'Dog') {
        $adoptionPets = $adoptionPets->where('species', 'Dog');
    } elseif ($species === 'Cat') {
        $adoptionPets = $adoptionPets->where('species', 'Cat');
    }
    
    // Apply gender filter
    if ($gender === 'Male') {
        $adoptionPets = $adoptionPets->where('gender', 'Male');
    } elseif ($gender === 'Female') {
        $adoptionPets = $adoptionPets->where('gender', 'Female');
    }
    
    // Apply age filter (using date_of_birth)
    if ($age === '0-6') {
        $adoptionPets = $adoptionPets->whereNotNull('date_of_birth')
                                   ->where('date_of_birth', '>=', now()->subMonths(6));
    } elseif ($age === '6-12') {
        $adoptionPets = $adoptionPets->whereNotNull('date_of_birth')
                                   ->where('date_of_birth', '<', now()->subMonths(6))
                                   ->where('date_of_birth', '>=', now()->subMonths(12));
    } elseif ($age === '1-3') {
        $adoptionPets = $adoptionPets->whereNotNull('date_of_birth')
                                   ->where('date_of_birth', '<', now()->subMonths(12))
                                   ->where('date_of_birth', '>=', now()->subYears(3));
    } elseif ($age === '3+') {
        $adoptionPets = $adoptionPets->whereNotNull('date_of_birth')
                                   ->where('date_of_birth', '<', now()->subYears(3));
    }
    
    // Apply breed filter
    if ($breeds !== 'all' && !empty($breeds)) {
        $breedArray = explode(',', $breeds);
        $adoptionPets = $adoptionPets->whereIn('breed', $breedArray);
    }
    
    // Handle traits filter
    $traits = $request->input('traits', 'all');
    if ($traits !== 'all' && !empty($traits)) {
        $traitArray = explode(',', $traits);
        $adoptionPets = $adoptionPets->whereHas('traits', function($query) use ($traitArray) {
            $query->whereIn('name', $traitArray);
        });
    }
    
    // Handle special filters
    if ($filter === 'recommended' && auth()->check() && $petOwner && $petOwner->pets()->exists()) {
        // Recommended filter - show pets that would work well with user's existing pets
        $userPets = $petOwner->pets()->get(['species', 'behavior', 'likes', 'dislikes']);
        $userSpecies = $userPets->pluck('species')->unique()->toArray();
        
        // Get all behavior traits from user's pets
        $userBehaviors = [];
        foreach ($userPets as $pet) {
            if (is_array($pet->behavior)) {
                $userBehaviors = array_merge($userBehaviors, $pet->behavior);
            }
        }
        $userBehaviors = array_unique($userBehaviors);
        
        // If user has aggressive/shy pets, recommend gentle/calm pets
        // If user has calm/playful pets, recommend playful/friendly pets
        $recommendedTraits = [];
        
        if (in_array('Aggressive', $userBehaviors) || in_array('Dominant', $userBehaviors)) {
            // Recommend gentle, calm, docile pets for aggressive owners
            $recommendedTraits = ['Gentle', 'Calm', 'Docile', 'Quiet'];
        } elseif (in_array('Shy', $userBehaviors) || in_array('Timid', $userBehaviors)) {
            // Recommend gentle pets for shy owners
            $recommendedTraits = ['Gentle', 'Calm', 'Friendly', 'Good with children'];
        } elseif (in_array('Playful', $userBehaviors) || in_array('Energetic', $userBehaviors)) {
            // Recommend playful/friendly pets for playful owners
            $recommendedTraits = ['Playful', 'Friendly', 'Energetic', 'Good with other pets'];
        } else {
            // Default: show different species OR pets with good-with-pets traits
            $recommendedTraits = ['Good with other pets', 'Friendly', 'Gentle', 'Calm'];
        }
        
        // Get all unique species user already has
        if (!empty($userSpecies)) {
            $adoptionPets = $adoptionPets->where(function($query) use ($userSpecies, $recommendedTraits) {
                // Show pets with different species (to avoid same-species conflicts)
                // OR pets with recommended traits
                $query->whereNotIn('species', $userSpecies)
                      ->orWhereHas('traits', function($q) use ($recommendedTraits) {
                          $q->whereIn('name', $recommendedTraits);
                      });
            });
        }
    }
    
    $adoptionPets = $adoptionPets->paginate(10);
    
    // Map pets to include computed age attribute
    $pets = collect($adoptionPets->items())->map(function ($pet) {
        return [
            'id' => $pet->adoption_id,
            'adoption_id' => $pet->adoption_id,
            'pet_name' => $pet->pet_name,
            'species' => $pet->species,
            'gender' => $pet->gender,
            'breed' => $pet->breed,
            'description' => $pet->description,
            'traits' => $pet->traits->pluck('name')->toArray(),
            'weight' => $pet->weight,
            'image' => $pet->image ? asset($pet->image) : null,
            'image_path' => $pet->image,
            'date_of_birth' => $pet->date_of_birth,
            'is_age_estimated' => $pet->is_age_estimated,
            'age' => $pet->age, // This calls the accessor
        ];
    });
    
    return response()->json([
        'pets' => $pets,
        'currentPage' => $adoptionPets->currentPage(),
        'lastPage' => $adoptionPets->lastPage(),
        'hasMorePages' => $adoptionPets->hasMorePages(),
        'total' => $adoptionPets->total()
    ]);
});

// Adoption Form Page Route
Route::get('/adoption/form', function () {
    $user = auth()->user();
    $petOwner = $user ? $user->petOwner : null;
    $traits = \App\Models\AdoptionTrait::orderBy('name')->get();
    return view('adoption_form', compact('user', 'petOwner', 'traits'));
});

// Store Adoption Pet Route
Route::post('/adoption', [\App\Http\Controllers\AdoptionPetController::class, 'store'])->name('adoption.store');

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
