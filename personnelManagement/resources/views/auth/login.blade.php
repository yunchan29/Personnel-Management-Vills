<!-- Login Modal -->
<div x-show="activeModal === 'login'"
     x-data="loginModal()"
     x-transition:enter="transition ease-out duration-300"
     x-transition:enter-start="opacity-0"
     x-transition:enter-end="opacity-100"
     x-transition:leave="transition ease-in duration-200"
     x-transition:leave-start="opacity-100"
     x-transition:leave-end="opacity-0"
     class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50 p-4"
     @click.self="activeModal = null"
     x-cloak>

    <div x-show="activeModal === 'login'"
         x-transition:enter="transition ease-out duration-300 transform"
         x-transition:enter-start="opacity-0 scale-95"
         x-transition:enter-end="opacity-100 scale-100"
         x-transition:leave="transition ease-in duration-200 transform"
         x-transition:leave-start="opacity-100 scale-100"
         x-transition:leave-end="opacity-0 scale-95"
         class="bg-white rounded-lg shadow-xl w-full max-w-4xl max-h-[90vh] overflow-y-auto relative"
         @click.away="activeModal = null">

        <!-- Close Button -->
        <button @click="activeModal = null"
                class="absolute top-4 right-4 z-10 text-gray-600 hover:text-gray-800 text-3xl font-bold leading-none bg-white rounded-full w-8 h-8 flex items-center justify-center">
            &times;
        </button>

        <div class="flex flex-col md:flex-row min-h-[500px]">

            <!-- Left Side (Signup Invite) -->
            <div class="w-full md:w-1/2 bg-[#BD9168] flex flex-col justify-center items-center p-8 text-white rounded-l-lg">
                <div class="text-center max-w-md">
                    <h2 class="text-2xl md:text-3xl font-alata mb-6 leading-relaxed">
                        Create one now to start applying and land your next job online — it's quick and easy!
                    </h2>
                    <button @click="activeModal = 'register'"
                            class="inline-block text-white bg-[#BD6F22] px-6 py-2 rounded-lg font-bold hover:bg-[#a35718] transition">
                        Sign Up
                    </button>
                </div>
            </div>

            <!-- Right Side (Login Form) -->
            <div class="w-full md:w-1/2 bg-white p-8 flex flex-col justify-center items-center">

                <!-- Logo -->
                <img src="/images/villsLogo2.png" alt="Logo" class="w-auto h-16 mb-6">

                <!-- Login Header -->
                <h2 class="text-2xl md:text-3xl font-bold text-center mb-6 text-[#BD6F22]">Login</h2>

                <!-- Status Messages -->
                <div x-show="loginStatus"
                     x-text="loginStatus"
                     class="mb-4 text-sm text-green-600 w-full max-w-sm"
                     x-cloak></div>

                <!-- Error Messages -->
                <div x-show="loginErrors.length > 0"
                     class="mb-4 w-full max-w-sm text-sm text-red-600"
                     x-cloak>
                    <ul class="list-disc list-inside">
                        <template x-for="error in loginErrors" :key="error">
                            <li x-text="error"></li>
                        </template>
                    </ul>
                </div>

                <!-- Login Form -->
                <form @submit.prevent="submitLogin" class="w-full max-w-sm">

                    <div class="mb-4">
                        <label for="login_email" class="block text-gray-700 mb-1">Email</label>
                        <input type="email"
                               x-model="loginForm.email"
                               id="login_email"
                               required
                               autofocus
                               class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-[#BD6F22]">
                    </div>

                    <div class="mb-4">
                        <label for="login_password" class="block text-gray-700 mb-1">Password</label>
                        <input type="password"
                               x-model="loginForm.password"
                               id="login_password"
                               required
                               class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-[#BD6F22]">
                    </div>

                    <div class="flex items-center mb-6">
                        <input type="checkbox"
                               x-model="loginForm.remember"
                               id="login_remember"
                               class="mr-2 h-4 w-4 text-[#BD6F22] bg-gray-100 border-gray-300 rounded focus:ring-[#BD6F22]">
                        <label for="login_remember" class="text-gray-700 text-sm">Remember Me</label>
                    </div>

                    <div class="flex flex-col items-center space-y-3">
                        <button type="submit"
                                :disabled="loginLoading"
                                class="bg-[#BD6F22] text-white px-8 py-2 rounded-lg hover:bg-[#a35718] transition w-40 disabled:opacity-50 disabled:cursor-not-allowed">
                            <span x-show="!loginLoading">Login</span>
                            <span x-show="loginLoading" class="flex items-center justify-center">
                                <svg class="animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                            </span>
                        </button>
                        <button type="button"
                                @click="activeModal = 'forgotPassword'"
                                class="text-sm text-[#BD6F22] hover:underline">
                            Forgot Password?
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Login Modal Script -->
<script>
    function loginModal() {
        return {
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

            // Submit login form
            async submitLogin() {
                this.loginLoading = true;
                this.loginErrors = [];
                this.loginStatus = '';

                try {
                    const response = await axios.post('/login', this.loginForm, {
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')
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
            }
        };
    }
</script>
