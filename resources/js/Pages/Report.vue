<script>
    import FetcherLayout from '@/Layouts/Fetcher.vue';

    import NavPageLink from '@/Components/NavPageLink.vue';
    import ReportForm from '@/Components/ReportForm.vue';

    import { deepMerge } from '@/helpers.js';

    export default {
        components: {
            FetcherLayout,
            NavPageLink,
            ReportForm
        },
        props: {
            routes: {
                type: Object
            }
        },
        methods: {
            deepMerge // Cannot use external function in prop computation, pass as internal
        }
    }
</script>

<template>
    <FetcherLayout :route="routes.fetch">
        <template #navbar-right>
            <NavPageLink :href="route('form.index')">
                <i18n-t keypath="common.nav.go" />
            </NavPageLink>
            <NavPageLink :href="route('dashboard')">
                <i18n-t keypath="common.nav.dashboard" />
            </NavPageLink>
        </template>
        <template #default="data">
            <ReportForm v-bind="deepMerge(data, {routes})" />
        </template>
    </FetcherLayout>
</template>

<style scoped>

</style>
