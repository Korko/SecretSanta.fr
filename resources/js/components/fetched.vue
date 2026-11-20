<script>
    import fetch from '../partials/fetch.js';

    // Loads the page data from its signed URL, then shows the form with it (or an error)
    export default {
        props: {
            form: {
                type: Object,
                required: true
            },
            url: {
                type: String,
                required: true
            },
            routes: {
                type: Object,
                default: () => ({})
            }
        },
        data: () => ({
            data: null,
            failed: false
        }),
        mounted() {
            fetch(this.url)
                .then(data => this.data = data)
                .catch(() => this.failed = true);
        }
    };
</script>

<template>
    <component :is="form" v-if="data" :data="data" :routes="routes" />
    <span v-else-if="failed">{{ $t('common.internal') }}</span>
</template>
