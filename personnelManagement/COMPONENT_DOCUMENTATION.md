# Shared Components Documentation

This document describes the shared components created to consolidate duplicate code across the Personnel Management System.

## Overview

The consolidation effort reduced ~2,000+ lines of duplicate code by creating reusable components and utilities. All layouts now extend a base layout and use shared components for navigation, forms, and UI elements.

---

## Table of Contents

1. [Layout Components](#layout-components)
2. [Navigation Components](#navigation-components)
3. [Form Components](#form-components)
4. [UI Components](#ui-components)
5. [JavaScript Utilities](#javascript-utilities)
6. [Tailwind Configuration](#tailwind-configuration)

---

## Layout Components

### Base Layout (`layouts/base.blade.php`)

The base layout provides the common HTML structure used by all role-specific layouts.

**Features:**
- Tailwind CSS configuration with brand colors
- Alpine.js integration
- Google Fonts (Alata)
- Custom animations (checkmark, progress bar)
- Sections for navbar, sidebar, and content
- Stack support for styles, scripts, and modals

**Usage:**
```blade
@extends('layouts.base', ['title' => 'Page Title'])

@section('navbar')
    <x-shared.navbar />
@endsection

@section('sidebar')
    <x-shared.sidebar :items="$menuItems" />
@endsection

@section('content')
    <!-- Your page content -->
@endsection
```

### Role-Specific Layouts

All role layouts now extend the base layout and define their menu items:

- `layouts/hrAdmin.blade.php` - HR Admin dashboard
- `layouts/hrStaff.blade.php` - HR Staff dashboard
- `layouts/employeeHome.blade.php` - Employee dashboard
- `layouts/applicantHome.blade.php` - Applicant dashboard

**Example (hrAdmin.blade.php):**
```blade
@extends('layouts.base', ['title' => $title ?? 'HR Admin Dashboard'])

@php
    $menuItems = [
        ['img' => 'home.png', 'label' => 'Home', 'route' => 'hrAdmin.dashboard'],
        ['img' => 'search.png', 'label' => 'Job Posting', 'route' => 'hrAdmin.jobPosting'],
        // ... more items
    ];
@endphp

@section('navbar')
    <x-shared.navbar :showRoleText="true" />
@endsection

@section('sidebar')
    <x-shared.sidebar :items="$menuItems" :currentRoute="Route::currentRouteName()" />
@endsection
```

---

## Navigation Components

### Navbar (`components/shared/navbar.blade.php`)

Unified navigation bar with logo, datetime, and user dropdown.

**Props:**
- `showRoleText` (boolean, default: true) - Whether to display the user's role text

**Features:**
- Displays company logo and name
- Real-time date/time display
- User profile picture with dropdown
- Profile and logout links
- Automatic role detection and routing

**Usage:**
```blade
<!-- Show role text -->
<x-shared.navbar :showRoleText="true" />

<!-- Hide role text (for employee/applicant) -->
<x-shared.navbar :showRoleText="false" />
```

### Sidebar (`components/shared/sidebar.blade.php`)

Collapsible sidebar with responsive design (desktop vertical menu, mobile bottom navigation).

**Props:**
- `items` (array, required) - Menu items array
- `currentRoute` (string, optional) - Current route name for active state
- `showTooltips` (boolean, default: false) - Show tooltips when sidebar is collapsed

**Menu Item Structure:**
```php
[
    'img' => 'icon.png',        // Icon filename in /images/
    'label' => 'Menu Label',    // Display text
    'route' => 'route.name'     // Laravel route name
]
```

**Usage:**
```blade
@php
    $menuItems = [
        ['img' => 'home.png', 'label' => 'Home', 'route' => 'dashboard'],
        ['img' => 'user.png', 'label' => 'Profile', 'route' => 'profile'],
    ];
@endphp

<x-shared.sidebar
    :items="$menuItems"
    :currentRoute="Route::currentRouteName()"
    :showTooltips="true"
/>
```

---

## Form Components

### Text Input (`components/shared/form/text-input.blade.php`)

Standard text input field with label, validation, and error display.

**Props:**
- `label` (string) - Input label
- `name` (string, required) - Input name attribute
- `value` (string) - Default value
- `type` (string, default: 'text') - Input type
- `required` (boolean) - Required field
- `disabled` (boolean) - Disabled state
- `placeholder` (string) - Placeholder text
- `pattern` (string) - Validation pattern
- `maxlength` (int) - Maximum length
- `title` (string) - Tooltip text
- `error` (string) - Custom error message

**Usage:**
```blade
<x-shared.form.text-input
    label="First Name"
    name="first_name"
    :value="old('first_name', $user->first_name)"
    required
    placeholder="Enter first name"
/>

<!-- With pattern validation -->
<x-shared.form.text-input
    label="Mobile Number"
    name="mobile_number"
    pattern="^09\d{9}$"
    maxlength="11"
    title="Enter a valid 11-digit mobile number starting with 09"
    required
/>
```

### Select Input (`components/shared/form/select-input.blade.php`)

Dropdown select field with label and error display.

**Props:**
- `label` (string) - Input label
- `name` (string, required) - Input name attribute
- `value` (string) - Selected value
- `options` (array, required) - Options array
- `required` (boolean) - Required field
- `disabled` (boolean) - Disabled state
- `placeholder` (string) - Placeholder option text
- `error` (string) - Custom error message

**Usage:**
```blade
<x-shared.form.select-input
    label="Gender"
    name="gender"
    :value="old('gender', $user->gender)"
    :options="['Male' => 'Male', 'Female' => 'Female', 'Other' => 'Other']"
    required
/>

<!-- With associative array -->
@php
    $provinces = ['01' => 'Ilocos Norte', '02' => 'Ilocos Sur', ...];
@endphp

<x-shared.form.select-input
    label="Province"
    name="province"
    :options="$provinces"
    placeholder="Select province"
/>
```

### Textarea Input (`components/shared/form/textarea-input.blade.php`)

Multi-line text area with label and error display.

**Props:**
- `label` (string) - Input label
- `name` (string, required) - Input name attribute
- `value` (string) - Default value
- `rows` (int, default: 3) - Number of rows
- `required` (boolean) - Required field
- `disabled` (boolean) - Disabled state
- `placeholder` (string) - Placeholder text
- `maxlength` (int) - Maximum length
- `error` (string) - Custom error message

**Usage:**
```blade
<x-shared.form.textarea-input
    label="Description"
    name="description"
    :value="old('description')"
    rows="5"
    maxlength="500"
    placeholder="Enter description"
/>
```

### Date Input (`components/shared/form/date-input.blade.php`)

Date picker input with label and validation.

**Props:**
- `label` (string) - Input label
- `name` (string, required) - Input name attribute
- `value` (string) - Default date value
- `required` (boolean) - Required field
- `disabled` (boolean) - Disabled state
- `min` (string) - Minimum date
- `max` (string) - Maximum date
- `error` (string) - Custom error message

**Usage:**
```blade
<x-shared.form.date-input
    label="Birth Date"
    name="birth_date"
    :value="old('birth_date', $user->birth_date)"
    :max="now()->subYears(18)->format('Y-m-d')"
    required
/>

<!-- With dynamic min date -->
<x-shared.form.date-input
    label="Interview Date"
    name="interview_date"
    x-model="interviewDate"
    :min="date('Y-m-d')"
/>
```

---

## UI Components

### Modal (`components/shared/modal.blade.php`)

Reusable modal dialog with backdrop and close button.

**Props:**
- `show` (string, default: 'showModal') - Alpine.js show variable
- `title` (string) - Modal title
- `maxWidth` (string, default: 'max-w-md') - Maximum width class
- `closeButton` (boolean, default: true) - Show close button

**Slots:**
- Default slot - Modal content
- `dynamicTitle` - Dynamic title slot (for complex titles)
- `footer` - Modal footer (typically for buttons)

**Usage:**
```blade
<!-- Simple modal -->
<x-shared.modal show="showConfirmModal" title="Confirm Action">
    <p>Are you sure you want to proceed?</p>

    <x-slot:footer>
        <button @click="showConfirmModal = false">Cancel</button>
        <button @click="confirm()">Confirm</button>
    </x-slot:footer>
</x-shared.modal>

<!-- Modal with dynamic title -->
<x-shared.modal show="showInterviewModal" maxWidth="max-w-lg">
    <x-slot:dynamicTitle>
        <template x-if="mode === 'single'">
            <span>Set Interview for <span x-text="applicant.name"></span></span>
        </template>
    </x-slot:dynamicTitle>

    <!-- Modal content -->
</x-shared.modal>
```

### Loading Spinner (`components/shared/loading-spinner.blade.php`)

Animated loading spinner SVG.

**Props:**
- `size` (string, default: 'h-4 w-4') - Size classes
- `color` (string, default: 'text-white') - Color class

**Usage:**
```blade
<!-- Default (small white spinner) -->
<x-shared.loading-spinner />

<!-- Large primary color spinner -->
<x-shared.loading-spinner size="h-8 w-8" color="text-brand-primary" />

<!-- Inside button -->
<button class="flex items-center gap-2">
    <x-shared.loading-spinner />
    <span>Loading...</span>
</button>
```

### Loading Overlay (`components/shared/loading-overlay.blade.php`)

Full-screen loading overlay that appears during navigation and form submissions.

**Props:**
- `id` (string, default: 'loading-overlay') - DOM element ID

**Features:**
- Automatically shows on form submission
- Automatically shows on link navigation (except anchors, external, new tabs)
- Can be manually triggered via JavaScript

**Usage:**
```blade
<!-- Add to layout -->
@push('modals')
    <x-shared.loading-overlay />
@endpush

<!-- Manual control -->
<script>
    document.getElementById('loading-overlay').classList.remove('hidden');
    // Do work...
    document.getElementById('loading-overlay').classList.add('hidden');
</script>
```

### Button (`components/shared/button.blade.php`)

Styled button with loading state support.

**Props:**
- `variant` (string, default: 'primary') - Button style variant
  - `primary` - Orange/brown primary button
  - `secondary` - Dark brown secondary button
  - `danger` - Red danger button
  - `outline` - Outlined button
- `loading` (string/boolean) - Alpine.js loading variable name
- `loadingText` (string, default: 'Processing...') - Text shown during loading
- `type` (string, default: 'button') - Button type attribute

**Usage:**
```blade
<!-- Primary button -->
<x-shared.button @click="submit" :loading="loading">
    Submit
</x-shared.button>

<!-- Danger button with custom loading text -->
<x-shared.button
    variant="danger"
    :loading="deleting"
    loadingText="Deleting..."
    @click="deleteItem"
>
    Delete
</x-shared.button>

<!-- Form submit button -->
<x-shared.button type="submit" variant="secondary">
    Save Changes
</x-shared.button>
```

### Status Badge (`components/shared/status-badge.blade.php`)

Colored badge for displaying status labels.

**Props:**
- `status` (string, required) - Status key
- `label` (string, optional) - Custom label (overrides default)

**Supported Statuses:**
- `pending` - Gray
- `for_interview` - Yellow
- `interviewed` - Green (labeled "Passed")
- `declined` - Red (labeled "Failed")
- `for_training` - Blue
- `trained` - Indigo
- `hired` - Dark green
- `approved` - Green
- `rejected` - Red
- `active` - Green
- `inactive` - Gray

**Usage:**
```blade
<!-- Default status label -->
<x-shared.status-badge status="for_interview" />
<!-- Displays: "For Interview" in yellow -->

<!-- Custom label -->
<x-shared.status-badge status="interviewed" label="Interview Passed" />
<!-- Displays: "Interview Passed" in green -->

<!-- Dynamic status -->
<x-shared.status-badge :status="$application->status" />
```

---

## JavaScript Utilities

All JavaScript utilities are located in `public/js/utils/` and use ES6 module syntax.

### Checkbox Utils (`checkboxUtils.js`)

Utilities for managing bulk checkbox selection.

**Functions:**

#### `toggleSelectAll(event, selectedArray, checkboxSelector, idField)`
Toggles all visible checkboxes.

**Parameters:**
- `event` - Checkbox change event
- `selectedArray` - Array to store selected items
- `checkboxSelector` (default: '.applicant-checkbox') - CSS selector
- `idField` (default: 'application_id') - ID field name

**Returns:** Updated selected array

**Usage:**
```javascript
import { CheckboxUtils } from './utils/checkboxUtils.js';

Alpine.data('myHandler', () => ({
    selectedItems: [],

    toggleSelectAll(event) {
        this.selectedItems = CheckboxUtils.toggleSelectAll(
            event,
            this.selectedItems,
            '.item-checkbox',
            'id'
        );
        this.updateMasterCheckbox();
    }
}));
```

#### `updateMasterCheckbox(rootElement, selectedArray, checkboxSelector, masterRefName, idField)`
Updates master checkbox state (checked, unchecked, or indeterminate).

**Usage:**
```javascript
updateMasterCheckbox() {
    CheckboxUtils.updateMasterCheckbox(
        this.$root,
        this.selectedItems,
        '.item-checkbox',
        'masterCheckbox',
        'id'
    );
}
```

#### `toggleItem(event, selectedArray, idField)`
Toggles a single checkbox item.

**Usage:**
```javascript
toggleItem(event) {
    this.selectedItems = CheckboxUtils.toggleItem(
        event,
        this.selectedItems,
        'id'
    );
    this.updateMasterCheckbox();
}
```

### Time Utils (`timeUtils.js`)

Utilities for time conversion and formatting.

**Functions:**

#### `to24h(hour, period)`
Converts 12-hour time to 24-hour format.

**Usage:**
```javascript
import { TimeUtils } from './utils/timeUtils.js';

const hour24 = TimeUtils.to24h(2, 'PM'); // Returns: 14
const midnight = TimeUtils.to24h(12, 'AM'); // Returns: 0
const noon = TimeUtils.to24h(12, 'PM'); // Returns: 12
```

#### `to12h(hour24)`
Converts 24-hour time to 12-hour format.

**Returns:** `{ hour12, period }`

**Usage:**
```javascript
const { hour12, period } = TimeUtils.to12h(14);
// Returns: { hour12: 2, period: 'PM' }
```

#### `formatDisplay(hour, period)`
Formats time for display.

**Usage:**
```javascript
const display = TimeUtils.formatDisplay(2, 'PM');
// Returns: "2:00 PM"
```

#### `getPeriodFromHour(hour)`
Gets AM/PM period based on hour.

**Usage:**
```javascript
const period = TimeUtils.getPeriodFromHour(10); // Returns: "AM"
```

### API Utils (`apiUtils.js`)

Utilities for API calls with CSRF token handling.

**Functions:**

#### `getCsrfToken()`
Gets CSRF token from meta tag.

**Throws:** Error if token not found

**Usage:**
```javascript
import { ApiUtils } from './utils/apiUtils.js';

const token = ApiUtils.getCsrfToken();
```

#### `fetchWithCsrf(url, options)`
Makes a fetch request with automatic CSRF token inclusion.

**Usage:**
```javascript
const response = await ApiUtils.fetchWithCsrf('/api/endpoint', {
    method: 'POST',
    body: { name: 'John' }
});
```

#### `post(url, data)`, `put(url, data)`, `delete(url, data)`
Convenience methods for common HTTP methods.

**Usage:**
```javascript
// POST request
const response = await ApiUtils.post('/api/users', {
    name: 'John Doe',
    email: 'john@example.com'
});

// PUT request
await ApiUtils.put('/api/users/1', { name: 'Jane Doe' });

// DELETE request
await ApiUtils.delete('/api/users/1');
```

#### `handleResponse(response)`
Handles API response and extracts JSON.

**Usage:**
```javascript
try {
    const response = await ApiUtils.post('/api/endpoint', data);
    const result = await ApiUtils.handleResponse(response);
    console.log(result);
} catch (error) {
    console.error('API error:', error.message);
}
```

#### `showFeedback(component, message, duration)`
Displays a feedback message.

**Usage:**
```javascript
Alpine.data('myComponent', () => ({
    feedbackMessage: '',
    feedbackVisible: false,

    async submitForm() {
        try {
            const response = await ApiUtils.post('/api/submit', this.formData);
            const result = await ApiUtils.handleResponse(response);
            ApiUtils.showFeedback(this, 'Success!', 3000);
        } catch (error) {
            ApiUtils.showFeedback(this, error.message, 5000);
        }
    }
}));
```

**Complete Example:**
```javascript
import { ApiUtils } from './utils/apiUtils.js';

Alpine.data('applicantsHandler', () => ({
    loading: false,
    feedbackMessage: '',
    feedbackVisible: false,

    async bulkStatusChange(status) {
        this.loading = true;

        try {
            const response = await ApiUtils.post('/api/applicants/bulk-status', {
                applicant_ids: this.selectedApplicants.map(a => a.id),
                status: status
            });

            const result = await ApiUtils.handleResponse(response);
            ApiUtils.showFeedback(this, result.message, 3000);

            // Refresh data
            await this.fetchApplicants();
        } catch (error) {
            ApiUtils.showFeedback(this, error.message, 5000);
        } finally {
            this.loading = false;
        }
    }
}));
```

---

## Tailwind Configuration

The Tailwind configuration now includes brand colors defined in `tailwind.config.js`.

### Brand Colors

| Class | Hex | Usage |
|-------|-----|-------|
| `brand-primary` | #BD9168 | Navbar background, primary accents |
| `brand-secondary` | #BD6F22 | Buttons, headings, links |
| `brand-tertiary` | #8B4513 | Sidebar icons, text |
| `brand-hover` | #a95e1d | Button hover states |
| `brand-light` | #F9F6F3 | Light backgrounds, active states |
| `brand-dark` | #6F3610 | Dark text, borders |

### Usage

Instead of inline hex values, use Tailwind classes:

```blade
<!-- OLD: -->
<div class="bg-[#BD9168]">

<!-- NEW: -->
<div class="bg-brand-primary">

<!-- OLD: -->
<button class="bg-[#BD6F22] hover:bg-[#a95e1d]">

<!-- NEW: -->
<button class="bg-brand-secondary hover:bg-brand-hover">

<!-- Text colors -->
<h1 class="text-brand-secondary">Heading</h1>
<span class="text-brand-tertiary">Label</span>

<!-- Borders -->
<div class="border-brand-secondary">
```

### Font Family

```blade
<!-- Apply Alata font -->
<div class="font-alata">Content</div>
```

---

## Migration Guide

### Updating Existing Views

1. **Replace navbar components:**
   ```blade
   <!-- OLD -->
   <x-hrAdmin.navbar />

   <!-- NEW -->
   <x-shared.navbar :showRoleText="true" />
   ```

2. **Replace sidebar components:**
   ```blade
   <!-- OLD -->
   <x-hrAdmin.sidebar :currentRoute="Route::currentRouteName()" />

   <!-- NEW -->
   @php
       $menuItems = [/* menu items */];
   @endphp
   <x-shared.sidebar :items="$menuItems" :currentRoute="Route::currentRouteName()" />
   ```

3. **Replace form inputs:**
   ```blade
   <!-- OLD -->
   <label>First Name</label>
   <input type="text" name="first_name" value="{{ old('first_name') }}" class="w-full border...">

   <!-- NEW -->
   <x-shared.form.text-input
       label="First Name"
       name="first_name"
       :value="old('first_name')"
   />
   ```

4. **Replace loading spinners:**
   ```blade
   <!-- OLD -->
   <svg class="animate-spin h-4 w-4" ...>...</svg>

   <!-- NEW -->
   <x-shared.loading-spinner />
   ```

5. **Update color classes:**
   ```blade
   <!-- OLD -->
   <div class="bg-[#BD6F22]">

   <!-- NEW -->
   <div class="bg-brand-secondary">
   ```

### Updating JavaScript Files

1. **Import utilities:**
   ```javascript
   // At the top of your file
   import { CheckboxUtils } from './utils/checkboxUtils.js';
   import { TimeUtils } from './utils/timeUtils.js';
   import { ApiUtils } from './utils/apiUtils.js';
   ```

2. **Update your HTML to load as module:**
   ```blade
   @push('scripts')
   <script type="module" src="{{ asset('js/yourHandler.js') }}"></script>
   @endpush
   ```

3. **Replace duplicate functions:**
   ```javascript
   // OLD
   toggleSelectAll(event) {
       // ... 20 lines of code
   }

   // NEW
   toggleSelectAll(event) {
       this.selectedItems = CheckboxUtils.toggleSelectAll(
           event,
           this.selectedItems
       );
       this.updateMasterCheckbox();
   }
   ```

---

## Benefits

### Code Reduction
- **Layouts**: 4 files with ~300 lines each → 1 base + 4 small extensions (~75% reduction)
- **Navbars**: 4 files → 1 shared component (98% reduction)
- **Sidebars**: 4 files → 1 shared component (95% reduction)
- **JavaScript**: ~500 lines of duplicate code → shared utilities

### Maintainability
- Single source of truth for common components
- Bug fixes apply everywhere automatically
- Consistent styling enforced
- Easier onboarding for new developers

### Performance
- Reduced HTML output
- Better caching of shared components
- Faster page loads

### Developer Experience
- Faster development (reuse vs rebuild)
- Less code to write and maintain
- Self-documenting component props
- IntelliSense support with Blade directives

---

## Best Practices

1. **Always use shared components** for common UI elements
2. **Don't hardcode brand colors** - use Tailwind classes
3. **Reuse JavaScript utilities** instead of copying code
4. **Document custom props** when extending components
5. **Test across all roles** when updating shared components
6. **Use semantic HTML** within components
7. **Follow naming conventions** for consistency

---

## Support

For questions or issues with shared components:
1. Check this documentation first
2. Review component source code in `resources/views/components/shared/`
3. Check utility source code in `public/js/utils/`
4. Consult the team lead or create an issue

---

## Changelog

### Version 1.0.0 (Initial Consolidation)
- Created base layout component
- Unified navbar and sidebar components
- Created form input components (text, select, textarea, date)
- Created UI components (modal, button, loading spinner, status badge)
- Extracted JavaScript utilities (checkbox, time, API)
- Configured Tailwind with brand colors
- Updated all 4 role layouts to use shared components
- Documentation created

---

**Last Updated:** 2025-11-07
**Version:** 1.0.0
