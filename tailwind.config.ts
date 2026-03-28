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
        accent: '#2C5F3F',
        'accent-light': '#EDF4EF',
        highlight: '#D4A843',
      },
      fontFamily: {
        sans: ['Inter', 'system-ui', 'sans-serif'],
      },
    },
  },
  plugins: [],
}
export default config
