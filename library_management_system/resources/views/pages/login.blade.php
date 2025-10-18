<x-layout :title="'Login'">

    <div class="min-h-screen flex items-center justify-center bg-background">
        <div class="bg-white p-12 rounded-2xl shadow-lg w-full max-w-lg">
            <div class="flex flex-col items-center mb-2">
                <img src="{{ asset('build/assets/images/logo-white.png') }}" width="60" height="60" alt="Logo" class="mb-2">
                <h2 class="text-5xl font-bold mb-6 text-center">Login</h2>
            </div>

            <form id="loginForm" method="post" class="space-y-6" novalidate>
                
                <!-- Username Input -->
                <div class="relative field-container mt-8">
                    <div class="error-placeholder" id="username-error-placeholder"></div>
                    <div class="relative mt-1">
                        <img src="{{ asset('build/assets/icons/username.svg') }}" alt="Username" class="absolute left-3 top-1/2 transform -translate-y-1/2 w-10 h-15 pointer-events-none z-10">
                        <input type="text" id="username" name="username" required
                               placeholder="Enter your username"
                               class="block w-full pl-16 bg-[#F2F2F2] font-extralight pr-4 py-4 border border-[#B1B1B1] rounded-2xl
                                    focus:outline-none focus:ring-2 focus:ring-accent focus:border-transparent relative z-0">
                    </div>
                </div>
                <!-- Password Input -->
                <div class="relative field-container mt-8">
                    <div class="error-placeholder" id="password-error-placeholder"></div>
                    <div class="relative mt-1">
                        <img src="{{ asset('build/assets/icons/password.svg') }}" alt="Password" class="absolute left-4 top-1/2 transform -translate-y-1/2 w-8 h-8 pointer-events-none z-10">
                        <input type="password" id="password" name="password" required
                               placeholder="Enter your password"
                               class="block w-full pl-16 bg-[#F2F2F2] font-extralight pr-16 py-4 border border-[#B1B1B1] rounded-2xl
                                      focus:outline-none focus:ring-2 focus:ring-accent focus:border-transparent relative z-0">
                        <button type="button" id="togglePassword" class="absolute right-2 top-1/2 transform -translate-y-1/2 z-10">
                            <img id="eyeIcon" src="{{ asset('build/assets/icons/eye-off.svg') }}" alt="Toggle password visibility" class="w-14 h-13 cursor-pointer hover:opacity-70 transition-opacity">
                        </button>
                    </div>
                </div>

                <div>
                    <button type="submit" class="hover-scale-sm cursor-pointer mt-4 w-full py-2.5 bg-accent text-2xl text-white rounded-lg hover:bg-accent-dark transition-colors duration-300">Login</button>
                </div>
            </form>

            <div class="flex items-center my-6">
                <hr class="flex-grow border-t border-gray-300">
                <span class="mx-4 text-gray-500">or</span>
                <hr class="flex-grow border-t border-gray-300">
            </div>

            <div class="mt-4 text-center">
                <p class="text-xs text-gray-600">Don't have an account yet? 
                    <a href="{{ asset('signup') }}" class="text-accent font-medium underline">Sign Up Here</a>
                </p>
            </div>
        </div>
    </div>

    {{-- Remove direct PHP includes and scripts, use @vite for assets --}}
    @vite('node_modules/sweetalert2/dist/sweetalert2.all.min.js')
    @vite('resources/js/utils.js')
    @vite('resources/js/helpers.js')
    @vite('resources/js/pages/login.js')
</x-layout>