const globals = require('globals');
const js = require('@eslint/js');
const sonarjs = require('eslint-plugin-sonarjs');

module.exports = [
  js.configs.recommended,
  sonarjs.configs.recommended,
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
