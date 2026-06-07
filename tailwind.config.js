/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './app/**/*.php',
        './resources/**/*.blade.php',
        './resources/**/*.js',
    ],
    theme: {
        extend: {
            colors: {
                brand: {
                    50: '#eef4ff',
                    100: '#d9e7ff',
                    200: '#bcd2ff',
                    300: '#8fb1ff',
                    400: '#5f89ff',
                    500: '#385ff6',
                    600: '#1f44d4',
                    700: '#1837aa',
                    800: '#192f87',
                    900: '#1a2b6d',
                },
                accent: {
                    50: '#eefcf5',
                    100: '#d7f8e5',
                    200: '#b2f0cd',
                    300: '#7ee2ac',
                    400: '#41cc84',
                    500: '#1fa968',
                    600: '#158955',
                    700: '#126d46',
                    800: '#125739',
                    900: '#114731',
                },
            },
            boxShadow: {
                panel: '0 14px 40px -24px rgba(15, 23, 42, 0.22)',
                soft: '0 10px 25px -18px rgba(30, 64, 175, 0.28)',
            },
            backgroundImage: {
                'app-glow': 'linear-gradient(180deg, rgba(238,244,255,0.95) 0%, rgba(248,250,252,1) 38%, rgba(241,245,249,1) 100%)',
            },
            fontFamily: {
                sans: ['Figtree', 'ui-sans-serif', 'system-ui', 'sans-serif'],
            },
        },
    },
    plugins: [],
};
