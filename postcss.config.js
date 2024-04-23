import postcssImport from 'postcss-import';
import postcssFocusVisible from 'postcss-focus-visible';
import tailwindcss from 'tailwindcss';
import autoprefixer from 'autoprefixer';

export default {
    plugins: [
        postcssImport,
        tailwindcss,
        postcssFocusVisible,
        // autoprefixer,
    ],
};
