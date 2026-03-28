import type { Config } from 'tailwindcss'

const config: Config = {
  content: [
    './pages/**/*.{js,ts,jsx,tsx,mdx}',
    './components/**/*.{js,ts,jsx,tsx,mdx}',
    './app/**/*.{js,ts,jsx,tsx,mdx}',
  ],
  theme: {
    extend: {
      colors: {
        bg: '#F8F6F2',
        surface: '#FFFFFF',
        border: '#E8E3DB',
        'text-primary': '#1A1714',
        'text-secondary': '#6B6560',
        accent: '#C4553A',
        'accent-light': '#FDF2EF',
        'accent-dark': '#A3432D',
        highlight: '#D4A843',
        dark: '#1E1B18',
      },
      fontFamily: {
        sans: ['Inter', 'system-ui', 'sans-serif'],
      },
    },
  },
  plugins: [],
}
export default config
