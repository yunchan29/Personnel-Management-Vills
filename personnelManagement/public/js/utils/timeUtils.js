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
    },

    /**
     * Format time string for storage in 24-hour format (H:i:s)
     * @param {string} timeStr - Time string (e.g., "8 AM", "4 PM", "8:00 AM")
     * @returns {string} Formatted time string in 24-hour format (e.g., "08:00:00", "16:00:00")
     */
    formatTime(timeStr) {
        if (!timeStr || typeof timeStr !== 'string') {
            return '';
        }

        // Parse the time string
        const parts = timeStr.trim().split(' ');
        if (parts.length !== 2) {
            return '';
        }

        let hour = parts[0];
        const period = parts[1].toUpperCase();

        // Extract hour from "8:00" or "8" format
        if (hour.includes(':')) {
            hour = hour.split(':')[0];
        }

        // Convert to number
        hour = parseInt(hour, 10);

        if (isNaN(hour) || hour < 1 || hour > 12) {
            return '';
        }

        // Convert to 24-hour format
        const hour24 = this.to24h(hour, period);

        // Format as H:i:s (e.g., "08:00:00" or "16:00:00")
        return `${String(hour24).padStart(2, '0')}:00:00`;
    }
};
