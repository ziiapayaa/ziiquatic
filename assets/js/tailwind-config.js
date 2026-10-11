/**
 * ziiquatic - Tailwind Custom Configuration
 */
tailwind.config = {
  theme: {
    extend: {
      fontFamily: {
        sans: ['"Plus Jakarta Sans"', 'sans-serif'],
        serif: ['"Newsreader"', 'serif'],
      },
      colors: {
        ivory: {
          50: '#FDFCF9',
          100: '#FAF8F5',
          200: '#F3EFE9',
          300: '#EAE4DB',
          400: '#DFD7CA',
          500: '#CFC3B0',
        },
        moss: {
          50: '#F0F5FA',
          100: '#E1EDF5',
          600: '#225B82',
          700: '#184767',
          800: '#11354F',
          900: '#0B2336',
        },
        stoneink: {
          900: '#1B1C1A',
          800: '#2A2C29',
          700: '#3E413D',
          500: '#676B65',
          400: '#8C9089',
          300: '#B6BAB2',
        }
      }
    }
  }
};
