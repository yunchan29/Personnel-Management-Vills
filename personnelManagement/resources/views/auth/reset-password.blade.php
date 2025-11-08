<!-- Reset Password Modal -->
<div x-show="activeModal === 'resetPassword'"
     x-data="resetPasswordModal()"
     x-init="init()"
     x-transition:enter="transition ease-out duration-300"
     x-transition:enter-start="opacity-0"
     x-transition:enter-end="opacity-100"
     x-transition:leave="transition ease-in duration-200"
     x-transition:leave-start="opacity-100"
     x-transition:leave-end="opacity-0"
     class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50 p-4"
     @click.self="activeModal = null"
     x-cloak>

    <div x-show="activeModal === 'resetPassword'"
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
            <h2 class="text-2xl font-bold text-[#BD6F22] mb-6 text-center tracking-wide">Reset Your Password</h2>

            <!-- Status Message -->
            <div x-show="resetPasswordStatus"
                 x-text="resetPasswordStatus"
                 class="mb-4 p-3 bg-green-100 text-green-700 rounded-md text-sm"
                 x-cloak></div>

            <!-- Error Messages -->
            <div x-show="resetPasswordErrors.length > 0"
                 class="mb-4 p-3 bg-red-100 text-red-700 rounded-md text-sm"
                 x-cloak>
                <ul class="list-disc pl-5">
                    <template x-for="error in resetPasswordErrors" :key="error">
                        <li x-text="error"></li>
                    </template>
                </ul>
            </div>

            <!-- Reset Password Form -->
            <form @submit.prevent="submitResetPassword" class="space-y-5">

                <!-- Hidden Token and Email -->
                <input type="hidden" x-model="resetPasswordForm.token">
                <input type="hidden" x-model="resetPasswordForm.email">

                <!-- New Password -->
                <div>
                    <label for="reset_password" class="block font-semibold text-[#3A2C1D] mb-1">New Password</label>
                    <div class="relative">
                        <input :type="showResetPassword ? 'text' : 'password'"
                               x-model="resetPasswordForm.password"
                               @input="validateResetPassword"
                               id="reset_password"
                               required
                               class="w-full border border-[#D4C4B0] rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-[#BD6F22] pr-20">
                        <button type="button"
                                @click="showResetPassword = !showResetPassword"
                                class="absolute right-3 top-2 text-sm text-[#BD6F22]"
                                x-text="showResetPassword ? 'Hide' : 'Show'"></button>
                    </div>
                </div>

                <!-- Confirm Password -->
                <div>
                    <label for="reset_password_confirmation" class="block font-semibold text-[#3A2C1D] mb-1">Confirm Password</label>
                    <div class="relative">
                        <input :type="showResetConfirmPassword ? 'text' : 'password'"
                               x-model="resetPasswordForm.password_confirmation"
                               @input="validateResetPassword"
                               id="reset_password_confirmation"
                               required
                               class="w-full border border-[#D4C4B0] rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-[#BD6F22] pr-20">
                        <button type="button"
                                @click="showResetConfirmPassword = !showResetConfirmPassword"
                                class="absolute right-3 top-2 text-sm text-[#BD6F22]"
                                x-text="showResetConfirmPassword ? 'Hide' : 'Show'"></button>
                    </div>
                </div>

                <!-- Password Rules Indicator -->
                <div x-show="resetPasswordForm.password.length > 0"
                     class="bg-gray-50 border rounded-lg p-3 text-sm"
                     x-cloak>
                    <ul class="space-y-1">
                        <li class="flex items-center gap-2">
                            <span class="w-2 h-2 rounded-full" :class="resetPasswordRules.length ? 'bg-green-500' : 'bg-red-500'"></span>
                            At least 8 characters
                        </li>
                        <li class="flex items-center gap-2">
                            <span class="w-2 h-2 rounded-full" :class="resetPasswordRules.uppercase ? 'bg-green-500' : 'bg-red-500'"></span>
                            At least one uppercase letter
                        </li>
                        <li class="flex items-center gap-2">
                            <span class="w-2 h-2 rounded-full" :class="resetPasswordRules.number ? 'bg-green-500' : 'bg-red-500'"></span>
                            At least one number
                        </li>
                        <li class="flex items-center gap-2">
                            <span class="w-2 h-2 rounded-full" :class="resetPasswordRules.special ? 'bg-green-500' : 'bg-red-500'"></span>
                            At least one special character
                        </li>
                        <li class="flex items-center gap-2">
                            <span class="w-2 h-2 rounded-full" :class="resetPasswordRules.match ? 'bg-green-500' : 'bg-red-500'"></span>
                            Passwords must match
                        </li>
                    </ul>
                </div>

                <!-- Submit Button -->
                <div>
                    <button type="submit"
                            :disabled="!isResetFormValid || resetPasswordLoading"
                            class="w-full bg-[#BD6F22] text-white font-semibold py-2 px-4 rounded-md transition duration-200 shadow"
                            :class="(!isResetFormValid || resetPasswordLoading) ? 'opacity-50 cursor-not-allowed' : 'hover:bg-[#a85d1f]'">
                        <span x-show="!resetPasswordLoading">Reset Password</span>
                        <span x-show="resetPasswordLoading" class="flex items-center justify-center">
                            <svg class="animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                        </span>
                    </button>
                </div>
            </form>

            <!-- Back to Login Link -->
            <div class="mt-6 text-center">
                <button @click="activeModal = 'login'"
                        class="text-sm text-[#BD6F22] hover:underline">
                    Back to login
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Reset Password Modal Script -->
<script>
    function resetPasswordModal() {
        return {
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

            // Initialize from URL params
            init() {
                const urlParams = new URLSearchParams(window.location.search);
                const token = urlParams.get('token');
                const email = urlParams.get('email');

                if (token && email) {
                    this.resetPasswordForm.token = token;
                    this.resetPasswordForm.email = email;
                }
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

            // Check if reset form is valid
            get isResetFormValid() {
                return this.resetPasswordRules.length &&
                       this.resetPasswordRules.lowercase &&
                       this.resetPasswordRules.uppercase &&
                       this.resetPasswordRules.number &&
                       this.resetPasswordRules.special &&
                       this.resetPasswordRules.match;
            },

            // Submit reset password form
            async submitResetPassword() {
                this.resetPasswordLoading = true;
                this.resetPasswordErrors = [];
                this.resetPasswordStatus = '';

                try {
                    const response = await axios.post('/reset-password', this.resetPasswordForm, {
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')
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
                            activeModal = 'login';
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
        };
    }
</script>
