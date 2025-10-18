<footer>
    <div class=" bg-primary  text-white  pt-18 flex justify-center flex-col items-center space-y-12">
        <!-- Logo Section -->
          <a href="{{ asset('index.php') }}" class="flex items-center space-x-3 hover:opacity-80 transition-opacity duration-300 cursor-pointer">
            <img src="{{ asset('build/assets/images/logo.png') }}" width="50" height="50" alt="Logo" class="rounded-full">
            <div>
              <h1 class="text-xl font-bold leading-tight">Smart Library</h1>
              <p class="text-xs text-gray-300">Management System</p>
            </div>
          </a>

        <!-- Desktop Navigation -->
          <nav class="flex items-center space-x-20">
            <a href="{{ asset('index.php') }}" class="hover-scale-lg text-sm font-medium tracking-wide uppercase cursor-pointer hover:text-accent transition-colors duration-300">Home</a>
            <a href="{{ asset('about.php') }}" class="hover-scale-lg text-sm font-medium tracking-wide uppercase cursor-pointer hover:text-accent transition-colors duration-300">About</a>
            <a href="{{ asset('catalog.php') }}" class="hover-scale-lg text-sm font-medium tracking-wide uppercase cursor-pointer hover:text-accent transition-colors duration-300">Catalog</a>
          </nav>

        <!-- Social Media Links -->
          <div class="mt-6 flex flex-col items-center pb-4 rounded-lg space-y-4">
            <h2 class="font-bold">Stay in Touch</h2>
            <div class="flex">
                <a href="" class="hover-scale-xl"><img src="{{ asset('build/assets/icons/footer-fb.svg') }}" alt="Facebook Logo"></a>
                <a href="" class="hover-scale-xl"><img src="{{ asset('build/assets/icons/footer-insta.svg') }}" alt="Instagram Logo"></a>
                <a href="" class="hover-scale-xl"><img src="{{ asset('build/assets/icons/footer-tiktok.svg') }}" alt="TikTok Logo"></a>
                <a href="" class="hover-scale-xl"><img src="{{ asset('build/assets/icons/footer-gmail.svg') }}" alt="Gmail Logo"></a>
                <a href="" class="hover-scale-xl"><img src="{{ asset('build/assets/icons/footer-pinterest.svg') }}" alt="Pinterest Logo"></a>
            </div>
          </div>
    </div>

    <div class="bg-[#1A1A1A] p-6 ">
        <p class="text-center text-xs text-gray-300 p-4 font-extralight tracking-wide">&copy; 2025 Smart Library Management System. All rights reserved.</p>
    </div>
</footer>