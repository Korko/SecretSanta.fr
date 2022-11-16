<script>
    import PageNotFound from '@/Pages/PageNotFound.vue';

    export default {
        components: {
            PageNotFound
        }
    }
</script>

<template>
    <PageNotFound v-bind="$attrs">
        <template #subtitle>
            <i18n-t keypath="404.draw_subtitle" />
        </template>
    </PageNotFound>
</template>

<style scoped>

</style>