import { confirmLogout } from "./utils/alerts.js";

const profileDropdown = document.getElementById('profileDropdown');
const dropdownMenu = document.getElementById('dropdownMenu');

// profile dropdown toggle
if (profileDropdown && dropdownMenu) {
  profileDropdown.addEventListener('click', (e) => {
    e.stopPropagation();
    dropdownMenu.classList.toggle('hidden');
  });

  // Close dropdown when clicking outside
  document.addEventListener('click', (e) => {
    if (!profileDropdown.contains(e.target) && !dropdownMenu.contains(e.target)) {
      dropdownMenu.classList.add('hidden');
    }
  });
}

// mobile menu toggle
const mobileBtn = document.getElementById('mobile-menu-btn');
  const mobileMenu = document.getElementById('mobile-menu');
  const menuIcon = document.getElementById('menu-icon');
  const closeIcon = document.getElementById('close-icon');

  mobileBtn.addEventListener('click', () => {
    const isOpen = mobileMenu.classList.contains('hidden');
    
    if (isOpen) {
      mobileMenu.classList.remove('hidden');
      menuIcon.classList.add('hidden');
      closeIcon.classList.remove('hidden');
    } else {
      mobileMenu.classList.add('hidden');
      menuIcon.classList.remove('hidden');
      closeIcon.classList.add('hidden');
    }
});

// Close mobile menu when clicking outside
document.addEventListener('click', (e) => {
if (!mobileBtn.contains(e.target) && !mobileMenu.contains(e.target)) {
    mobileMenu.classList.add('hidden');
    menuIcon.classList.remove('hidden');
    closeIcon.classList.add('hidden');
}
});


if (document.getElementById('logoutForm')) {
  const logoutForm = document.getElementById('logoutForm');

  logoutForm.addEventListener('submit', (e) => {
    e.preventDefault();
     confirmLogout();
  });
}

// Add to your JavaScript file
document.getElementById('mobile-profile-btn')?.addEventListener('click', function(e) {
    e.stopPropagation();
    const profileMenu = document.getElementById('mobile-profile-menu');
    const mobileMenu = document.getElementById('mobile-menu');
    
    // Close mobile menu if open
    if (!mobileMenu.classList.contains('hidden')) {
        mobileMenu.classList.add('hidden');
        document.getElementById('menu-icon').classList.remove('hidden');
        document.getElementById('close-icon').classList.add('hidden');
    }
    
    profileMenu.classList.toggle('hidden');
});

// Close mobile profile menu when clicking outside
document.addEventListener('click', function(e) {
    const profileMenu = document.getElementById('mobile-profile-menu');
    const profileBtn = document.getElementById('mobile-profile-btn');
    
    if (profileMenu && !profileMenu.contains(e.target) && !profileBtn?.contains(e.target)) {
        profileMenu.classList.add('hidden');
    }
});

// Handle mobile logout
document.getElementById('logoutFormMobile')?.addEventListener('submit', function(e) {
    e.preventDefault();
    // Add your logout logic here (same as desktop)
});