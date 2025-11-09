{{--
    Complete Profile Modal Component

    Usage:
    <x-shared.complete-profile-modal
        :isIncomplete="!auth()->user()->is_profile_complete"
        :profileRoute="route('applicant.profile')"
        :settingsRoute="route('applicant.settings')"
    />

    Props:
    - isIncomplete (boolean): Whether the user's profile is incomplete
    - profileRoute (string): The route to redirect to when user clicks the button (for profile fields)
    - settingsRoute (string): The route to redirect to settings (when only active toggle is missing)
    - title (string, optional): Custom modal title. Default: "Complete Your Profile"
    - message (string, optional): Custom modal message. Default: "Please complete your profile to access all features."
    - buttonText (string, optional): Custom button text. Default: "Go to Profile"

    Note: The component automatically detects if only the active status toggle is missing and redirects to settings instead of profile.
--}}

@props([
    'isIncomplete' => false,
    'profileRoute' => '#',
    'settingsRoute' => '#',
    'title' => 'Complete Your Profile',
    'message' => 'Please complete your profile to access all features.',
    'buttonText' => 'Go to Profile'
])

<!-- Complete Profile Modal Component Loaded: isIncomplete={{ $isIncomplete ? 'true' : 'false' }} -->

@if($isIncomplete)
<!-- Complete Profile Modal Overlay -->
<div id="completeProfileModal"
     class="fixed inset-0 bg-gray-900 bg-opacity-75 flex items-center justify-center transition-opacity duration-300"
     style="display: none; z-index: 9999;">
    <div class="bg-white rounded-lg shadow-2xl max-w-md w-full mx-4 transform transition-all duration-300 scale-95 opacity-0"
         id="modalContent">
        <!-- Modal Header -->
        <div class="bg-[#BD6F22] p-6 rounded-t-lg">
            <div class="flex items-center justify-center mb-2">
                <svg class="w-16 h-16 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                </svg>
            </div>
            <h2 class="text-2xl font-bold text-white text-center">{{ $title }}</h2>
        </div>

        <!-- Modal Body -->
        <div class="p-6">
            <p class="text-gray-700 text-center mb-6 leading-relaxed">
                {{ $message }}
            </p>

            <!-- Key Information List -->
            <div class="bg-gray-50 rounded-lg p-4 mb-6">
                <h3 class="font-semibold text-gray-800 mb-3 flex items-center">
                    <svg class="w-5 h-5 mr-2 text-[#BD6F22]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    Required Information:
                </h3>
                <ul class="space-y-2 text-sm text-gray-600">
                    <li class="flex items-start">
                        <span class="text-[#BD6F22] mr-2">•</span>
                        <span>Personal Information</span>
                    </li>
                    <li class="flex items-start">
                        <span class="text-[#BD6F22] mr-2">•</span>
                        <span>Contact Details</span>
                    </li>
                    <li class="flex items-start">
                        <span class="text-[#BD6F22] mr-2">•</span>
                        <span>Preference</span>
                    </li>
                    <li class="flex items-start">
                        <span class="text-[#BD6F22] mr-2">•</span>
                        <span>Active Toggle Switch in Settings</span>
                    </li>
                </ul>
            </div>

            <!-- Action Button -->
            <button id="goToProfileBtn"
                    class="w-full bg-[#BD6F22] hover:bg-[#A65F1C] text-white font-semibold py-3 px-6 rounded-lg
                           transition duration-300 ease-in-out transform hover:scale-105 focus:outline-none
                           focus:ring-4 focus:ring-[#BD9168] focus:ring-opacity-50 shadow-lg">
                {{ $buttonText }}
            </button>
        </div>

        <!-- Modal Footer -->
        <div class="bg-gray-50 px-6 py-4 rounded-b-lg">
            <p class="text-xs text-gray-500 text-center">
                This step is required to ensure your account is fully set up.
            </p>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    console.log('Complete Profile Modal Script Loaded');

    const modal = document.getElementById('completeProfileModal');
    const modalContent = document.getElementById('modalContent');
    const goToProfileBtn = document.getElementById('goToProfileBtn');
    const profileRoute = @json($profileRoute);
    const settingsRoute = @json($settingsRoute);
    const currentUrl = window.location.href;
    const isProfilePage = currentUrl.includes('/profile');
    const isSettingsPage = currentUrl.includes('/settings');
    let allowNavigation = false; // Flag to allow navigation when button is clicked

    // Check if user data is available and determine what's missing
    const user = @json(auth()->user());
    const onlyMissingActiveStatus = user &&
        user.first_name && user.last_name && user.gender &&
        user.birth_date && user.civil_status && user.nationality &&
        user.mobile_number && user.full_address && user.province &&
        user.city && user.barangay && user.profile_picture &&
        user.job_industry &&
        user.active_status !== 'Active';

    // Determine which route to use
    const targetRoute = onlyMissingActiveStatus ? settingsRoute : profileRoute;
    console.log('Only missing active status:', onlyMissingActiveStatus);
    console.log('Target route:', targetRoute);

    // Update button text based on what's missing
    if (onlyMissingActiveStatus) {
        goToProfileBtn.textContent = 'Go to Settings';
    }

    console.log('Modal Elements:', {
        modal: !!modal,
        modalContent: !!modalContent,
        goToProfileBtn: !!goToProfileBtn,
        profileRoute: profileRoute,
        currentUrl: currentUrl,
        isProfilePage: isProfilePage,
        isSettingsPage: isSettingsPage
    });

    // Show modal with animation
    function showModal() {
        if (!modal) {
            console.error('Modal element not found!');
            return;
        }
        console.log('showModal() called - setting display to flex');
        modal.style.display = 'flex';
        modal.style.visibility = 'visible';
        document.body.classList.add('modal-open');
        console.log('Modal display set, waiting for animation...');
        // Trigger animation after display is set
        setTimeout(() => {
            modal.classList.add('opacity-100');
            modalContent.classList.remove('scale-95', 'opacity-0');
            modalContent.classList.add('scale-100', 'opacity-100');
            console.log('Modal animation complete, should be visible now');
        }, 10);
    }

    // Hide modal with animation
    function hideModal() {
        modal.classList.remove('opacity-100');
        modalContent.classList.remove('scale-100', 'opacity-100');
        modalContent.classList.add('scale-95', 'opacity-0');
        setTimeout(() => {
            modal.style.display = 'none';
            document.body.classList.remove('modal-open');
        }, 300);
    }

    // Prevent all navigation attempts
    function preventNavigation(e) {
        // Only redirect to profile if not already on profile or settings page
        if (!isProfilePage && !isSettingsPage) {
            e.preventDefault();
            e.stopPropagation();

            // Show shake animation
            modalContent.classList.add('animate-shake');
            setTimeout(() => {
                modalContent.classList.remove('animate-shake');
            }, 500);

            return false;
        }
    }

    // Block all link clicks except profile and settings links
    document.addEventListener('click', function(e) {
        const link = e.target.closest('a');
        if (link && !isProfilePage && !isSettingsPage) {
            const href = link.getAttribute('href');
            // Allow only profile and settings routes
            if (!href || (!href.includes('/profile') && !href.includes('/settings'))) {
                preventNavigation(e);
            }
        }
    }, true);

    // Block form submissions
    document.addEventListener('submit', function(e) {
        if (!isProfilePage && !isSettingsPage) {
            preventNavigation(e);
        }
    }, true);

    // Prevent back/forward browser navigation
    if (!isProfilePage && !isSettingsPage) {
        window.history.pushState(null, '', window.location.href);
        window.addEventListener('popstate', function(e) {
            window.history.pushState(null, '', window.location.href);
            modalContent.classList.add('animate-shake');
            setTimeout(() => {
                modalContent.classList.remove('animate-shake');
            }, 500);
        });
    }

    // Prevent keyboard shortcuts for navigation
    document.addEventListener('keydown', function(e) {
        // Block: ESC, F5, Ctrl+R, Ctrl+W, Alt+Left, Alt+Right, Backspace
        const blockedKeys = [
            27,  // ESC
            116, // F5
            (e.ctrlKey && e.keyCode === 82), // Ctrl+R
            (e.ctrlKey && e.keyCode === 87), // Ctrl+W
            (e.altKey && e.keyCode === 37),  // Alt+Left
            (e.altKey && e.keyCode === 39),  // Alt+Right
            (e.keyCode === 8 && !isInputField(e.target)) // Backspace (not in input)
        ];

        if (!isProfilePage && !isSettingsPage && blockedKeys.some(key => key === true || key === e.keyCode)) {
            e.preventDefault();
            e.stopPropagation();
            modalContent.classList.add('animate-shake');
            setTimeout(() => {
                modalContent.classList.remove('animate-shake');
            }, 500);
            return false;
        }
    }, true);

    // Helper function to check if element is an input field
    function isInputField(element) {
        const tagName = element.tagName.toLowerCase();
        return tagName === 'input' || tagName === 'textarea' || element.isContentEditable;
    }

    // Prevent closing modal by clicking outside (mandatory modal)
    modal.addEventListener('click', function (e) {
        if (e.target === modal) {
            // Add shake animation to indicate it's mandatory
            modalContent.classList.add('animate-shake');
            setTimeout(() => {
                modalContent.classList.remove('animate-shake');
            }, 500);
        }
    });

    // Handle button click - allow navigation to profile or settings
    goToProfileBtn.addEventListener('click', function () {
        // Mark as intentional navigation - disable beforeunload warning
        allowNavigation = true;
        sessionStorage.setItem('allowProfileNavigation', 'true');
        window.location.href = targetRoute;
    });

    // Show modal on page load if not on profile or settings page
    if (!isProfilePage && !isSettingsPage) {
        console.log('Showing modal - not on profile or settings page');
        showModal();
    } else {
        console.log('Not showing modal - on profile or settings page');
    }

    // Detect if user is trying to navigate away (not reload)
    let isNavigatingAway = false;

    // Track navigation attempts through links
    document.addEventListener('click', function(e) {
        const link = e.target.closest('a');
        if (link && !isProfilePage && !isSettingsPage) {
            const href = link.getAttribute('href');
            // Mark as navigating away if clicking non-profile and non-settings links
            if (href && !href.includes('/profile') && !href.includes('/settings')) {
                isNavigatingAway = true;
            }
        }
    }, true);

    // Only show warning when navigating away, not on reload
    window.addEventListener('beforeunload', function (e) {
        // Don't show warning if:
        // - User is on profile or settings page
        // - Navigation is explicitly allowed (going to profile)
        // - User is just reloading the page (not navigating away)
        if (!isProfilePage && !isSettingsPage && !allowNavigation && isNavigatingAway) {
            e.preventDefault();
            e.returnValue = 'You must complete your profile before accessing other pages.';
            return e.returnValue;
        }
    });
});
</script>

<style>
@keyframes shake {
    0%, 100% { transform: translateX(0) scale(1); }
    25% { transform: translateX(-10px) scale(1); }
    75% { transform: translateX(10px) scale(1); }
}

.animate-shake {
    animation: shake 0.5s ease-in-out;
}

/* Smooth transitions */
#completeProfileModal {
    /* opacity: 0; Removed - causing display issue */
}

#modalContent {
    transition: transform 0.3s ease-out, opacity 0.3s ease-out;
}

/* Prevent body scroll when modal is open */
body.modal-open {
    overflow: hidden;
}
</style>
@endif
