import { ref, unref } from 'vue'

export default function($t, v$) {
    const fieldErrors = ref({});//DEBUG

    const clientValidationErrors = field => {
        field = field.replace('[', '.').replace(']', '');
        return (field.split('.').reduce((p, c) => p?.[c], unref(v$))?.$errors || []).map(v => unref($t)(field + '.' + v.$validator) || v.$message);
    };

    const serverValidationErrors = field => fieldErrors?.[field]?.[0] || [];
    const fieldError = field => [].concat(clientValidationErrors(field), serverValidationErrors(field));

    return fieldError;
};
