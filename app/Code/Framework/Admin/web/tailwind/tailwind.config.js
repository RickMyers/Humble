/** @type {import('tailwindcss').Config} */
module.exports = {
  content: ['../../**/*.{twig,tpl,html,blade,savant,tbs,latte}'],
  theme: {
    container: {
        center: true
    },
    screens: {
        sm: '480px',
        md: '768px',
        lg: '976px',
        xl: '1440px'
    },
    extend: {
        colors: {
            brightRed: 'hsl(12,88%,59%)',
            midblack: '#333333',
            transparent: 'transparent',
            currentColor: 'currentColor'
        }
    },
  },
  plugins: [],
}

