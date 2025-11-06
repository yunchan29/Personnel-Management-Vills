/** @type {import('tailwindcss').Config} */
export default {
  content: [
    './resources/**/*.blade.php',
    './resources/**/*.js',
    './resources/**/*.vue', // optional if using Vue
    './storage/framework/views/*.php',
    './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
  ],
  theme: {
    extend: {
      colors: {
        brand: {
          primary: '#BD9168',
          secondary: '#BD6F22',
          tertiary: '#8B4513',
          hover: '#a95e1d',
          light: '#F9F6F3',
          dark: '#6F3610',
        }
      },
      fontFamily: {
        sans: ['Instrument Sans', 'ui-sans-serif', 'system-ui', 'sans-serif'],
        alata: ['Alata', 'sans-serif'],
      },
    },
  },
  plugins: [],
}
