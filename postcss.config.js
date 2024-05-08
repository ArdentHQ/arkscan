import postcssImport from 'postcss-import';
import postcssFocusVisible from 'postcss-focus-visible';
import tailwindcss from 'tailwindcss';

export default {
    plugins: [
        postcssImport,
        tailwindcss,
        postcssFocusVisible,
    ],
};
