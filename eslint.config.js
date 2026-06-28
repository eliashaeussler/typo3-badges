import globals from 'globals';
import js from '@eslint/js';

export default [
  js.configs.recommended,
  {
    languageOptions: {
      globals: {
        ...globals.browser,
      },
      parserOptions: {
        ecmaVersion: 13,
        sourceType: 'module',
      },
    },
  },
];
