/** @type {import('tailwindcss').Config} */
import colors from 'tailwindcss/colors';
import forms from '@tailwindcss/forms';

export default {
    // Files to scan for Tailwind classes
    content: [
        // Application's Blade templates, including components
        './resources/views/**/*.blade.php',
        './resources/js/**/*.js',
        
        // Filament's Blade templates
        './vendor/filament/**/*.blade.php',
        
        // Laravel's core Blade templates (e.g., for pagination)
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
    ],

    darkMode: 'class',

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
        forms,
    ],
}