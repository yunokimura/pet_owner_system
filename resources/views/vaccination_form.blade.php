<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Anti-Rabies Vaccination Form - Dasmariñas City Veterinary Services</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Inter', 'sans-serif'],
                    },
                    colors: {
                        primary: {
                            DEFAULT: '#066D33',
                            light: '#077a40',
                            dark: '#055a29',
                        },
                        secondary: {
                            DEFAULT: '#07A13F',
                            light: '#08b148',
                            dark: '#068c35',
                        }
                    }
                }
            }
        }
    </script>
    <style>
        .dropdown-option {
            transition: all 0.2s ease;
        }
        .dropdown-option:hover {
            background-color: #f3f4f6;
        }
        .dropdown-option.selected {
            background-color: #e8f5e9;
        }
    </style>
</head>
<body class="font-sans antialiased bg-gray-50">
    <!-- Header -->
    <header class="bg-white shadow-sm sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center py-4">
                <!-- Government Logo -->
                <div class="flex items-center space-x-3">
                    <div class="w-12 h-12 rounded-full flex items-center justify-center overflow-hidden">
                        <img src="{{ asset('images/dasma logo.png') }}" alt="Dasmariñas City Logo" class="w-full h-full object-contain">
                    </div>
                    <div>
                        <h1 class="text-lg font-bold text-gray-900">Dasmariñas City Veterinary Services</h1>
                        <p class="text-sm text-gray-500">Official Veterinary Office of Dasmariñas City</p>
                    </div>
                </div>
                
                <!-- Navigation -->
                <nav class="hidden md:flex space-x-8">
                    <a href="{{ url('/') }}" class="text-gray-600 hover:text-primary font-medium transition-colors">Home</a>
                    <a href="{{ url('/about-us') }}" class="text-gray-600 hover:text-primary font-medium transition-colors">About Us</a>
                    <a href="{{ url('/services') }}" class="text-gray-600 hover:text-primary font-medium transition-colors">Services</a>
                    <a href="{{ url('/missing-pets') }}" class="text-gray-600 hover:text-primary font-medium transition-colors">Missing Pets</a>
                </nav>
                
                <!-- Login/Register Buttons or User Dropdown -->
                @auth
                    <div class="relative">
                        <button onclick="toggleDropdown()" class="flex items-center space-x-3 focus:outline-none">
                            <div class="w-10 h-10 rounded-full bg-primary flex items-center justify-center">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                </svg>
                            </div>
                            <span class="text-primary font-medium hidden lg:block">{{ Auth::user()->name }}</span>
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-gray-500 hidden lg:block" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                            </svg>
                        </button>
                        
                        <div id="userDropdown" class="hidden absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-lg border border-gray-200 py-2 z-50">
                            <a href="{{ route('owner.dashboard') }}" class="flex items-center px-4 py-2 text-gray-700 hover:bg-gray-50">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 mr-3 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                                </svg>
                                Dashboard
                            </a>
                            <a href="{{ route('profile.edit') }}" class="flex items-center px-4 py-2 text-gray-700 hover:bg-gray-50">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 mr-3 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                </svg>
                                Profile
                            </a>
                            <hr class="my-2 border-gray-200">
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="flex items-center w-full px-4 py-2 text-red-600 hover:bg-red-50">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 mr-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                                    </svg>
                                    Log Out
                                </button>
                            </form>
                        </div>
                    </div>
                @else
                    <div class="flex space-x-4">
                        <a href="{{ route('login') }}" class="px-4 py-2 text-primary font-medium hover:bg-gray-100 rounded-lg transition-colors">Log In</a>
                        <a href="{{ route('register') }}" class="px-4 py-2 bg-primary text-white font-medium rounded-lg hover:bg-primary-light transition-colors">Register</a>
                    </div>
                @endauth
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <!-- Title and Required Fields Notice -->
        <div class="text-center mb-8">
            <h2 class="text-3xl font-bold text-gray-900">Anti-Rabies Vaccination</h2>
            <p class="text-gray-500 text-sm mt-1">Fields marked with <span class="text-red-500">*</span> are required</p>
        </div>

        <!-- Session Status -->
        @if (session('status'))
            <div class="mb-6 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg text-sm">
                {{ session('status') }}
            </div>
        @endif

        <!-- Validation Errors -->
        @if ($errors->any())
            <div class="mb-6 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg text-sm">
                <strong>Please fix the following errors:</strong>
                <ul class="list-disc ml-5 mt-2">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="bg-white border border-gray-200 rounded-lg p-8">
            <!-- Progress Bar -->
            <div class="mb-8">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-sm font-medium text-gray-700" id="stepLabel">Step 1 of 4</span>
                    <span class="text-sm font-medium text-primary" id="stepTitle">Part 1: Owner's Information</span>
                </div>
                <div class="w-full bg-gray-200 rounded-full h-2.5">
                    <div id="progressBar" class="bg-primary h-2.5 rounded-full transition-all duration-300" style="width: 25%"></div>
                </div>
                <div class="flex justify-between mt-2">
                    <div class="text-xs text-center flex-1">
                        <div id="step1Indicator" class="font-semibold text-primary">1</div>
                        <div class="text-gray-500">Owner</div>
                    </div>
                    <div class="text-xs text-center flex-1">
                        <div id="step2Indicator" class="font-semibold text-gray-400">2</div>
                        <div class="text-gray-400">Pet</div>
                    </div>
                    <div class="text-xs text-center flex-1">
                        <div id="step3Indicator" class="font-semibold text-gray-400">3</div>
                        <div class="text-gray-400">Appointment</div>
                    </div>
                    <div class="text-xs text-center flex-1">
                        <div id="step4Indicator" class="font-semibold text-gray-400">4</div>
                        <div class="text-gray-400">Medical History</div>
                    </div>
                </div>
            </div>

            <form id="vaccinationForm" method="POST" action="{{ url('/vaccination/form') }}" enctype="multipart/form-data">
                @csrf

                <!-- PART 1: OWNER'S INFORMATION -->
                <div id="part1" class="form-part">
                    <div class="mb-8">
                        <h3 class="text-lg font-semibold mb-4 pb-2 border-b bg-green-50 px-4 py-2 rounded-lg">Part 1: Owner's Information</h3>

                        <div class="grid md:grid-cols-2 gap-4">
                            <!-- Name -->
                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium mb-1.5">
                                    Pet Owner's Name <span class="text-red-500">*</span>
                                    <span class="text-gray-500 text-xs ml-2">(First name and Last name)</span>
                                </label>
                                <div class="grid grid-cols-2 gap-4">
                                    <input type="text" name="owner_first_name" placeholder="First Name" value="{{ old('owner_first_name', $petOwner->first_name ?? '') }}"
                                           class="w-full px-4 py-2.5 rounded-lg border border-gray-300 focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none">
                                    <input type="text" name="owner_last_name" placeholder="Last Name" value="{{ old('owner_last_name', $petOwner->last_name ?? '') }}"
                                           class="w-full px-4 py-2.5 rounded-lg border border-gray-300 focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none">
                                </div>
                            </div>

                            <!-- Email -->
                            <div>
                                <label class="block text-sm font-medium mb-1.5">
                                    Email <span class="text-red-500">*</span>
                                </label>
                                <input type="email" name="owner_email" placeholder="Enter Email" value="{{ old('owner_email', $user->email ?? '') }}"
                                       class="w-full px-4 py-2.5 rounded-lg border border-gray-300 focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none">
                            </div>

                            <!-- Contact Number -->
                            <div>
                                <label class="block text-sm font-medium mb-1.5">
                                    Contact Number <span class="text-red-500">*</span>
                                </label>
                                <div class="flex">
                                    <span class="inline-flex items-center px-4 py-2.5 rounded-l-lg border border-r-0 border-gray-300 bg-gray-100 text-gray-600 text-sm">
                                        +63
                                    </span>
                                    <input type="tel" name="owner_contact" placeholder="943 210 2012" maxlength="12" value="{{ old('owner_contact', $petOwner->phone_number ?? '') }}"
                                           class="flex-1 px-4 py-2.5 rounded-r-lg border border-gray-300 focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none">
                                </div>
                            </div>

                            <!-- House No. / Unit No. -->
                            <div>
                                <label class="block text-sm font-medium mb-1.5">
                                    House No. / Unit No. <span class="text-red-500">*</span>
                                </label>
                                <input type="text" name="house_no" placeholder="House No. / Unit No." value="{{ old('house_no', $petOwner->house_no ?? '') }}"
                                       class="w-full px-4 py-2.5 rounded-lg border border-gray-300 focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none">
                            </div>

                            <!-- Street -->
                            <div>
                                <label class="block text-sm font-medium mb-1.5">
                                    Street <span class="text-red-500">*</span>
                                </label>
                                <input type="text" name="street" placeholder="Street" value="{{ old('street', $petOwner->street ?? '') }}"
                                       class="w-full px-4 py-2.5 rounded-lg border border-gray-300 focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none">
                            </div>

                            <!-- Barangay -->
                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium mb-1.5">
                                    Barangay <span class="text-red-500">*</span>
                                </label>
                                <input type="text" name="barangay" placeholder="Barangay" value="{{ old('barangay', $petOwner->barangay ?? '') }}"
                                       class="w-full px-4 py-2.5 rounded-lg border border-gray-300 focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none">
                            </div>
                        </div>
                    </div>
                    
                    <!-- Navigation Buttons for Part 1 -->
                    <div class="flex justify-end mt-8">
                        <button type="button" onclick="goToStep(2)" class="bg-primary text-white px-8 py-3 rounded-lg font-semibold hover:bg-primary-light transition-colors flex items-center">
                            Next: Pet's Information
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 ml-2" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
                            </svg>
                        </button>
                    </div>
                </div>

                <!-- PART 2: PET'S INFORMATION -->
                <div id="part2" class="form-part hidden">
                    <h3 class="text-lg font-semibold mb-4 pb-2 border-b bg-green-50 px-4 py-2 rounded-lg">Part 2: Pet's Information</h3>

                    <!-- Pet's Name -->
                    <div class="mb-6">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Pet's Name <span class="text-red-500">*</span></label>
                        <input type="text" name="pet_name" value="{{ old('pet_name') }}" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary" placeholder="Enter your pet's name">
                    </div>

                    <!-- Species and Gender -->
                    <div class="mb-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Species <span class="text-red-500">*</span></label>
                                <select name="pet_species" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary" onchange="updateBreedOptions()">
                                    <option value="">Select species</option>
                                    <option value="dog">Dog</option>
                                    <option value="cat">Cat</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Gender <span class="text-red-500">*</span></label>
                                <select name="pet_gender" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary">
                                    <option value="">Select gender</option>
                                    <option value="male">Male</option>
                                    <option value="female">Female</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- Breed -->
                    <div class="mb-6" id="breedSection">
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Breed <span class="text-red-500">*</span>
                        </label>
                        
                        <!-- Dropdown trigger and container -->
                        <div class="relative">
                            <!-- Display selected breed (click to open) -->
                            <div id="breedDisplay" class="w-full px-4 py-3 border border-gray-300 rounded-lg bg-white cursor-pointer hover:border-gray-400" onclick="toggleBreedDropdown()">
                                <span id="selectedBreedText" class="text-gray-500">Select a breed</span>
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-gray-400 absolute right-3 top-1/2 transform -translate-y-1/2" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                                </svg>
                            </div>
                            
                            <!-- Dropdown menu -->
                            <div id="breedDropdown" class="hidden absolute z-10 w-full mt-1 border border-gray-300 rounded-lg bg-white shadow-lg max-h-64">
                                <!-- Search inside dropdown -->
                                <div class="p-2 border-b border-gray-200">
                                    <input type="text" id="breedSearch" placeholder="Search breed..." class="w-full px-3 py-2 border border-gray-300 rounded text-sm focus:outline-none focus:border-primary" onkeyup="filterBreeds()" onclick="event.stopPropagation()">
                                </div>
                                
                                <!-- Options -->
                                <div id="breedOptions" class="max-h-48 overflow-y-auto py-1">
                                    <!-- Options populated by JS -->
                                </div>
                            </div>
                        </div>
                        
                        <!-- Hidden input to store selected breed -->
                        <input type="hidden" id="selectedBreed" name="pet_breed" value="">
                    </div>

                    <!-- Age -->
                    <div class="mb-6">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Age <span class="text-red-500">*</span></label>
                        <select name="pet_age" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary">
                            <option value="">Select estimated age</option>
                            <option value="less_than_3_months">Less than 3 months old</option>
                            <option value="3_to_12_months">3 to 12 months old</option>
                            <option value="1_year">1 year old</option>
                            <option value="2_years">2 years old</option>
                            <option value="3_years">3 years old</option>
                            <option value="4_years">4 years old</option>
                            <option value="5_years">5 years old</option>
                            <option value="6_years">6 years old</option>
                            <option value="7_years">7 years old</option>
                            <option value="8_years">8 years old</option>
                            <option value="9_years">9 years old</option>
                            <option value="10_years">10 years old</option>
                            <option value="11_years">11 years old</option>
                            <option value="12_years">12 years old</option>
                            <option value="13_years">13 years old</option>
                            <option value="14_years">14 years old</option>
                            <option value="15_years">15 years old</option>
                            <option value="16_years">16 years old</option>
                            <option value="17_years">17 years old</option>
                            <option value="18_years">18 years old</option>
                            <option value="19_years">19 years old</option>
                            <option value="20_years">20 years old</option>
                        </select>
                    </div>

                    <!-- Navigation Buttons for Part 2 -->
                    <div class="flex justify-between mt-8">
                        <button type="button" onclick="goToStep(1)" class="bg-gray-200 text-gray-700 px-8 py-3 rounded-lg font-semibold hover:bg-gray-300 transition-colors flex items-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
                            </svg>
                            Previous
                        </button>
                        <button type="button" onclick="goToStep(3)" class="bg-primary text-white px-8 py-3 rounded-lg font-semibold hover:bg-primary-light transition-colors flex items-center">
                            Next: Appointment Date
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 ml-2" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
                            </svg>
                        </button>
                    </div>
                </div>

                <!-- PART 3: APPOINTMENT DATE -->
                <div id="part3" class="form-part hidden">
                    <h3 class="text-lg font-semibold mb-4 pb-2 border-b bg-green-50 px-4 py-2 rounded-lg">Part 3: Appointment Date</h3>

                    <!-- Appointment Date -->
                    <div class="mb-6">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Appointment Date <span class="text-red-500">*</span></label>
                        <input type="date" name="appointment_date" value="{{ old('appointment_date') }}" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary">
                    </div>

                    <!-- Navigation Buttons for Part 3 -->
                    <div class="flex justify-between mt-8">
                        <button type="button" onclick="goToStep(2)" class="bg-gray-200 text-gray-700 px-8 py-3 rounded-lg font-semibold hover:bg-gray-300 transition-colors flex items-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
                            </svg>
                            Previous
                        </button>
                        <button type="button" onclick="goToStep(4)" class="bg-primary text-white px-8 py-3 rounded-lg font-semibold hover:bg-primary-light transition-colors flex items-center">
                            Next: Medical History
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 ml-2" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
                            </svg>
                        </button>
                    </div>
                </div>

                <!-- PART 4: MEDICAL HISTORY -->
                <div id="part4" class="form-part hidden">
                    <h3 class="text-lg font-semibold mb-4 pb-2 border-b bg-green-50 px-4 py-2 rounded-lg">Part 4: Medical History</h3>

                    <!-- Date of Last Anti-Rabies -->
                    <div class="mb-6">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Date of Last Anti-Rabies</label>
                        <input type="date" name="last_anti_rabies_date" value="{{ old('last_anti_rabies_date') }}" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary">
                    </div>

                    <!-- Surgery Question -->
                    <div class="mb-6">
                        <label class="block text-sm font-medium text-gray-700 mb-3">Did your pet undergo any surgery in the last two (2) weeks? <span class="text-red-500">*</span></label>
                        <div class="flex space-x-6">
                            <label class="inline-flex items-center cursor-pointer">
                                <input type="radio" name="recent_surgery" value="yes" class="form-radio h-5 w-5 text-primary">
                                <span class="ml-3 text-gray-700 font-medium">Yes</span>
                            </label>
                            <label class="inline-flex items-center cursor-pointer">
                                <input type="radio" name="recent_surgery" value="no" class="form-radio h-5 w-5 text-primary">
                                <span class="ml-3 text-gray-700 font-medium">No</span>
                            </label>
                        </div>
                    </div>

                    <!-- Confirmation Checkbox -->
                    <div class="mb-8">
                        <label class="inline-flex items-start cursor-pointer">
                            <input type="checkbox" name="confirmation" class="form-checkbox h-5 w-5 text-primary rounded mt-1">
                            <span class="ml-3 text-gray-700 text-sm">
                                By confirming, you acknowledge that the provided information is accurate and that your pet will be available for vaccination on the selected date. <span class="text-red-500">*</span>
                            </span>
                        </label>
                    </div>

                    <!-- Navigation Buttons for Part 4 -->
                    <div class="flex justify-between mt-8">
                        <button type="button" onclick="goToStep(3)" class="bg-gray-200 text-gray-700 px-8 py-3 rounded-lg font-semibold hover:bg-gray-300 transition-colors flex items-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
                            </svg>
                            Previous
                        </button>
                        <button type="submit" class="bg-primary text-white px-12 py-3 rounded-lg font-semibold hover:bg-primary-light transition-colors flex items-center">
                            Submit
                        </button>
                    </div>
                </div>
            </form>

            <!-- Back Link -->
            <div class="text-center mt-6">
                <a href="{{ url('/vaccination') }}" class="inline-block text-gray-600 hover:text-primary transition-colors">
                    ← Back to Vaccination
                </a>
            </div>
        </div>
    </main>

    <!-- Footer -->
    <footer class="bg-gray-800 text-white py-8 mt-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex flex-col md:flex-row justify-between items-center">
                <div class="mb-4 md:mb-0">
                    <p class="text-sm">&copy; 2026 Dasmariñas City Veterinary Services. All rights reserved.</p>
                </div>
                <div class="flex space-x-6">
                    <a href="#" class="text-gray-400 hover:text-white transition-colors">Privacy Policy</a>
                    <a href="#" class="text-gray-400 hover:text-white transition-colors">Terms of Service</a>
                    <a href="#" class="text-gray-400 hover:text-white transition-colors">Contact Us</a>
                </div>
            </div>
        </div>
    </footer>

    <script>
        // Multi-step form navigation
        function goToStep(step) {
            // Hide all parts
            document.querySelectorAll('.form-part').forEach(part => {
                part.classList.add('hidden');
            });
            
            // Show the selected part
            document.getElementById('part' + step).classList.remove('hidden');
            
            // Update progress bar
            const progressBar = document.getElementById('progressBar');
            const stepLabel = document.getElementById('stepLabel');
            const stepTitle = document.getElementById('stepTitle');
            
            progressBar.style.width = (step * 25) + '%';
            stepLabel.textContent = 'Step ' + step + ' of 4';
            
            const titles = {
                1: 'Part 1: Owner\'s Information',
                2: 'Part 2: Pet\'s Information',
                3: 'Part 3: Appointment Date',
                4: 'Part 4: Medical History'
            };
            stepTitle.textContent = titles[step];
            
            // Update step indicators
            for (let i = 1; i <= 4; i++) {
                const indicator = document.getElementById('step' + i + 'Indicator');
                if (i <= step) {
                    indicator.classList.remove('text-gray-400');
                    indicator.classList.add('text-primary');
                } else {
                    indicator.classList.remove('text-primary');
                    indicator.classList.add('text-gray-400');
                }
            }
            
            // Scroll to top of form
            document.querySelector('.form-part:not(.hidden)').scrollIntoView({ behavior: 'smooth' });
        }

        // Cat breeds list
        const catBreeds = [
            "Mixed Breed (Puspin)", "Abyssinian", "Aegean", "American Bobtail", "American Curl", 
            "American Shorthair", "American Wirehair", "Arabian Mau", "Australian Mist", "Balinese", 
            "Bambino", "Bengal", "Birman", "Bombay", "British Longhair", "British Shorthair", 
            "Burmese", "Burmilla", "California Spangled", "Chantilly-Tiffany", "Chartreux", 
            "Chausie", "Cheetoh", "Colorpoint Shorthair", "Cornish Rex", "Cymric", "Devon Rex", 
            "Donskoy", "Egyptian Mau", "Exotic Shorthair", "Havana Brown", "Highlander", 
            "Himalayan", "Japanese Bobtail", "Javanese", "Khao Manee", "Korat", "Kurilian Bobtail", 
            "LaPerm", "Lykoi", "Maine Coon", "Manx", "Mexican Hairless", "Minskin", "Minuet", 
            "Munchkin", "Nebelung", "Norwegian Forest Cat", "Ocicat", "Ojos Azules", 
            "Oriental Shorthair", "Persian", "Peterbald", "Pixiebob", "Ragamuffin", "Ragdoll", 
            "Russian Blue", "Savannah", "Scottish Fold", "Scottish Straight", "Selkirk Rex", 
            "Serengeti", "Siamese", "Siberian", "Singapura", "Snowshoe", "Somali", "Sphynx", 
            "Thai", "Tonkinese", "Toybob", "Toyger", "Turkish Angora", "Turkish Van", 
            "York Chocolate", "Unknown"
        ];

        // Dog breeds list
        const dogBreeds = [
            "Mixed Breed (Aspin)", "Akita", "Alaskan Klee Kai", "Alaskan Malamute", "American Eskimo", 
            "Appenzeller Sennenhund", "Australian Stumpy Tail Cattle Dog", "Azawakh", "Barbado da Terciera", "Barbet", "Basenji",
            "Basset Fauve de Bretagne", "Basset Hound", "Beagle", "Belgian Laekenois", "Belgian Tervuren", 
            "Berger Picard", "Bichon Frise", "Bloodhound", "Boerboel", "Bolognese", "Borzoi", "Boxer", 
            "Bracco Italiano", "Braque Francais Pyrenean", "Braques du Bourbonnais", "Broholmer", 
            "Brussels Griffon", "Bull Terrier", "Boston Bull Terrier", "Staffordshire Bull Terrier", 
            "Miniature Bull Terrier", "Bulldog", "French Bulldog", "Olde English Bulldogge", 
            "American Bulldog", "Continental Bulldog", "Ca de Bou", "Serrano Bulldog", "Campeiro Bulldog", 
            "Alano Español", "Canaan Dog", "Canadian Eskimo Dog", "Cane Corso", "Carolina Dog", 
            "Catahoula Leopard", "Cesky Fousek", "Chihuahua", "Chinese Crested", "Chinese Shar-Pei", 
            "Chinook", "Chow Chow", "Cirneco dell'Etna", "American English Coonhound", 
            "Black and Tan Coonhound", "Bluetick Coonhound", "Redbone Coonhound", "Treeing Walker Coonhound", 
            "Plott Hound", "Cardigan Welsh Corgi", "Pembroke Welsh Corgi", "Coton de Tulear", 
            "Czechoslovakian Vlcak", "Dachshund", "Dalmatian", "Danish-Swedish Farmdog", 
            "Deutscher Watchtelhund", "Dogo Argentino", "Dogue de Bordeaux", "Drever", "Dutch Partridge", 
            "Dutch Smoushond", "East Siberian Laika", "Eurasier", "Finnish Spitz", "American Foxhound", 
            "English Foxhound", "German Spitz", "Grand Basset Griffon Vendeen", "Grand Bleu de Gascogne", 
            "Great Dane", "Great Pyrenees", "Greenland Dog", "Hanoverian Scenthound", "Harrier", 
            "Havanese", "Hokkaido", "Afghan Hound", "American Leopard Hound", 
            "Bavarian Mountain Scent Hound", "Caravan (Mudhol) Hound", "Finnish Hound", 
            "French White & Black Hound", "German Hound", "Hamilton Hound", "Ibizan Hound", 
            "Pharaoh Hound", "Transylvanian Hound", "Hovawart", "Irish Wolfhound", "Italian Greyhound", 
            "Japanese Akitainu", "Japanese Chin", "Japanese Spitz", "Jindo", "Kai Ken", 
            "Karelian Bear Dog", "Keeshond", "Kishu Ken", "Komondor", "Kromfohrlander", "Kuvasz", 
            "Lagotto Romagnolo", "Lancashire Heeler", "Lapponian Herder", "Leonberger", "Lhasa Apso", 
            "Lowchen", "Maltese", "Standard Manchester Terrier", "Toy Manchester Terrier", "Mastiff", 
            "Bullmastiff", "Brazilian Mastiff", "Neapolitan Mastiff", "Pyrenean Mastiff", 
            "Spanish Mastiff", "Tibetan Mastiff", "Moscow Watchdog", "Mountain Cur", "Bernese Mountain Dog", 
            "Entlebucher Mountain Dog", "Estrela Mountain Dog", "Greater Swiss Mountain Dog", "Mudi", 
            "Newfoundland", "Norrbottenspets", "Norwegian Buhund", "Norwegian Elkhound", 
            "Norwegian Lundehund", "Otterhound", "Pekingese", "Perro de Presa Canario", "Peruvian Inca Orchid", 
            "Petit Basset Griffon Vendeen", "Affenpinscher Pinscher", "Doberman Pinscher", 
            "German Pinscher", "Miniature Pinscher", "German Longhaired Pointer", "German Shorthaired Pointer", 
            "German Wirehaired Pointer", "Portuguese Pointer", "Slovakian Wirehaired Pointer", 
            "Chart Polski Polish Greyhound", "Pomeranian", "Miniature Poodle", "Standard Poodle", 
            "Toy Poodle", "Porcelaine", "Portuguese Podengo", "Portuguese Podengo Pequeno", 
            "Pudelpointer", "Pug", "Puli", "Pumi", "Rafeiro do Alentejo", "Chesapeake Bay Retriever", 
            "Curly-coated Retriever", "Flat-Coated Retriever", "Golden Retriever", "Labrador Retriever", 
            "Nova Scotia Duck Tolling Retriever", "Rhodesian Ridgeback", "Rottweiler", "Russian Toy", 
            "Russian Tsvetnaya Bolonka", "Saint Bernard", "Saluki", "Samoyed", "Schapendoes", 
            "Schipperke", "Giant Schnauzer", "Miniature Schnauzer", "Standard Schnauzer", 
            "Scottish Deerhound", "Sealyham Terrier", "Segugio Italiano", "English Setter", 
            "Gordon Setter", "Irish Red and White Setter", "Irish Setter", "Anatolian Shepherd", 
            "Australian Cattle Dog", "American Miniature Shepherd", "Australian Kelpie Shepherd", 
            "Australian Shepherd", "Bearded Collie", "Beauceron", "Belgian Malinois", 
            "Belgian Sheepdog (Groenendael)", "Briard", "Border Collie", "Bouvier des Flandres", 
            "Collie", "Finnish Lapphund", "Miniature American Shepherd", "Bergamasco Shepherd", 
            "Bohemian Shepherd", "Catalan Shepherd", "Caucasian Shepherd", "Central Asian Shepherd", 
            "Croatian Shepherd", "Dutch Shepherd", "English Shepherd", "German Shepherd", 
            "Icelandic Shepherd", "Karst Shepherd", "Old English Shepherd", "Polish Lowland Shepherd", 
            "Portuguese Shepherd", "Pyrenean Shepherd", "Romanian Carpathian Shepherd Dog", 
            "Romanian Mioritic Shepherd Dog", "Shetland Sheepdog", "Shiba Inu", "Shih Tzu", "Shikoku", 
            "Siberian Husky", "Sloughi", "Slovensky Cubac", "Slovensky Kopov", "Small Munsterlander", 
            "American Water Spaniel", "Boykin Spaniel", "Brittany", "Cavalier King Charles Spaniel", 
            "Clumber Spaniel", "Cocker Spaniel", "English Cocker Spaniel", "English Springer Spaniel", 
            "English Toy Spaniel", "Field Spaniel", "French Spaniel", "Irish Water Spaniel", 
            "Nederlandse Kooikerhondje", "Papillon Spaniel", "Sussex Spaniel", "Tibetan Spaniel", 
            "Welsh Springer Spaniel", "Spinone Italiano", "Stabyhoun", "Swedish Vallhund", "Taiwan Dog", 
            "Airedale Terrier", "American Hairless Terrier", "American Staffordshire Terrier", 
            "Australian Terrier", "Bedlington Terrier", "Border Terrier", "Biewer Terrier", 
            "Black Russian Terrier", "Cairn Terrier", "Cesky Terrier", "Dandie Dinmont Terrier", 
            "Glen of Imaal Terrier", "Irish Terrier", "Jagdterrier", "Japanese Terrier", 
            "Kerry Blue Terrier", "Lakeland Terrier", "Norfolk Terrier", "Norwich Terrier", 
            "Parson Russell Terrier", "Rat Terrier", "Jack Russell Terrier", "Scottish Terrier", 
            "Silky Terrier", "Skye Terrier", "Smooth Fox Terrier", "Soft Coated Wheaten Terrier", 
            "Teddy Roosevelt Terrier", "Tibetan Terrier", "Toy Fox Terrier", "Welsh Terrier", 
            "West Highland White Terrier", "Wire Fox Terrier", "Thai Ridgeback", "Tornjak", "Tosa", 
            "Treeing Tennessee Brindle", "Vizsla", "Volpino Italiano", "Portuguese Water Dog", 
            "Spanish Water Dog", "Weimaraner", "Wetterhoun", "Whippet", "Wirehaired Pointing Griffon", 
            "Wirehaired Vizsla", "Working Kelpie", "Xoloitzcuintli", "Yakutian Laika", "Yorkshire Terrier", 
            "Unknown"
        ];

        let selectedBreed = '';

        // Toggle breed dropdown
        function toggleBreedDropdown() {
            const dropdown = document.getElementById('breedDropdown');
            dropdown.classList.toggle('hidden');
            
            // Populate options if not already done
            if (!dropdown.classList.contains('hidden')) {
                updateBreedOptions();
            }
        }

        // Close dropdown when clicking outside
        document.addEventListener('click', function(e) {
            const dropdown = document.getElementById('breedDropdown');
            const display = document.getElementById('breedDisplay');
            if (dropdown && display && !dropdown.contains(e.target) && !display.contains(e.target)) {
                dropdown.classList.add('hidden');
            }
        });

        // Update breed options based on species
        function updateBreedOptions() {
            const speciesSelect = document.querySelector('select[name="pet_species"]');
            const currentSpecies = speciesSelect ? speciesSelect.value : '';
            const breedOptions = document.getElementById('breedOptions');
            
            let breeds = [];
            if (currentSpecies === 'cat') {
                breeds = catBreeds;
            } else if (currentSpecies === 'dog') {
                breeds = dogBreeds;
            } else {
                breeds = [...dogBreeds, ...catBreeds];
            }
            
            // Sort breeds alphabetically
            breeds.sort();
            
            // Generate HTML for breed options
            breedOptions.innerHTML = breeds.map(breed => 
                `<div class="dropdown-option px-4 py-2 cursor-pointer hover:bg-gray-100 ${selectedBreed === breed ? 'selected bg-green-50' : ''}" 
                     onclick="selectBreed('${breed.replace(/'/g, "\\'")}')">
                    ${breed}
                </div>`
            ).join('');
        }

        // Select a breed
        function selectBreed(breed) {
            selectedBreed = breed;
            document.getElementById('selectedBreedText').textContent = breed;
            document.getElementById('selectedBreed').value = breed;
            document.getElementById('breedDropdown').classList.add('hidden');
            updateBreedOptions(); // Update visual selection
        }

        // Filter breeds based on search
        function filterBreeds() {
            const searchInput = document.getElementById('breedSearch');
            const filter = searchInput.value.toLowerCase();
            const breedOptions = document.getElementById('breedOptions');
            const options = breedOptions.getElementsByClassName('dropdown-option');
            
            for (let i = 0; i < options.length; i++) {
                const text = options[i].textContent || options[i].innerText;
                if (text.toLowerCase().indexOf(filter) > -1) {
                    options[i].style.display = "";
                } else {
                    options[i].style.display = "none";
                }
            }
        }

        // Dropdown toggle for user menu
        function toggleDropdown() {
            const dropdown = document.getElementById('userDropdown');
            dropdown.classList.toggle('hidden');
        }

        // Close dropdown when clicking outside
        document.addEventListener('click', function(e) {
            const dropdown = document.getElementById('userDropdown');
            const button = document.querySelector('button[onclick="toggleDropdown()"]');
            if (dropdown && button && !dropdown.contains(e.target) && !button.contains(e.target)) {
                dropdown.classList.add('hidden');
            }
        });
    </script>
</body>
</html>
