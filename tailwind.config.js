/** @type {import('tailwindcss').Config} */
import colors from 'tailwindcss/colors';
import forms from '@tailwindcss/forms';

export default {
    // Files to scan for Tailwind classes
    content: [
        // Application's Blade templates and JS files
        './resources/**/*.blade.php',
        './resources/**/*.js',
        
        // Filament's Blade templates
        './vendor/filament/**/*.blade.php',
        
        // Laravel's core Blade templates (e.g., for pagination)
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
    ],

    // Enable dark mode based on the 'class' attribute (used by Filament)
    darkMode: 'class', 

    theme: {
        extend: {
            colors: {
                // Custom color palette for the application
                primary: colors.teal,
                slate: colors.slate,
                gray: colors.gray,
            },
        },
    },

    plugins: [
        // Tailwind Forms plugin for better form styling
        forms,
    ],
}