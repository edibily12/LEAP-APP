import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';
import typography from '@tailwindcss/typography';
const plugin = require('tailwindcss/plugin');

/** @type {import('tailwindcss').Config} */
export default {
    darkMode: 'media',
   content: [
       './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
       './vendor/laravel/jetstream/**/*.blade.php',
       './storage/framework/views/*.php',
       './resources/views/**/*.blade.php',
       "./node_modules/flowbite/**/*.js"
   ],

   theme: {
       extend: {
           fontFamily: {
               sans: ['Figtree', ...defaultTheme.fontFamily.sans],
           },
           colors: {
               "primary": "#1f4b8e",
               "primary-dark": "#102a52",
               "secondary": "#182430",
               "secondary-dark": "#060C11",
           }
       },
   },

   plugins: [
       require('flowbite/plugin'),
       forms,
       typography,
       plugin(function({ addUtilities, theme }) {
           const newUtilities = {
               '.custom-scrollbar': {
                   '.custom-scrollbar::-webkit-scrollbar': { width: '6px' },
                   '.custom-scrollbar::-webkit-scrollbar-track': { background: theme('bg-secondary')},
                   '.custom-scrollbar::-webkit-scrollbar-thumb': { background: '#888' },
                   '.custom-scrollbar::-webkit-scrollbar-thumb:hover': {background: '#555'},
               }
           }

           addUtilities(newUtilities, ['responsive', 'hover'])
       })
   ],
};