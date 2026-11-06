<script>
    import fetch from '../partials/fetch.js';

    export default {
        props: {
            fetchurl: {
                type: String,
                required: true
            }
        },
        data: () => {
            return {
                loading: false
            };
        },
        mounted() {
            this.loading = true;
            fetch(this.fetchurl)
                .then(response => {
                    this.$emit('success', response);
                })
                .catch(response => {
                    this.$emit('error');
                })
                .finally(() => {
                    this.loading = false;
                });
        }
    };
</script>

<template>
    <span />
</template>
