<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>VillsPMS</title>
  <!-- Favicon -->
    <link rel="icon" href="{{ asset('images/villslogo3.png') }}" type="image/png">
  <!-- Tailwind CSS -->
  <script src="https://cdn.tailwindcss.com"></script>

  <!-- Google Font: Barlow Condensed -->
  <link href="https://fonts.googleapis.com/css2?family=Barlow+Condensed:wght@500;700&display=swap" rel="stylesheet">

  <!-- Alata Font for modals -->
  <link href="https://fonts.googleapis.com/css2?family=Alata&display=swap" rel="stylesheet">

  <!-- Axios for AJAX -->
  <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>

  <!-- SweetAlert2 -->
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

  <!-- Flatpickr for date picker -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
  <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>

  <style>
    html {
      scroll-behavior: smooth;
    }

    .barlow-condensed {
      font-family: 'Barlow Condensed', sans-serif;
    }

    .font-alata {
      font-family: 'Alata', sans-serif;
    }

    [x-cloak] {
      display: none !important;
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
      background-position: center top;
      background-repeat: no-repeat;
      background-attachment: scroll;
    }

    .hero-section {
      height: 100vh;
      min-height: 600px;
    }

    @media (max-width: 768px) {
      .parallax-bg {
        background-image: linear-gradient(135deg, #f5f5f5 0%, #e8e0d5 50%, #d4c4b0 100%);
        background-size: cover;
      }
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
<body class="bg-white text-gray-900" x-data="authModals()" x-init="init()">

  <!-- Navigation -->
  <x-landingPage.navbar/>

  <!-- Hero Section -->
  <section class="relative w-full parallax-bg hero-section">
    <div class="absolute inset-0 bg-black bg-opacity-10 z-0"></div>

    <div class="absolute inset-0 z-10 flex items-center justify-center md:justify-start px-4 sm:px-6 md:px-16 pt-20 md:pt-0">
      <h1 id="hero-heading"
          class="barlow-condensed fade-slide-up text-[#8C5A3C] text-2xl sm:text-3xl md:text-4xl lg:text-5xl font-bold leading-tight sm:leading-normal tracking-tight max-w-full sm:max-w-xl md:max-w-2xl text-center md:text-left uppercase px-4">
        "CONNECTING TALENT WITH OPPORTUNITY — ANYTIME,<br class="hidden sm:block">ANYWHERE."
      </h1>
    </div>
  </section>

  <!-- Job Listings Section -->
  <section id="jobs" class="px-6 md:px-16 py-12 space-y-8">
    <x-landingPage.jobListing :jobs="$jobs" />
  </section>

  <!-- Footer Section -->
  <section id="footer">
    <x-landingPage.footer/>
  </section>

  <!-- Auth Modals -->
  @include('auth.login')
  @include('auth.register')
  @include('auth.forgot-password')
  @include('auth.reset-password')

  <!-- Mobile Menu Toggle -->
  <script>
    document.getElementById('menu-btn')?.addEventListener('click', function () {
      document.getElementById('mobile-menu')?.classList.toggle('hidden');
    });

    // Close mobile menu when clicking on nav links
    document.querySelectorAll('#mobile-menu a').forEach(link => {
      link.addEventListener('click', function () {
        document.getElementById('mobile-menu')?.classList.add('hidden');
      });
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

  <!-- Alpine.js Auth Modals Script -->
  <script>
    function authModals() {
      return {
        // Modal state
        activeModal: null,

        // Login form
        loginForm: {
          email: '',
          password: '',
          remember: false
        },
        loginErrors: [],
        loginStatus: '',
        loginLoading: false,
        showLoginPassword: false,

        // Register form
        registerForm: {
          first_name: '',
          last_name: '',
          email: '',
          birth_date: '',
          gender: '',
          password: '',
          password_confirmation: '',
          terms: false
        },
        registerErrors: [],
        registerLoading: false,
        showRegPassword: false,
        showRegConfirmPassword: false,
        showPasswordRules: false,
        passwordRules: {
          length: false,
          lowercase: false,
          uppercase: false,
          number: false,
          special: false,
          match: false
        },

        // Forgot password form
        forgotPasswordForm: {
          email: ''
        },
        forgotPasswordErrors: [],
        forgotPasswordStatus: '',
        forgotPasswordLoading: false,

        // Reset password form
        resetPasswordForm: {
          token: '',
          email: '',
          password: '',
          password_confirmation: ''
        },
        resetPasswordErrors: [],
        resetPasswordStatus: '',
        resetPasswordLoading: false,
        showResetPassword: false,
        showResetConfirmPassword: false,
        resetPasswordRules: {
          length: false,
          lowercase: false,
          uppercase: false,
          number: false,
          special: false,
          match: false
        },

        // Date picker state
        showDatePicker: false,
        selectedYear: new Date().getFullYear() - 18,
        selectedMonth: new Date().getMonth(),
        selectedDay: null,
        datePickerDropUp: false,

        init() {
          // Check for reset password token in URL
          const urlParams = new URLSearchParams(window.location.search);
          const token = urlParams.get('token');
          const email = urlParams.get('email');

          if (token && email) {
            this.resetPasswordForm.token = token;
            this.resetPasswordForm.email = email;
            this.activeModal = 'resetPassword';
          }
        },

        // Get years for date picker (from 100 years ago to 18 years ago)
        get availableYears() {
          const currentYear = new Date().getFullYear();
          const years = [];
          for (let i = currentYear - 100; i <= currentYear - 18; i++) {
            years.push(i);
          }
          return years.reverse();
        },

        // Get months
        get availableMonths() {
          return [
            { value: 0, label: 'January' },
            { value: 1, label: 'February' },
            { value: 2, label: 'March' },
            { value: 3, label: 'April' },
            { value: 4, label: 'May' },
            { value: 5, label: 'June' },
            { value: 6, label: 'July' },
            { value: 7, label: 'August' },
            { value: 8, label: 'September' },
            { value: 9, label: 'October' },
            { value: 10, label: 'November' },
            { value: 11, label: 'December' }
          ];
        },

        // Get days in selected month
        get availableDays() {
          const daysInMonth = new Date(this.selectedYear, this.selectedMonth + 1, 0).getDate();
          const days = [];
          for (let i = 1; i <= daysInMonth; i++) {
            days.push(i);
          }
          return days;
        },

        // Set the birthdate when day is selected
        selectBirthdate(day) {
          this.selectedDay = day;
          const month = String(this.selectedMonth + 1).padStart(2, '0');
          const dayStr = String(day).padStart(2, '0');
          this.registerForm.birth_date = `${month}/${dayStr}/${this.selectedYear}`;
          this.showDatePicker = false;
        },

        // Open date picker
        openDatePicker() {
          this.showDatePicker = true;

          // Determine if picker should drop up or down
          this.$nextTick(() => {
            const input = document.getElementById('reg_birth_date');
            if (input) {
              const rect = input.getBoundingClientRect();
              const spaceBelow = window.innerHeight - rect.bottom;
              const spaceAbove = rect.top;
              const pickerHeight = 400; // minimum space needed to show picker comfortably

              // Drop up if there's not enough space below but more space above
              this.datePickerDropUp = spaceBelow < pickerHeight && spaceAbove > spaceBelow;
            }
          });

          // If birth_date is already set, parse it
          if (this.registerForm.birth_date) {
            const parts = this.registerForm.birth_date.split('/');
            if (parts.length === 3) {
              this.selectedMonth = parseInt(parts[0]) - 1;
              this.selectedDay = parseInt(parts[1]);
              this.selectedYear = parseInt(parts[2]);
            }
          }
        },

        // Validate register password
        validateRegisterPassword() {
          const password = this.registerForm.password;
          const confirm = this.registerForm.password_confirmation;

          this.passwordRules.length = password.length >= 8;
          this.passwordRules.lowercase = /[a-z]/.test(password);
          this.passwordRules.uppercase = /[A-Z]/.test(password);
          this.passwordRules.number = /[0-9]/.test(password);
          this.passwordRules.special = /[@$!%*#?&]/.test(password);
          this.passwordRules.match = password && confirm && password === confirm;
        },

        // Validate reset password
        validateResetPassword() {
          const password = this.resetPasswordForm.password;
          const confirm = this.resetPasswordForm.password_confirmation;

          this.resetPasswordRules.length = password.length >= 8;
          this.resetPasswordRules.lowercase = /[a-z]/.test(password);
          this.resetPasswordRules.uppercase = /[A-Z]/.test(password);
          this.resetPasswordRules.number = /[0-9]/.test(password);
          this.resetPasswordRules.special = /[@$!%*#?&]/.test(password);
          this.resetPasswordRules.match = password && confirm && password === confirm;
        },

        // Check if register form is valid
        get isRegisterFormValid() {
          return this.passwordRules.length &&
                 this.passwordRules.lowercase &&
                 this.passwordRules.uppercase &&
                 this.passwordRules.number &&
                 this.passwordRules.special &&
                 this.passwordRules.match &&
                 this.registerForm.terms;
        },

        // Check if reset form is valid
        get isResetFormValid() {
          return this.resetPasswordRules.length &&
                 this.resetPasswordRules.lowercase &&
                 this.resetPasswordRules.uppercase &&
                 this.resetPasswordRules.number &&
                 this.resetPasswordRules.special &&
                 this.resetPasswordRules.match;
        },

        // Submit login form
        async submitLogin() {
          this.loginLoading = true;
          this.loginErrors = [];
          this.loginStatus = '';

          try {
            const response = await axios.post('{{ route('login') }}', this.loginForm, {
              headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '{{ csrf_token() }}'
              }
            });

            if (response.data.success) {
              Swal.fire({
                icon: 'success',
                title: 'Login Successful!',
                text: response.data.message || 'Welcome back!',
                confirmButtonColor: '#BD6F22',
                timer: 2000,
                showConfirmButton: false
              }).then(() => {
                window.location.href = response.data.redirect || '/home';
              });
            }
          } catch (error) {
            if (error.response?.data?.errors) {
              this.loginErrors = Object.values(error.response.data.errors).flat();
            } else if (error.response?.data?.message) {
              this.loginErrors = [error.response.data.message];
            } else {
              this.loginErrors = ['An error occurred. Please try again.'];
            }
          } finally {
            this.loginLoading = false;
          }
        },

        // Submit register form
        async submitRegister() {
          this.registerLoading = true;
          this.registerErrors = [];

          try {
            const response = await axios.post('{{ route('register') }}', this.registerForm, {
              headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '{{ csrf_token() }}'
              }
            });

            if (response.data.success) {
              Swal.fire({
                icon: 'success',
                title: 'Registration Successful!',
                text: response.data.message || 'Your account has been created!',
                confirmButtonColor: '#BD6F22',
                timer: 2500,
                showConfirmButton: false
              }).then(() => {
                this.activeModal = null;
                window.location.href = response.data.redirect || '/home';
              });
            }
          } catch (error) {
            if (error.response?.data?.errors) {
              this.registerErrors = Object.values(error.response.data.errors).flat();
            } else if (error.response?.data?.message) {
              this.registerErrors = [error.response.data.message];
            } else {
              this.registerErrors = ['An error occurred. Please try again.'];
            }
          } finally {
            this.registerLoading = false;
          }
        },

        // Submit forgot password form
        async submitForgotPassword() {
          this.forgotPasswordLoading = true;
          this.forgotPasswordErrors = [];
          this.forgotPasswordStatus = '';

          try {
            const response = await axios.post('{{ route('password.email') }}', this.forgotPasswordForm, {
              headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '{{ csrf_token() }}'
              }
            });

            if (response.data.success || response.data.status) {
              this.forgotPasswordStatus = response.data.message || response.data.status || 'Password reset link sent to your email!';
              this.forgotPasswordForm.email = '';

              Swal.fire({
                icon: 'success',
                title: 'Email Sent!',
                text: this.forgotPasswordStatus,
                confirmButtonColor: '#BD6F22',
                timer: 3000,
                showConfirmButton: true
              });
            }
          } catch (error) {
            if (error.response?.data?.errors) {
              this.forgotPasswordErrors = Object.values(error.response.data.errors).flat();
            } else if (error.response?.data?.message) {
              this.forgotPasswordErrors = [error.response.data.message];
            } else {
              this.forgotPasswordErrors = ['An error occurred. Please try again.'];
            }
          } finally {
            this.forgotPasswordLoading = false;
          }
        },

        // Submit reset password form
        async submitResetPassword() {
          this.resetPasswordLoading = true;
          this.resetPasswordErrors = [];
          this.resetPasswordStatus = '';

          try {
            const response = await axios.post('{{ route('password.update') }}', this.resetPasswordForm, {
              headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '{{ csrf_token() }}'
              }
            });

            if (response.data.success || response.data.status) {
              Swal.fire({
                icon: 'success',
                title: 'Password Reset!',
                text: response.data.message || 'Your password has been reset successfully!',
                confirmButtonColor: '#BD6F22',
                timer: 2500,
                showConfirmButton: false
              }).then(() => {
                this.activeModal = 'login';
                // Clear URL parameters
                window.history.replaceState({}, document.title, window.location.pathname);
              });
            }
          } catch (error) {
            if (error.response?.data?.errors) {
              this.resetPasswordErrors = Object.values(error.response.data.errors).flat();
            } else if (error.response?.data?.message) {
              this.resetPasswordErrors = [error.response.data.message];
            } else {
              this.resetPasswordErrors = ['An error occurred. Please try again.'];
            }
          } finally {
            this.resetPasswordLoading = false;
          }
        }
      }
    }

    // Birthdate Picker Component
    function birthdatePicker() {
      return {
        // Constants
        minYear: 1900,
        maxYear: new Date().getFullYear() - 18,
        minAge: 18,

        // Day names
        dayNames: ['Su', 'Mo', 'Tu', 'We', 'Th', 'Fr', 'Sa'],

        // Month names
        months: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],

        // State
        showPicker: false,
        pickerYear: new Date().getFullYear() - 18,
        pickerMonth: new Date().getMonth(),
        selectedDate: null,
        ageError: '',

        // Initialization
        init() {
          // Component initialized
        },

        // Computed: Calendar days grid
        get calendarDays() {
          const firstDay = new Date(this.pickerYear, this.pickerMonth, 1).getDay();
          const daysInMonth = new Date(this.pickerYear, this.pickerMonth + 1, 0).getDate();
          const days = [];

          // Empty cells for days before month starts
          for (let i = 0; i < firstDay; i++) {
            days.push({ day: null, disabled: true });
          }

          // Calculate max allowed date (18 years ago from today)
          const today = new Date();
          const maxDate = new Date(today.getFullYear() - this.minAge, today.getMonth(), today.getDate());

          // Generate days of the month
          for (let i = 1; i <= daysInMonth; i++) {
            const date = new Date(this.pickerYear, this.pickerMonth, i);
            const isFuture = date > maxDate;
            const isSelected = this.selectedDate &&
                              date.toDateString() === this.selectedDate.toDateString();

            days.push({
              day: i,
              disabled: isFuture,
              isFuture,
              isSelected
            });
          }

          return days;
        },

        // Methods
        togglePicker() {
          this.showPicker = !this.showPicker;
        },

        closePicker() {
          this.showPicker = false;
        },

        validateAge(date) {
          const today = new Date();
          const birthDate = new Date(date);
          let age = today.getFullYear() - birthDate.getFullYear();
          const monthDiff = today.getMonth() - birthDate.getMonth();

          if (monthDiff < 0 || (monthDiff === 0 && today.getDate() < birthDate.getDate())) {
            age--;
          }

          return age >= this.minAge;
        },

        selectDay(day) {
          if (!day) return;

          this.selectedDate = new Date(this.pickerYear, this.pickerMonth, day);

          // Validate age requirement
          if (!this.validateAge(this.selectedDate)) {
            this.ageError = `You must be at least ${this.minAge} years old to register`;
            registerForm.birth_date = '';
            return;
          }

          // Clear error and format date
          this.ageError = '';
          const month = String(this.pickerMonth + 1).padStart(2, '0');
          const dayStr = String(day).padStart(2, '0');
          registerForm.birth_date = `${month}/${dayStr}/${this.pickerYear}`;

          // Close picker
          this.closePicker();
        },

        navigateToPreviousMonth() {
          if (this.pickerMonth === 0) {
            this.pickerMonth = 11;
            this.pickerYear--;
          } else {
            this.pickerMonth--;
          }
        },

        navigateToNextMonth() {
          const today = new Date();
          const maxYear = today.getFullYear() - this.minAge;
          const maxMonth = today.getMonth();

          if (this.pickerMonth === 11) {
            if (this.pickerYear < maxYear) {
              this.pickerMonth = 0;
              this.pickerYear++;
            }
          } else {
            // Check if we can advance to next month
            if (this.pickerYear < maxYear || (this.pickerYear === maxYear && this.pickerMonth < maxMonth)) {
              this.pickerMonth++;
            }
          }
        }
      };
    }
  </script>

<script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>

</body>
</html>
