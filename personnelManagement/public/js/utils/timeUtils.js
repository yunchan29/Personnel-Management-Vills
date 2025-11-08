/**
 * Time Utilities
 * Shared time conversion and formatting functions
 */

window.TimeUtils = {
    /**
     * Convert 12-hour time to 24-hour format
     * @param {number} hour - Hour in 12-hour format (1-12)
     * @param {string} period - 'AM' or 'PM'
     * @returns {number} Hour in 24-hour format (0-23)
     */
    to24h(hour, period) {
        hour = Number(hour);

        if (period === "AM") {
            if (hour === 12) {
                return 0; // 12 AM → 00
            }
            return hour; // 1–11 AM → 1–11
        } else {
            if (hour === 12) {
                return 12; // 12 PM → 12 (noon)
            }
            return hour + 12; // 1–11 PM → 13–23
        }
    },

    /**
     * Convert 24-hour time to 12-hour format
     * @param {number} hour24 - Hour in 24-hour format (0-23)
     * @returns {Object} Object with hour12 and period properties
     */
    to12h(hour24) {
        let period = hour24 >= 12 ? 'PM' : 'AM';
        let hour12 = hour24 % 12;
        if (hour12 === 0) hour12 = 12;
        return { hour12, period };
    },

    /**
     * Format time for display
     * @param {number} hour - Hour (1-12)
     * @param {string} period - 'AM' or 'PM'
     * @returns {string} Formatted time string (e.g., "2:00 PM")
     */
    formatDisplay(hour, period) {
        return `${hour}:00 ${period}`;
    },

    /**
     * Get period (AM/PM) based on hour
     * @param {number} hour - Hour (1-12 or 8-4 format used in the app)
     * @returns {string} 'AM' or 'PM'
     */
    getPeriodFromHour(hour) {
        // 8–11 → AM
        if ([8, 9, 10, 11].includes(hour)) {
            return 'AM';
        }
        // 12–4 → PM
        if ([12, 1, 2, 3, 4].includes(hour)) {
            return 'PM';
        }
        return 'PM'; // default
    }
};
