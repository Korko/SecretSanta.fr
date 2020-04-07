module.exports = {
    "env": {
        "browser": true,
        "es6": true,
        "jquery": true,
        "commonjs": true
    },
    "extends": [
        "eslint:recommended",
        "plugin:vue/vue3-recommended"
    ],
    "globals": {
        "Atomics": "readonly",
        "SharedArrayBuffer": "readonly"
    },
    "parserOptions": {
        "ecmaVersion": 2018,
        "sourceType": "module"
    },
    "plugins": [
        "vue"
    ],
    "rules": {
        "vue/html-indent": ["error", 4, {}],
        "vue/max-attributes-per-line": ["warn", {singleline: 10}],
        "vue/html-self-closing": ["off"],
        "vue/singleline-html-element-content-newline": ["off"]
    }
};
