/** @type {import('tailwindcss').Config} */
import colors from 'tailwindcss/colors'

export default {
  content: [
    './resources/**/*.blade.php',
    './resources/**/*.js',
    './vendor/filament/**/*.blade.php',
  ],
  theme: {
    extend: {
      colors: {
        primary: colors.teal,
        slate: colors.slate,
        gray: colors.gray,
      },
    },
  },
  plugins: [
    require('@tailwindcss/forms'),
  ],
}