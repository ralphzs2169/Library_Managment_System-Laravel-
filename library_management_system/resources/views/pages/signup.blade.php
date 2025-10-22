<x-layout :title="'Signup'">
    <div class="min-h-screen flex items-center py-10 justify-center bg-background">
        <div class="bg-white p-10 rounded-2xl shadow-lg w-full max-w-2xl">
            <div class="flex flex-col items-center mb-4">
                <img src="{{ asset('build/assets/images/logo-white.png') }}" width=50 height=50 alt="Logo" class="">
                <h2 class="text-3xl font-bold mb-5 text-center">Sign Up</h2>
            </div>

            <form id="signup-form" class="space-y-4" novalidate>

                <!-- Personal Information Section -->
                <div class="form-section">
                    <label class="block text-sm font-medium text-gray-700 ">Personal Information</label>
                    <div class="grid gap-2 section-grid" style="grid-template-columns: 1fr 1fr 0.5fr;">
                        <!-- First Name -->
                        <div class="field-container">
                            <div class="error-placeholder" id="firstname-error-placeholder"></div>
                            <input type="text" id="firstname" name="firstname" placeholder="First Name" class="mt-1 block text-sm w-full bg-[#F2F2F2] font-extralight px-4 py-3 border border-[#B1B1B1] rounded-lg
                                            focus:outline-none focus:ring-2 focus:ring-accent focus:border-transparent">
                        </div>
                        <!-- Last Name -->
                        <div class="field-container">
                            <div class="error-placeholder" id="lastname-error-placeholder"></div>
                            <input type="text" id="lastname" name="lastname" placeholder="Last Name" class="mt-1 block text-sm w-full bg-[#F2F2F2] font-extralight px-4 py-3 border border-[#B1B1B1] rounded-lg
                                            focus:outline-none focus:ring-2 focus:ring-accent focus:border-transparent">
                        </div>
                        <!-- Middle Initial -->
                        <div class="field-container">
                            <div class="error-placeholder" id="middle_initial-error-placeholder"></div>
                            <input type="text" id="middle_initial" name="middle_initial" maxlength="1" placeholder="M.I." class="mt-1 block text-sm w-full bg-[#F2F2F2] font-extralight px-4 py-3 border border-[#B1B1B1] rounded-lg
                                            focus:outline-none focus:ring-2 focus:ring-accent focus:border-transparent">
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-2 section-grid">
                        <!-- Email Address -->
                        <div class="field-container">
                            <div class="error-placeholder" id="email-error-placeholder"></div>
                            <input type="email" id="email" name="email" placeholder="Email" class="mt-1 block text-sm w-full bg-[#F2F2F2] font-extralight px-4 py-3 border border-[#B1B1B1] rounded-lg
                                            focus:outline-none focus:ring-2 focus:ring-accent focus:border-transparent">
                        </div>
                        <!-- Contact Number -->
                        <div class="field-container">
                            <div class="error-placeholder" id="contact_number-error-placeholder"></div>
                            <input type="text" id="contact_number" name="contact_number" placeholder="Contact Number" class="mt-1 block text-sm w-full bg-[#F2F2F2] font-extralight px-4 py-3 border border-[#B1B1B1] rounded-lg
                                            focus:outline-none focus:ring-2 focus:ring-accent focus:border-transparent">
                        </div>
                    </div>
                </div>

                <!-- Account Information Section -->
                <div class="form-section">
                    <label class="block text-sm font-medium text-gray-700 ">Account Information</label>
                    <!-- Username -->
                    <div class="field-container">
                        <div class="error-placeholder" id="username-error-placeholder"></div>
                        <input type="text" id="username" name="username" placeholder="Username" class="mt-1 block text-sm w-full bg-[#F2F2F2] font-extralight px-4 py-3 border border-[#B1B1B1] rounded-lg
                                            focus:outline-none focus:ring-2 focus:ring-accent focus:border-transparent">
                    </div>

                    <div class="grid grid-cols-2 gap-2 section-grid">
                        <!-- Password -->
                        <div class="field-container">
                            <div class="error-placeholder" id="password-error-placeholder"></div>
                            <input type="password" id="password" name="password" placeholder="Password" class="mt-1 block text-sm w-full bg-[#F2F2F2] font-extralight px-4 py-3 border border-[#B1B1B1] rounded-lg
                                            focus:outline-none focus:ring-2 focus:ring-accent focus:border-transparent">
                        </div>
                        <!-- Confirm Password-->
                        <div class="field-container">
                            <div class="error-placeholder" id="confirm_password-error-placeholder"></div>
                            <input type="password" id="confirm_password" name="confirm_password" placeholder="Confirm Password" class="mt-1 block text-sm w-full bg-[#F2F2F2] font-extralight px-4 py-3 border border-[#B1B1B1] rounded-lg
                                            focus:outline-none focus:ring-2 focus:ring-accent focus:border-transparent">
                        </div>
                    </div>
                </div>

                <!-- School Information Section -->
                <div class="form-section">
                    <label class="block text-sm font-medium text-gray-700 ">School Information</label>
                    <div class="grid grid-cols-2 gap-2 section-grid">
                        <!-- Role -->
                        <div class="field-container">
                            <div class="error-placeholder" id="role-error-placeholder"></div>
                            <select id="role" name="role" class="cursor-pointer mt-1 block text-sm w-full bg-[#F2F2F2] font-extralight px-4 py-3 border border-[#B1B1B1] rounded-lg
                                            focus:outline-none focus:ring-2 focus:ring-accent focus:border-transparent">
                                <option value="" disabled selected>Select Role</option>
                                <option value="student">Student</option>
                                <option value="teacher">Teacher</option>
                            </select>
                        </div>

                        <!-- ID Number-->
                        <div class="field-container">
                            <div class="error-placeholder" id="id_number-error-placeholder"></div>
                            <input type="text" id="id_number" name="id_number" disabled placeholder="Select role first" class="mt-1 block text-sm w-full bg-gray-200 font-extralight px-4 py-3 border border-[#B1B1B1] rounded-lg
                                            focus:outline-none focus:ring-2 focus:ring-accent focus:border-transparent disabled:cursor-not-allowed">
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-2 section-grid" id="school-details">
                        <!-- Year Level -->
                        <div class="field-container">
                            <div class="error-placeholder" id="year_level-error-placeholder"></div>
                            <select id="year_level" name="year_level" class="cursor-pointer mt-1 block text-sm w-full bg-gray-200 font-extralight px-4 py-3 border border-[#B1B1B1] rounded-lg
                                            focus:outline-none focus:ring-2 focus:ring-accent focus:border-transparent disabled:cursor-not-allowed">
                                <option value="" disabled selected>Select Year Level</option>
                                <option value="1">1st Year</option>
                                <option value="2">2nd Year</option>
                                <option value="3">3rd Year</option>
                                <option value="4">4th Year</option>
                            </select>
                        </div>

                        <!-- Department -->
                        <div class="field-container">
                            <div class="error-placeholder" id="department-error-placeholder"></div>
                            <select id="department" name="department" class="cursor-pointer mt-1 block text-sm w-full bg-gray-200 font-extralight px-4 py-3 border border-[#B1B1B1] rounded-lg
                                            focus:outline-none focus:ring-2 focus:ring-accent focus:border-transparent disabled:cursor-not-allowed">
                                <option value="" disabled selected>Select Department</option>
                                @foreach($departments as $department)
                                <option value={{ $department->id }}>{{ $department->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>

                <div>
                    <button class="hover-scale-sm cursor-pointer mt-4 w-full py-2.5 bg-accent text-2xl text-white rounded-lg hover:bg-accent-dark transition-colors duration-300">Sign Up</button>
                </div>
            </form>

            <div class="flex items-center my-6">
                <hr class="flex-grow border-t border-gray-300">
                <span class="mx-4 text-gray-500">or</span>
                <hr class="flex-grow border-t border-gray-300">
            </div>

            <div class="mt-4 text-center">
                <p class="text-xs text-gray-600">Already Have an Account?
                    <a href="/login" class="text-accent font-medium underline">Login Now</a>
                </p>
            </div>
        </div>
    </div>


    @vite('node_modules/sweetalert2/dist/sweetalert2.min.css')
    @vite('resources/js/utils.js')
    @vite('resources/js/config.js')
    @vite('resources/js/helpers.js')
    @vite('resources/js/api/authHandler.js')
    @vite('resources/js/pages/signup.js')
</x-layout>
