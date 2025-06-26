<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>VillsPMS</title>

  <!-- Tailwind CSS -->
  <script src="https://cdn.tailwindcss.com"></script>

  <!-- Google Font: Barlow Condensed -->
  <link href="https://fonts.googleapis.com/css2?family=Barlow+Condensed:wght@500;700&display=swap" rel="stylesheet">

  <style>
    .barlow-condensed {
      font-family: 'Barlow Condensed', sans-serif;
    }

    .nav-link {
      color: #374151;
      font-weight: 500;
      transition: color 0.3s ease;
    }

    .nav-link:hover {
      color: #BD9168;
    }

    .nav-button {
      background-color: #BD9168;
      color: white;
      padding: 0.5rem 1rem;
      border-radius: 0.375rem;
      font-weight: 500;
      transition: background-color 0.3s ease;
    }

    .nav-button:hover {
      background-color: #a7744f;
    }

    .parallax-bg {
      background-image: url('/images/Workflows.png');
      background-size: cover;
      background-position: center;
      background-attachment: scroll;
    }

    @keyframes fadeSlideUp {
      0% {
        opacity: 0;
        transform: translateY(30px);
      }
      100% {
        opacity: 1;
        transform: translateY(0);
      }
    }

    .fade-slide-up {
      opacity: 0;
      transform: translateY(30px);
      transition: all 1s ease-out;
    }

    .fade-slide-up.visible {
      opacity: 1;
      transform: translateY(0);
    }
  </style>
</head>
<body class="bg-white text-gray-900">

  <!-- Navigation -->
  <x-landingPage.navbar/>

  <!-- Hero Section -->
  <section class="relative w-full h-screen bg-cover bg-center pt-16 parallax-bg">
    <div class="absolute inset-0 bg-black bg-opacity-10 z-0"></div>

    <div class="relative z-10 flex items-center h-full px-6 md:px-16">
      <h1 id="hero-heading"
          class="barlow-condensed fade-slide-up text-[#8C5A3C] text-3xl sm:text-4xl md:text-5xl font-bold leading-normal tracking-tight max-w-2xl text-left uppercase">
        “CONNECTING TALENT WITH OPPORTUNITY — ANYTIME,<br>ANYWHERE.”
      </h1>
    </div>
  </section>

  <!-- Job Listings Section -->
  <section class="px-6 md:px-16 py-12 space-y-8">
    <x-landingPage.jobListing :jobs="$jobs" />
  </section>

  <!-- Footer Section -->
  <x-landingPage.footer/>

  <!-- Mobile Menu Toggle -->
  <script>
    document.getElementById('menu-btn')?.addEventListener('click', function () {
      document.getElementById('mobile-menu')?.classList.toggle('hidden');
    });
  </script>

  <!-- Scroll Animations -->
  <script>
    // Hero text scroll + fade-up reveal
    const heroHeading = document.getElementById('hero-heading');

    const observer = new IntersectionObserver((entries) => {
      entries.forEach(entry => {
        if (entry.isIntersecting) {
          entry.target.classList.add('visible');
          observer.unobserve(entry.target);
        }
      });
    }, {
      threshold: 0.5
    });

    if (heroHeading) observer.observe(heroHeading);

    // Parallax scroll effect
    window.addEventListener('scroll', () => {
      const scrollY = window.scrollY;
      if (heroHeading) {
        heroHeading.style.transform = `translateY(${Math.min(20 + scrollY * 0.1, 100)}px)`;
        heroHeading.style.opacity = Math.max(1 - scrollY * 0.002, 0);
      }
    });
  </script>

  <!-- Loading Overlay -->
  <div id="loading-overlay" class="fixed inset-0 bg-white bg-opacity-75 flex items-center justify-center z-50 hidden">
    <div class="flex items-center space-x-4">
      <svg class="animate-spin h-8 w-8 text-[#BD9168]" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path>
      </svg>
      <span class="text-[#BD9168] font-semibold text-lg">Loading...</span>
    </div>
  </div>

  <!-- Loader Triggers -->
  <script>
    const loadingOverlay = document.getElementById('loading-overlay');

    const searchForm = document.querySelector('form[action="{{ route('welcome') }}"]');
    if (searchForm) {
      searchForm.addEventListener('submit', function () {
        loadingOverlay.classList.remove('hidden');
      });
    }

    document.addEventListener('DOMContentLoaded', function () {
      document.querySelectorAll('.pagination a').forEach(function (el) {
        el.addEventListener('click', function () {
          loadingOverlay.classList.remove('hidden');
        });
      });

      document.querySelectorAll('a[href^="/job/"]').forEach(function (btn) {
        btn.addEventListener('click', function () {
          loadingOverlay.classList.remove('hidden');
        });
      });
    });
  </script>
<script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>

</body>
</html>
