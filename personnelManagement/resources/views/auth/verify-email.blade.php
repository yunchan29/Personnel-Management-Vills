<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verify Email - VillsPMS</title>

    <!-- Favicon -->
    <link rel="icon" href="{{ asset('images/villslogo3.png') }}" type="image/png">

    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Barlow+Condensed:wght@500;700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Alata&display=swap" rel="stylesheet">

    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <!-- Axios -->
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>

    <style>
        .barlow-condensed {
            font-family: 'Barlow Condensed', sans-serif;
        }

        .font-alata {
            font-family: 'Alata', sans-serif;
        }

        [x-cloak] {
            display: none !important;
        }

        @keyframes float {
            0%, 100% {
                transform: translateY(0px);
            }
            50% {
                transform: translateY(-20px);
            }
        }

        @keyframes pulse-slow {
            0%, 100% {
                opacity: 1;
            }
            50% {
                opacity: 0.6;
            }
        }

        .float-animation {
            animation: float 6s ease-in-out infinite;
        }

        .pulse-slow {
            animation: pulse-slow 3s ease-in-out infinite;
        }

        .gradient-bg {
            background: linear-gradient(135deg, #BD9168 0%, #A06E45 50%, #8B5A3C 100%);
        }

        .glass-effect {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
        }

        /* Code input styling */
        .code-input {
            width: 3.5rem;
            height: 3.5rem;
            text-align: center;
            font-size: 1.5rem;
            font-weight: bold;
            border: 2px solid #e5e7eb;
            border-radius: 0.5rem;
            transition: all 0.3s;
        }

        .code-input:focus {
            border-color: #BD9168;
            outline: none;
            box-shadow: 0 0 0 3px rgba(189, 145, 104, 0.1);
        }

        .code-input::-webkit-outer-spin-button,
        .code-input::-webkit-inner-spin-button {
            -webkit-appearance: none;
            margin: 0;
        }
    </style>
</head>
<body class="gradient-bg min-h-screen flex items-center justify-center p-4 font-alata">
    <!-- Decorative Background Elements -->
    <div class="absolute inset-0 overflow-hidden pointer-events-none">
        <div class="absolute top-20 left-10 w-64 h-64 bg-white opacity-5 rounded-full blur-3xl"></div>
        <div class="absolute bottom-20 right-10 w-96 h-96 bg-white opacity-5 rounded-full blur-3xl"></div>
    </div>

    <div x-data="{
        resending: false,
        verifying: false,
        code: ['', '', '', '', '', ''],
        errors: {},
        message: '',
        messageType: 'success',
        email: '{{ session('verification_email') }}',

        focusNext(index) {
            if (index < 5 && this.code[index]) {
                this.$refs['input' + (index + 1)].focus();
            }
        },

        focusPrev(index, event) {
            if (event.key === 'Backspace' && !this.code[index] && index > 0) {
                this.$refs['input' + (index - 1)].focus();
            }
        },

        pasteCode(event) {
            const paste = (event.clipboardData || window.clipboardData).getData('text');
            const digits = paste.replace(/\D/g, '').slice(0, 6);

            for (let i = 0; i < digits.length; i++) {
                this.code[i] = digits[i];
            }

            if (digits.length === 6) {
                this.$refs.input5.focus();
            }
        },

        async verifyCode() {
            this.verifying = true;
            this.errors = {};
            this.message = '';

            const codeString = this.code.join('');

            if (codeString.length !== 6) {
                this.errors = { code: ['Please enter all 6 digits.'] };
                this.verifying = false;
                return;
            }

            try {
                const response = await axios.post('{{ route('verification.verify') }}', {
                    email: this.email,
                    code: codeString
                }, {
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json',
                        'Content-Type': 'application/json'
                    }
                });

                if (response.data.success) {
                    this.messageType = 'success';
                    this.message = response.data.message;
                    setTimeout(() => {
                        window.location.href = response.data.redirect;
                    }, 1500);
                }
            } catch (error) {
                this.verifying = false;
                if (error.response && error.response.data) {
                    this.errors = error.response.data.errors || {};
                    this.messageType = 'error';
                    this.message = error.response.data.message || 'Verification failed.';
                }
            }
        },

        async resendCode() {
            this.resending = true;
            this.errors = {};
            this.message = '';

            try {
                const response = await axios.post('{{ route('verification.resend') }}', {
                    email: this.email
                }, {
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json',
                        'Content-Type': 'application/json'
                    }
                });

                if (response.data.success) {
                    this.messageType = 'success';
                    this.message = response.data.message;
                    this.code = ['', '', '', '', '', ''];
                    this.$refs.input0.focus();
                }
            } catch (error) {
                console.error('Resend error:', error);
                this.messageType = 'error';
                this.message = error.response?.data?.message || 'Failed to resend code.';
            } finally {
                this.resending = false;
            }
        }
    }"
         class="relative w-full max-w-lg">

        <!-- Main Card -->
        <div class="glass-effect rounded-2xl shadow-2xl overflow-hidden">

            <!-- Header Section with Icon -->
            <div class="bg-gradient-to-r from-[#BD9168] to-[#A06E45] p-8 text-center relative">
                <div class="absolute top-0 left-0 w-full h-full opacity-10">
                    <svg class="w-full h-full" viewBox="0 0 100 100" preserveAspectRatio="none">
                        <path d="M0,0 L100,0 L100,100 Q50,80 0,100 Z" fill="white"/>
                    </svg>
                </div>

                <!-- Email Icon with Animation -->
                <div class="relative mx-auto w-24 h-24 mb-4 float-animation">
                    <div class="absolute inset-0 bg-white rounded-full opacity-20 pulse-slow"></div>
                    <div class="relative bg-white rounded-full w-24 h-24 flex items-center justify-center shadow-lg">
                        <svg class="w-12 h-12 text-[#BD9168]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                        </svg>
                    </div>
                </div>

                <h1 class="text-3xl font-bold text-white barlow-condensed uppercase tracking-wider">
                    Verify Your Email
                </h1>
                <p class="text-white text-opacity-90 mt-2 text-sm">
                    Enter the 6-digit code we sent to your email
                </p>
            </div>

            <!-- Content Section -->
            <div class="p-8">

                <!-- Success/Error Message -->
                <div x-show="message"
                     x-transition:enter="transition ease-out duration-300"
                     x-transition:enter-start="opacity-0 transform scale-90"
                     x-transition:enter-end="opacity-100 transform scale-100"
                     x-cloak
                     :class="messageType === 'success' ? 'bg-green-50 border-green-500 text-green-800' : 'bg-red-50 border-red-500 text-red-800'"
                     class="mb-6 p-4 border-l-4 rounded-lg">
                    <div class="flex items-start">
                        <svg x-show="messageType === 'success'" class="w-6 h-6 mr-3 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                        </svg>
                        <svg x-show="messageType === 'error'" class="w-6 h-6 mr-3 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                        </svg>
                        <p class="font-medium" x-text="message"></p>
                    </div>
                </div>

                <!-- Email Display -->
                <div class="bg-gradient-to-r from-[#BD9168] to-[#A06E45] rounded-xl p-4 mb-6 shadow-md">
                    <div class="flex items-center">
                        <div class="bg-white rounded-full p-2 mr-3">
                            <svg class="w-5 h-5 text-[#BD9168]" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M2.003 5.884L10 9.882l7.997-3.998A2 2 0 0016 4H4a2 2 0 00-1.997 1.884z"></path>
                                <path d="M18 8.118l-8 4-8-4V14a2 2 0 002 2h12a2 2 0 002-2V8.118z"></path>
                            </svg>
                        </div>
                        <div class="flex-1">
                            <p class="text-xs text-white text-opacity-75 uppercase tracking-wide">Sent to</p>
                            <p class="text-white font-semibold break-all" x-text="email"></p>
                        </div>
                    </div>
                </div>

                <!-- Code Input -->
                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-3 text-center">
                        Enter Verification Code
                    </label>
                    <div class="flex justify-center gap-2 mb-2">
                        <input
                            type="text"
                            inputmode="numeric"
                            maxlength="1"
                            x-model="code[0]"
                            x-ref="input0"
                            @input="focusNext(0)"
                            @keydown="focusPrev(0, $event)"
                            @paste="pasteCode($event)"
                            class="code-input"
                            :class="errors.code ? 'border-red-500' : ''"
                            @keyup.enter="verifyCode()"
                        />
                        <input
                            type="text"
                            inputmode="numeric"
                            maxlength="1"
                            x-model="code[1]"
                            x-ref="input1"
                            @input="focusNext(1)"
                            @keydown="focusPrev(1, $event)"
                            @paste="pasteCode($event)"
                            class="code-input"
                            :class="errors.code ? 'border-red-500' : ''"
                            @keyup.enter="verifyCode()"
                        />
                        <input
                            type="text"
                            inputmode="numeric"
                            maxlength="1"
                            x-model="code[2]"
                            x-ref="input2"
                            @input="focusNext(2)"
                            @keydown="focusPrev(2, $event)"
                            @paste="pasteCode($event)"
                            class="code-input"
                            :class="errors.code ? 'border-red-500' : ''"
                            @keyup.enter="verifyCode()"
                        />
                        <input
                            type="text"
                            inputmode="numeric"
                            maxlength="1"
                            x-model="code[3]"
                            x-ref="input3"
                            @input="focusNext(3)"
                            @keydown="focusPrev(3, $event)"
                            @paste="pasteCode($event)"
                            class="code-input"
                            :class="errors.code ? 'border-red-500' : ''"
                            @keyup.enter="verifyCode()"
                        />
                        <input
                            type="text"
                            inputmode="numeric"
                            maxlength="1"
                            x-model="code[4]"
                            x-ref="input4"
                            @input="focusNext(4)"
                            @keydown="focusPrev(4, $event)"
                            @paste="pasteCode($event)"
                            class="code-input"
                            :class="errors.code ? 'border-red-500' : ''"
                            @keyup.enter="verifyCode()"
                        />
                        <input
                            type="text"
                            inputmode="numeric"
                            maxlength="1"
                            x-model="code[5]"
                            x-ref="input5"
                            @input="focusNext(5)"
                            @keydown="focusPrev(5, $event)"
                            @paste="pasteCode($event)"
                            class="code-input"
                            :class="errors.code ? 'border-red-500' : ''"
                            @keyup.enter="verifyCode()"
                        />
                    </div>
                    <p x-show="errors.code" x-text="errors.code ? errors.code[0] : ''" class="text-red-600 text-sm text-center mt-2" x-cloak></p>
                </div>

                <!-- Verify Button -->
                <button @click="verifyCode()"
                        x-bind:disabled="verifying"
                        class="w-full bg-gradient-to-r from-[#BD9168] to-[#A06E45] hover:from-[#A06E45] hover:to-[#8B5A3C] text-white font-semibold py-4 px-6 rounded-xl transition duration-300 transform hover:scale-[1.02] disabled:opacity-50 disabled:cursor-not-allowed disabled:transform-none shadow-lg hover:shadow-xl flex items-center justify-center barlow-condensed text-lg uppercase tracking-wide mb-4">
                    <svg x-show="verifying" x-cloak class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    <span x-show="!verifying">Verify Email</span>
                    <span x-show="verifying" x-cloak>Verifying...</span>
                </button>

                <!-- Instructions -->
                <div class="bg-blue-50 border-l-4 border-blue-400 p-4 mb-6 rounded-lg">
                    <div class="flex items-start">
                        <svg class="w-5 h-5 text-blue-400 mr-3 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                        </svg>
                        <div class="text-sm text-blue-800">
                            <p class="font-medium mb-1">Didn't receive the code?</p>
                            <p class="text-blue-700">Check your spam folder or click below to resend.</p>
                            <p class="text-blue-700 mt-1">Code expires in 15 minutes.</p>
                        </div>
                    </div>
                </div>

                <!-- Divider -->
                <div class="relative mb-4">
                    <div class="absolute inset-0 flex items-center">
                        <div class="w-full border-t border-gray-300"></div>
                    </div>
                    <div class="relative flex justify-center text-sm">
                        <span class="px-4 bg-white text-gray-500">or</span>
                    </div>
                </div>

                <!-- Resend Button -->
                <button @click="resendCode()"
                        x-bind:disabled="resending"
                        class="w-full bg-gray-100 hover:bg-gray-200 text-gray-700 font-semibold py-3 px-6 rounded-xl transition duration-200 flex items-center justify-center group mb-4">
                    <svg x-show="resending" x-cloak class="animate-spin -ml-1 mr-3 h-5 w-5" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    <svg x-show="!resending" class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                    </svg>
                    <span x-show="!resending">Resend Code</span>
                    <span x-show="resending" x-cloak>Sending...</span>
                </button>

                <!-- Back to Login -->
                <div class="text-center">
                    <a href="{{ route('login') }}" class="text-sm text-gray-600 hover:text-[#BD9168] underline">
                        Back to Login
                    </a>
                </div>
            </div>

            <!-- Footer -->
            <div class="bg-gray-50 px-8 py-4 border-t border-gray-200">
                <p class="text-xs text-gray-500 text-center">
                    Need help? Contact support at
                    <a href="mailto:{{ env('MAIL_FROM_ADDRESS') }}" class="text-[#BD9168] hover:underline">{{ env('MAIL_FROM_ADDRESS') }}</a>
                </p>
            </div>
        </div>
    </div>
</body>
</html>
