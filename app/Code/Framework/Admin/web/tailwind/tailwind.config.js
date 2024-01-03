/** @type {import('tailwindcss').Config} */
module.exports = {
  content: ['../../**/*.{twig,tpl,html}'],
  theme: {
    screens: {
        sm: '480px',
        md: '768px',
        lg: '976px',
        xl: '1440px'
    },
    extend: {
        colors: {
            brightRed: 'hsl(12,88%,59%)'
        }
    },
  },
  plugins: [],
}

