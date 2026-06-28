import tailwindSelectionVariant from 'tailwindcss-selection-variant';
import tailwindCrossbrowserTouch from 'tailwindcss-crossbrowser-touch';

export default {
  content: [
    './assets/**/*.js',
    './templates/**/*.html.twig',
  ],
  theme: {
    fontFamily: {
      'sans': ['Lato', 'sans-serif'],
      'mono': ['JetBrains Mono', 'monospace'],
      'cursive': ['Indie Flower', 'cursive'],
    },
    triangles: {
      'right-up': {
        direction: 'right-up',
        size: '8rem',
      },
    },
    extend: {
      colors: {
        current: 'currentColor',
        'typo3-orange': '#f49700',
      },
    },
  },
  variants: {
    opacity: ['no-touch'],
  },
  plugins: [
    tailwindSelectionVariant,
    tailwindCrossbrowserTouch(),
  ],
}
