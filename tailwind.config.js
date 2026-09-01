/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './resources/views/**/*.blade.php',
        './resources/js/**/*.js',
    ],
    theme: {
        extend: {
            colors: {
                primary: {
                    DEFAULT: '#0A5C3A',
                    dark: '#1F3D2E',
                },
                gold: {
                    DEFAULT: '#E6AF00',
                    soft: '#E8C670',
                },
                cream: '#F5EDE0',
                teal: '#4A7C83',
                accent: '#E96F27',
            },
            fontFamily: {
                sans: ['IranYekan', 'ui-sans-serif', 'system-ui', 'sans-serif'],
                secondary: ['IranSans', 'IranYekan', 'ui-sans-serif', 'sans-serif'],
            },
            maxWidth: {
                site: '1440px',
                content: '1290px', // 12*80 + 11*30
            },
            spacing: {
                gutter: '30px',
                col: '80px',
            },
            borderRadius: {
                pill: '9999px',
                card: '16px',
            },
            boxShadow: {
                card: '0 8px 30px rgba(31, 61, 46, 0.08)',
            },
        },
    },
    plugins: [],
};
