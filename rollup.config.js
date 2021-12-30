import babel from '@rollup/plugin-babel';
import commonjs from '@rollup/plugin-commonjs';
import resolve from '@rollup/plugin-node-resolve';
import postcss from 'rollup-plugin-postcss';
import copy from 'rollup-plugin-copy';
import {uglify} from 'rollup-plugin-uglify';

const isProduction = process.env.NODE_ENV === 'production';

export default {
  input: 'assets/main.js',
  output: {
    dir: 'public/assets',
    format: 'iife',
  },
  plugins: [
    copy({
      targets: [
        { src: 'node_modules/@fontsource/lato/files/lato-*-{400,700}-*.{woff,woff2}', dest: 'public/fonts/' },
        { src: 'node_modules/@fontsource/jetbrains-mono/files/jetbrains-mono-*-{400,700}-*.{woff,woff2}', dest: 'public/fonts/' },
      ],
      copyOnce: !isProduction,
    }),
    postcss({
      modules: true,
      extract: 'fonts.css',
      minimize: isProduction,
    }),
    babel({
      babelHelpers: 'bundled',
      exclude: 'node_modules/**',
    }),
    commonjs(),
    resolve(),
    isProduction && uglify(),
  ],
};
