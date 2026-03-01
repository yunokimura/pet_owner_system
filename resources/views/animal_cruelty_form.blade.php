<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Animal Cruelty Report - Dasmariñas City Veterinary Services</title>
    
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
                        }
                    }
                }
            }
        }
    </script>
</head>

<body class="font-sans bg-gray-50 min-h-screen">

<!-- Header -->
<header class="bg-white border-b border-gray-200">
    <div class="max-w-6xl mx-auto px-6 py-4 flex items-center space-x-4">
        <img src="{{ asset('images/dasma logo.png') }}" class="w-12 h-12 object-contain" alt="Logo">
        <div>
            <h1 class="text-lg font-bold">Dasmariñas City Veterinary Services</h1>
            <p class="text-sm text-gray-600">Official Veterinary Office of Dasmariñas City</p>
        </div>
    </div>
</header>

<!-- Main -->
<main class="py-10">
    <div class="max-w-4xl mx-auto px-6">

        <!-- Title -->
        <div class="text-center mb-8">
            <h2 class="text-3xl font-bold">Animal Cruelty Report</h2>
            <p class="mt-2 text-gray-600 text-sm text-left">
                Fields marked with <span class="text-red-500">*</span> are required
            </p>
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

        <!-- Form Card -->
        <div class="bg-white border border-gray-200 rounded-lg p-8">
            <form method="POST" action="#" enctype="multipart/form-data">
                @csrf

                <!-- A. REPORTER'S INFO -->
                <div class="mb-8">
                    <h3 class="text-lg font-semibold mb-4 pb-2 border-b">A. REPORTER'S INFO</h3>

                    <div class="grid md:grid-cols-2 gap-4">
                        <!-- Name -->
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium mb-1.5">
                                Your Name <span class="text-red-500">*</span>
                                <span class="text-gray-500 text-xs ml-2">(First name and Last name)</span>
                            </label>
                            <div class="grid grid-cols-2 gap-4">
                                <input type="text" name="reporter_first_name" placeholder="First Name"
                                       class="w-full px-4 py-2.5 rounded-lg border border-gray-300 focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none">
                                <input type="text" name="reporter_last_name" placeholder="Last Name"
                                       class="w-full px-4 py-2.5 rounded-lg border border-gray-300 focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none">
                            </div>
                        </div>

                        <!-- Email -->
                        <div>
                            <label class="block text-sm font-medium mb-1.5">
                                Email <span class="text-red-500">*</span>
                            </label>
                            <input type="email" name="reporter_email" placeholder="Enter Email"
                                   class="w-full px-4 py-2.5 rounded-lg border border-gray-300 focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none">
                        </div>

                        <!-- Phone -->
                        <div>
                            <label class="block text-sm font-medium mb-1.5">
                                Phone <span class="text-red-500">*</span>
                            </label>
                            <div class="flex">
                                <span class="inline-flex items-center px-4 py-2.5 rounded-l-lg border border-r-0 border-gray-300 bg-gray-100 text-gray-600 text-sm">
                                    +63
                                </span>
                                <input type="tel" name="reporter_phone" placeholder="943 210 2012" maxlength="12"
                                       class="flex-1 px-4 py-2.5 rounded-r-lg border border-gray-300 focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- B. ACCUSED'S INFO -->
                <div class="mb-8">
                    <h3 class="text-lg font-semibold mb-4 pb-2 border-b">B. ACCUSED'S INFO</h3>

                    <div class="grid md:grid-cols-2 gap-4">
                        <!-- Name of the Accused -->
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium mb-1.5">
                                Name of the Accused <span class="text-red-500">*</span>
                                <span class="text-gray-500 text-xs ml-2">(First name and Last name)</span>
                            </label>
                            <div class="grid grid-cols-2 gap-4">
                                <input type="text" name="accused_first_name" placeholder="First Name"
                                       class="w-full px-4 py-2.5 rounded-lg border border-gray-300 focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none">
                                <input type="text" name="accused_last_name" placeholder="Last Name"
                                       class="w-full px-4 py-2.5 rounded-lg border border-gray-300 focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none">
                            </div>
                            <p class="text-xs text-gray-500 mt-1">We cannot file a case without the name of the accused</p>
                        </div>

                        <!-- Complete Address of the Accused -->
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium mb-1.5">
                                Complete Address of the Accused <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="accused_address" placeholder="Complete Address"
                                   class="w-full px-4 py-2.5 rounded-lg border border-gray-300 focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none">
                            <p class="text-xs text-gray-500 mt-1">We cannot proceed to file a case without the complete address of the accused</p>
                        </div>
                    </div>
                </div>

                <!-- C. COURT COMMITMENT -->
                <div class="mb-8">
                    <h3 class="text-lg font-semibold mb-4 pb-2 border-b">C. COURT COMMITMENT</h3>

                    <div class="space-y-6">
                        <!-- Are you prepared to testify in court? -->
                        <div>
                            <label class="block text-sm font-medium mb-2">
                                Are you prepared to testify in court? <span class="text-red-500">*</span>
                            </label>
                            <p class="text-xs text-gray-500 mb-2">Handa ka bang tumestigo sa korte?</p>
                            <div class="flex gap-4">
                                <label class="inline-flex items-center">
                                    <input type="radio" name="testify_in_court" value="yes" class="text-primary">
                                    <span class="ml-2">Yes</span>
                                </label>
                                <label class="inline-flex items-center">
                                    <input type="radio" name="testify_in_court" value="no" class="text-primary">
                                    <span class="ml-2">No</span>
                                </label>
                            </div>
                        </div>

                        <!-- Are you prepared to attend all the hearing and to pursue the case until a verdict is reached? -->
                        <div>
                            <label class="block text-sm font-medium mb-2">
                                Are you prepared to attend all the hearing and to pursue the case until a verdict is reached? <span class="text-red-500">*</span>
                            </label>
                            <p class="text-xs text-gray-500 mb-2">Handa ka bang dumalo sa lahat ng mga pagdinig at ituloy ang kaso hanggang sa maabot ang hatol?</p>
                            <div class="flex gap-4">
                                <label class="inline-flex items-center">
                                    <input type="radio" name="attend_hearings" value="yes" class="text-primary">
                                    <span class="ml-2">Yes</span>
                                </label>
                                <label class="inline-flex items-center">
                                    <input type="radio" name="attend_hearings" value="no" class="text-primary">
                                    <span class="ml-2">No</span>
                                </label>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- D. EVIDENCE -->
                <div class="mb-8">
                    <h3 class="text-lg font-semibold mb-4 pb-2 border-b">D. EVIDENCE</h3>

                    <div class="grid md:grid-cols-2 gap-4">
                        <!-- Upload Witness Affidavit -->
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium mb-1.5">
                                Please upload your Witness Affidavit
                            </label>
                            <div class="border-2 border-dashed border-gray-300 rounded-lg p-6 text-center">
                                <input type="file" name="witness_affidavit[]" id="witness_affidavit" multiple accept=".pdf,.doc,.docx,.jpg,.jpeg,.png"
                                       class="hidden">
                                <label for="witness_affidavit" class="cursor-pointer">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-12 h-12 text-gray-400 mx-auto mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                                    </svg>
                                    <p class="text-gray-600">Click to upload files</p>
                                </label>
                            </div>
                            <p class="text-xs text-gray-500 italic mt-1">Max. file size: 8 MB, Max. files: 5</p>
                        </div>

                        <!-- Upload Photos of the Animal -->
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium mb-1.5">
                                Please upload photos of the animal
                            </label>
                            <p class="text-xs text-gray-500 mb-2">Videos may be requested at a later time</p>
                            <div class="border-2 border-dashed border-gray-300 rounded-lg p-6 text-center">
                                <input type="file" name="animal_photos[]" id="animal_photos" multiple accept="image/*"
                                       class="hidden">
                                <label for="animal_photos" class="cursor-pointer">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-12 h-12 text-gray-400 mx-auto mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                    <p class="text-gray-600">Click to upload photos</p>
                                </label>
                            </div>
                            <p class="text-xs text-gray-500 italic mt-1">Max. file size: 8 MB, Max. files: 10</p>
                        </div>
                    </div>
                </div>

                <!-- E. COMPLAINT DETAILS -->
                <div class="mb-8">
                    <h3 class="text-lg font-semibold mb-4 pb-2 border-b">E. COMPLAINT DETAILS</h3>

                    <div class="grid md:grid-cols-2 gap-4">
                        <!-- Details of your complaint / Narration of events -->
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium mb-1.5">
                                Details of your complaint / Narration of events <span class="text-red-500">*</span>
                            </label>
                            <textarea name="complaint_details" rows="8"
                                      class="w-full px-4 py-2.5 rounded-lg border border-gray-300 focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none resize-y"
                                      placeholder="Please provide a detailed description of the incident..."></textarea>
                        </div>
                    </div>
                </div>

                <!-- Submit Button -->
                <div class="flex justify-end">
                    <button type="submit" class="bg-primary text-white px-8 py-3 rounded-lg font-semibold hover:bg-primary-light transition-colors shadow-lg hover:shadow-xl">
                        Submit Report
                    </button>
                </div>
            </form>
        </div>
    </div>
</main>

</body>
</html>
