/** @type {import('tailwindcss').Config} */
module.exports = {
    content: [
        "./resources/**/*.blade.php",
        "./resources/**/*.js",
        "./resources/**/*.vue",
    ],
    theme: {
        extend: {
            colors: {
                // Color names coming from color-name.com

                // main text color
                independance: '#455964',
                // main title color
                melon: '#F9AFAA',
                // secondary title color
                'desert-sand': '#E4C8B3',
                // highlight color
                auburn: '#A62729',

                'bright-gray': '#EBEBEB',
                // accent color
                'persian-red': '#D03133',
            },
            fontFamily: {
                'happy-kids': ['HappyKids', 'sans-serif'],
                aileron: ['Aileron', 'sans-serif'],
            },
            flex: {
                2: '2 2 0%',
            }
        }
    },
    plugins: [],
};
