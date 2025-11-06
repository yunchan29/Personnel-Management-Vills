/**
 * API Utilities
 * Shared functions for API calls and CSRF handling
 */

export const ApiUtils = {
    /**
     * Get CSRF token from meta tag
     * @throws {Error} If CSRF token is not found
     * @returns {string} CSRF token
     */
    getCsrfToken() {
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
        if (!csrfToken) {
            throw new Error('CSRF token not found. Please refresh the page.');
        }
        return csrfToken;
    },

    /**
     * Make a fetch request with CSRF token
     * @param {string} url - The API endpoint URL
     * @param {Object} options - Fetch options
     * @param {string} options.method - HTTP method (GET, POST, PUT, DELETE, etc.)
     * @param {Object} options.body - Request body (will be JSON stringified)
     * @param {Object} options.headers - Additional headers
     * @returns {Promise<Response>} Fetch response
     */
    async fetchWithCsrf(url, { method = 'POST', body = null, headers = {} } = {}) {
        const csrfToken = this.getCsrfToken();

        const defaultHeaders = {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrfToken,
            ...headers
        };

        const options = {
            method,
            headers: defaultHeaders
        };

        if (body && method !== 'GET') {
            options.body = typeof body === 'string' ? body : JSON.stringify(body);
        }

        return fetch(url, options);
    },

    /**
     * Make a POST request with CSRF token
     * @param {string} url - The API endpoint URL
     * @param {Object} data - Request body data
     * @returns {Promise<Response>} Fetch response
     */
    async post(url, data) {
        return this.fetchWithCsrf(url, { method: 'POST', body: data });
    },

    /**
     * Make a PUT request with CSRF token
     * @param {string} url - The API endpoint URL
     * @param {Object} data - Request body data
     * @returns {Promise<Response>} Fetch response
     */
    async put(url, data) {
        return this.fetchWithCsrf(url, { method: 'PUT', body: data });
    },

    /**
     * Make a DELETE request with CSRF token
     * @param {string} url - The API endpoint URL
     * @param {Object} data - Request body data (optional)
     * @returns {Promise<Response>} Fetch response
     */
    async delete(url, data = null) {
        return this.fetchWithCsrf(url, { method: 'DELETE', body: data });
    },

    /**
     * Handle API response and extract JSON
     * @param {Response} response - Fetch response
     * @returns {Promise<Object>} Parsed JSON response
     * @throws {Error} If response is not ok
     */
    async handleResponse(response) {
        if (!response.ok) {
            const errorData = await response.json().catch(() => ({}));
            throw new Error(errorData.message || `HTTP error! status: ${response.status}`);
        }
        return response.json();
    },

    /**
     * Display a feedback message
     * @param {Object} component - Alpine component instance
     * @param {string} message - The message to display
     * @param {number} duration - Duration in milliseconds (default: 3000)
     */
    showFeedback(component, message, duration = 3000) {
        component.feedbackMessage = message;
        component.feedbackVisible = true;
        setTimeout(() => {
            component.feedbackVisible = false;
        }, duration);
    }
};
