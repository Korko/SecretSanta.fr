import purgecss from '@fullhuman/postcss-purgecss';

export default {
    plugins: process.env.NODE_ENV === 'production' ? [
        purgecss({
            content: ['resources/**/*.{php,js,vue}', 'app/**/*.php'],
            // Classes added at runtime by alertify, vuejs-dialog, toastify, bootstrap JS states
            safelist: { greedy: [/^ajs-/, /^alertify/, /^dg-/, /^toastify/, /^multiselect/, /^(show|fade|collapsing|modal-backdrop)/] },
        }),
    ] : [],
};
