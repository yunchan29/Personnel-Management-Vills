<!-- Register Modal -->
<div x-show="activeModal === 'register'"
     x-transition:enter="transition ease-out duration-300"
     x-transition:enter-start="opacity-0"
     x-transition:enter-end="opacity-100"
     x-transition:leave="transition ease-in duration-200"
     x-transition:leave-start="opacity-100"
     x-transition:leave-end="opacity-0"
     class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50 p-4"
     @click.self="activeModal = null"
     x-cloak>

    <div x-show="activeModal === 'register'"
         x-transition:enter="transition ease-out duration-300 transform"
         x-transition:enter-start="opacity-0 scale-95"
         x-transition:enter-end="opacity-100 scale-100"
         x-transition:leave="transition ease-in duration-200 transform"
         x-transition:leave-start="opacity-100 scale-100"
         x-transition:leave-end="opacity-0 scale-95"
         class="bg-white rounded-lg shadow-xl w-full max-w-4xl max-h-[90vh] overflow-hidden relative"
         @click.away="activeModal = null">

        <!-- Close Button -->
        <button @click="activeModal = null"
                class="absolute top-4 right-4 z-20 text-gray-600 hover:text-gray-800 text-3xl font-bold leading-none bg-white rounded-full w-8 h-8 flex items-center justify-center shadow-md">
            &times;
        </button>

        <div class="flex flex-col md:flex-row h-full max-h-[90vh]">

            <!-- Left Side (Login Invite) -->
            <div class="w-full md:w-1/2 bg-[#BD9168] flex flex-col justify-center items-center p-8 text-white rounded-l-lg">
                <div class="text-center max-w-md">
                    <h2 class="text-2xl md:text-3xl font-alata mb-6 leading-relaxed">
                        Welcome Back! Ready to land your next opportunity?
                    </h2>
                    <button @click="activeModal = 'login'"
                            class="inline-block text-white bg-[#BD6F22] px-6 py-2 rounded-lg font-bold hover:bg-[#a35718] transition">
                        Login
                    </button>
                </div>
            </div>

            <!-- Right Side (Register Form) -->
            <div class="w-full md:w-1/2 bg-white flex flex-col overflow-visible">
                <div class="p-8 overflow-y-auto flex-1">

                <!-- Logo -->
                <img src="/images/villsLogo2.png" alt="Logo" class="w-auto h-16 mb-4">

                <!-- Register Header -->
                <h2 class="text-2xl md:text-3xl font-bold text-center mb-4 text-[#BD6F22]">Create Account</h2>

                <!-- Error Messages -->
                <div x-show="registerErrors.length > 0"
                     class="mb-4 text-sm text-red-600"
                     x-cloak>
                    <ul class="list-disc list-inside">
                        <template x-for="error in registerErrors" :key="error">
                            <li x-text="error"></li>
                        </template>
                    </ul>
                </div>

                <!-- Register Form -->
                <form @submit.prevent="submitRegister" class="w-full">

                    <!-- First Name and Last Name -->
                    <div class="flex space-x-2 mb-4">
                        <div class="w-1/2">
                            <label for="reg_first_name" class="block text-gray-700 mb-1 text-sm">First Name <span class="text-red-500">*</span></label>
                            <input type="text"
                                   x-model="registerForm.first_name"
                                   id="reg_first_name"
                                   required
                                   class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-[#BD6F22]">
                        </div>
                        <div class="w-1/2">
                            <label for="reg_last_name" class="block text-gray-700 mb-1 text-sm">Last Name <span class="text-red-500">*</span></label>
                            <input type="text"
                                   x-model="registerForm.last_name"
                                   id="reg_last_name"
                                   required
                                   class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-[#BD6F22]">
                        </div>
                    </div>

                    <!-- Email -->
                    <div class="mb-4">
                        <label for="reg_email" class="block text-gray-700 mb-1 text-sm">Email Address <span class="text-red-500">*</span></label>
                        <input type="email"
                               x-model="registerForm.email"
                               id="reg_email"
                               required
                               class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-[#BD6F22]">
                    </div>

                    <!-- Birthdate and Gender -->
                    <div class="flex space-x-2 mb-4">
                        <!-- Birthdate -->
                        <div class="w-1/2"
                             x-data="birthdatePicker()"
                             x-init="init()">
                            <label for="reg_birth_date" class="block text-gray-700 mb-1 text-sm">Birthdate <span class="text-red-500">*</span></label>
                            <div class="relative">
                                <input type="text"
                                       x-model="registerForm.birth_date"
                                       @click="togglePicker"
                                       id="reg_birth_date"
                                       readonly
                                       required
                                       placeholder="MM/DD/YYYY"
                                       class="w-full px-3 py-2 pr-10 border rounded-lg focus:outline-none focus:ring-2 focus:ring-[#BD6F22] cursor-pointer bg-white">
                                <svg @click="togglePicker"
                                     class="absolute right-2 top-1/2 transform -translate-y-1/2 w-5 h-5 text-gray-400 cursor-pointer"
                                     fill="none"
                                     stroke="currentColor"
                                     viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                </svg>

                                <!-- Age Validation Error -->
                                <div x-show="ageError"
                                     x-transition
                                     class="absolute left-0 top-full mt-1 text-xs text-red-600 whitespace-nowrap z-10"
                                     x-text="ageError"
                                     x-cloak>
                                </div>

                                <!-- Date Picker Dropdown - Specialized for Register Modal -->
                                <div x-show="showPicker"
                                     @click.away="closePicker"
                                     x-transition:enter="transition ease-out duration-200"
                                     x-transition:enter-start="opacity-0 scale-95"
                                     x-transition:enter-end="opacity-100 scale-100"
                                     x-transition:leave="transition ease-in duration-150"
                                     x-transition:leave-start="opacity-100 scale-100"
                                     x-transition:leave-end="opacity-0 scale-95"
                                     class="absolute left-0 top-full mt-2 z-[99999] bg-white border-2 border-[#BD6F22] rounded-xl shadow-2xl p-3 w-[280px]"
                                     x-cloak>

                                    <!-- Header: Month/Year Navigation -->
                                    <div class="flex items-center justify-between mb-3">
                                        <button type="button"
                                                @click="navigateToPreviousMonth"
                                                class="p-1.5 hover:bg-gray-100 rounded-lg transition flex-shrink-0"
                                                aria-label="Previous month">
                                            <svg class="w-4 h-4 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                                            </svg>
                                        </button>

                                        <div class="flex items-center gap-1.5 flex-1 justify-center">
                                            <select x-model.number="pickerMonth"
                                                    class="px-2 py-1 border border-gray-300 rounded-lg text-xs sm:text-sm font-medium focus:outline-none focus:ring-2 focus:ring-[#BD6F22] flex-shrink-0">
                                                <template x-for="(month, index) in months" :key="index">
                                                    <option :value="index" x-text="month"></option>
                                                </template>
                                            </select>

                                            <input type="number"
                                                   x-model.number="pickerYear"
                                                   :min="minYear"
                                                   :max="maxYear"
                                                   class="w-16 sm:w-20 px-2 py-1 border border-gray-300 rounded-lg text-xs sm:text-sm font-medium focus:outline-none focus:ring-2 focus:ring-[#BD6F22]">
                                        </div>

                                        <button type="button"
                                                @click="navigateToNextMonth"
                                                class="p-1.5 hover:bg-gray-100 rounded-lg transition flex-shrink-0"
                                                aria-label="Next month">
                                            <svg class="w-4 h-4 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                            </svg>
                                        </button>
                                    </div>

                                    <!-- Day Names Header -->
                                    <div class="grid grid-cols-7 gap-0.5 mb-1">
                                        <template x-for="day in dayNames" :key="day">
                                            <div class="text-center text-[10px] font-semibold text-gray-500 py-1" x-text="day"></div>
                                        </template>
                                    </div>

                                    <!-- Calendar Grid -->
                                    <div class="grid grid-cols-7 gap-0.5">
                                        <template x-for="(dayObj, index) in calendarDays" :key="index">
                                            <button type="button"
                                                    @click="selectDay(dayObj.day)"
                                                    :disabled="dayObj.disabled"
                                                    :class="{
                                                        'invisible': !dayObj.day,
                                                        'bg-[#BD6F22] text-white font-semibold hover:bg-[#a35718]': dayObj.isSelected,
                                                        'opacity-40 cursor-not-allowed': dayObj.isFuture,
                                                        'hover:bg-gray-100 text-gray-700': !dayObj.disabled && !dayObj.isSelected && !dayObj.isFuture,
                                                        'text-gray-800': dayObj.day && !dayObj.isSelected && !dayObj.isFuture
                                                    }"
                                                    class="w-9 h-9 rounded text-xs flex items-center justify-center transition-colors">
                                                <span x-text="dayObj.day"></span>
                                            </button>
                                        </template>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Gender -->
                        <div class="w-1/2">
                            <label for="reg_gender" class="block text-gray-700 mb-1 text-sm">Gender <span class="text-red-500">*</span></label>
                            <select x-model="registerForm.gender"
                                    id="reg_gender"
                                    required
                                    class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-[#BD6F22]">
                                <option value="" disabled>Gender</option>
                                <option value="male">Male</option>
                                <option value="female">Female</option>
                                <option value="other">Other</option>
                            </select>
                        </div>
                    </div>

                    <!-- Password Field -->
                    <div class="mb-4">
                        <label for="reg_password" class="block text-gray-700 mb-1 text-sm">Password <span class="text-red-500">*</span></label>
                        <div class="relative">
                            <input :type="showRegPassword ? 'text' : 'password'"
                                   x-model="registerForm.password"
                                   @input="validateRegisterPassword"
                                   @focus="showPasswordRules = true"
                                   id="reg_password"
                                   required
                                   class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-[#BD6F22] pr-16">
                            <button type="button"
                                    @click="showRegPassword = !showRegPassword"
                                    class="absolute right-3 top-1/2 transform -translate-y-1/2 text-sm text-[#BD6F22]"
                                    x-text="showRegPassword ? 'Hide' : 'Show'"></button>
                        </div>
                    </div>

                    <!-- Re-type Password -->
                    <div class="mb-4">
                        <label for="reg_password_confirmation" class="block text-gray-700 mb-1 text-sm">Re-type Password <span class="text-red-500">*</span></label>
                        <div class="relative">
                            <input :type="showRegConfirmPassword ? 'text' : 'password'"
                                   x-model="registerForm.password_confirmation"
                                   @input="validateRegisterPassword"
                                   id="reg_password_confirmation"
                                   required
                                   class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-[#BD6F22] pr-16">
                            <button type="button"
                                    @click="showRegConfirmPassword = !showRegConfirmPassword"
                                    class="absolute right-3 top-1/2 transform -translate-y-1/2 text-sm text-[#BD6F22]"
                                    x-text="showRegConfirmPassword ? 'Hide' : 'Show'"></button>
                        </div>
                    </div>

                    <!-- Password Rules Indicator -->
                    <div x-show="showPasswordRules && registerForm.password.length > 0"
                         class="mb-4 bg-gray-50 border rounded-lg p-3 text-sm"
                         x-cloak>
                        <ul class="space-y-1">
                            <li class="flex items-center gap-2">
                                <span class="w-2 h-2 rounded-full" :class="passwordRules.length ? 'bg-green-500' : 'bg-red-500'"></span>
                                At least 8 characters
                            </li>
                            <li class="flex items-center gap-2">
                                <span class="w-2 h-2 rounded-full" :class="passwordRules.lowercase ? 'bg-green-500' : 'bg-red-500'"></span>
                                At least one lowercase letter
                            </li>
                            <li class="flex items-center gap-2">
                                <span class="w-2 h-2 rounded-full" :class="passwordRules.uppercase ? 'bg-green-500' : 'bg-red-500'"></span>
                                At least one uppercase letter
                            </li>
                            <li class="flex items-center gap-2">
                                <span class="w-2 h-2 rounded-full" :class="passwordRules.number ? 'bg-green-500' : 'bg-red-500'"></span>
                                At least one number
                            </li>
                            <li class="flex items-center gap-2">
                                <span class="w-2 h-2 rounded-full" :class="passwordRules.special ? 'bg-green-500' : 'bg-red-500'"></span>
                                At least one special character (@$!%*#?&)
                            </li>
                            <li class="flex items-center gap-2">
                                <span class="w-2 h-2 rounded-full" :class="passwordRules.match ? 'bg-green-500' : 'bg-red-500'"></span>
                                Passwords must match
                            </li>
                        </ul>
                    </div>

                    <!-- Terms -->
                    <div class="flex items-start mb-6">
                        <input type="checkbox"
                               x-model="registerForm.terms"
                               id="reg_terms"
                               required
                               class="mt-1 mr-2 h-4 w-4 text-[#BD6F22]">
                        <label for="reg_terms" class="text-sm text-gray-700">
                            By signing up, you agree to our
                            <a href="#" class="text-[#BD6F22] hover:underline">Terms of Use</a> and
                            <a href="#" class="text-[#BD6F22] hover:underline">Privacy Policy</a>.
                            <span class="text-red-500">*</span>
                        </label>
                    </div>

                    <!-- Submit Button -->
                    <div class="flex justify-center">
                        <button type="submit"
                                :disabled="!isRegisterFormValid || registerLoading"
                                class="bg-[#BD6F22] text-white px-8 py-2 rounded-lg w-40 transition"
                                :class="(!isRegisterFormValid || registerLoading) ? 'opacity-50 cursor-not-allowed' : 'hover:bg-[#a35718]'">
                            <span x-show="!registerLoading">Sign Up</span>
                            <span x-show="registerLoading" class="flex items-center justify-center">
                                <svg class="animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                            </span>
                        </button>
                    </div>

                </form>
                </div>
            </div>
        </div>
    </div>
</div>
