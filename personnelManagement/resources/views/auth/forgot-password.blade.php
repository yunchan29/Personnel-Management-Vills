<!-- Forgot Password Modal -->
<div x-show="activeModal === 'forgotPassword'"
     x-data="forgotPasswordModal()"
     x-transition:enter="transition ease-out duration-300"
     x-transition:enter-start="opacity-0"
     x-transition:enter-end="opacity-100"
     x-transition:leave="transition ease-in duration-200"
     x-transition:leave-start="opacity-100"
     x-transition:leave-end="opacity-0"
     class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50 p-4"
     @click.self="activeModal = null"
     x-cloak>

    <div x-show="activeModal === 'forgotPassword'"
         x-transition:enter="transition ease-out duration-300 transform"
         x-transition:enter-start="opacity-0 scale-95"
         x-transition:enter-end="opacity-100 scale-100"
         x-transition:leave="transition ease-in duration-200 transform"
         x-transition:leave-start="opacity-100 scale-100"
         x-transition:leave-end="opacity-0 scale-95"
         class="bg-white rounded-lg shadow-xl w-full max-w-md max-h-[90vh] overflow-y-auto relative"
         @click.away="activeModal = null">

        <!-- Close Button -->
        <button @click="activeModal = null"
                class="absolute top-4 right-4 z-10 text-gray-600 hover:text-gray-800 text-3xl font-bold leading-none bg-white rounded-full w-8 h-8 flex items-center justify-center">
            &times;
        </button>

        <div class="p-8">
            <!-- Header -->
            <h2 class="text-2xl font-bold text-[#A06E45] mb-6 text-center uppercase tracking-wide">Forgot Password</h2>

            <!-- Status Message -->
            <div x-show="forgotPasswordStatus"
                 x-text="forgotPasswordStatus"
                 class="mb-4 text-green-700 bg-green-100 border border-green-200 p-3 rounded text-sm"
                 x-cloak></div>

            <!-- Error Messages -->
            <div x-show="forgotPasswordErrors.length > 0"
                 class="mb-4 text-red-700 bg-red-100 border border-red-200 p-3 rounded text-sm"
                 x-cloak>
                <ul class="list-disc pl-5">
                    <template x-for="error in forgotPasswordErrors" :key="error">
                        <li x-text="error"></li>
                    </template>
                </ul>
            </div>

            <!-- Description -->
            <p class="text-gray-600 text-sm mb-6 text-center">
                Enter your email address and we'll send you a link to reset your password.
            </p>

            <!-- Forgot Password Form -->
            <form @submit.prevent="submitForgotPassword">

                <div class="mb-5">
                    <label for="forgot_email" class="block text-sm font-medium text-gray-700 mb-1">Email Address</label>
                    <input type="email"
                           x-model="forgotPasswordForm.email"
                           id="forgot_email"
                           required
                           autofocus
                           class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-[#A06E45]">
                </div>

                <button type="submit"
                        :disabled="forgotPasswordLoading"
                        class="w-full bg-[#A06E45] text-white font-semibold py-2 px-4 rounded-md hover:bg-[#8c5e3b] transition disabled:opacity-50 disabled:cursor-not-allowed">
                    <span x-show="!forgotPasswordLoading">Send Reset Link</span>
                    <span x-show="forgotPasswordLoading" class="flex items-center justify-center">
                        <svg class="animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                    </span>
                </button>
            </form>

            <!-- Back to Login Link -->
            <div class="mt-6 text-center text-sm">
                <button @click="activeModal = 'login'"
                        class="text-[#A06E45] hover:underline">
                    ← Back to login
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Forgot Password Modal Script -->
<script>
    function forgotPasswordModal() {
        return {
            // Forgot password form
            forgotPasswordForm: {
                email: ''
            },
            forgotPasswordErrors: [],
            forgotPasswordStatus: '',
            forgotPasswordLoading: false,

            // Submit forgot password form
            async submitForgotPassword() {
                this.forgotPasswordLoading = true;
                this.forgotPasswordErrors = [];
                this.forgotPasswordStatus = '';

                try {
                    const response = await axios.post('/forgot-password', this.forgotPasswordForm, {
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')
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
            }
        };
    }
</script>
