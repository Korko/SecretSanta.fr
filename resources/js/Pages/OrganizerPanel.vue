<script>
    import FetcherLayout from '@/Layouts/Fetcher.vue';

    import NavPageLink from '@/Components/NavPageLink.vue';
    import OrganizerForm from '@/Components/OrganizerForm.vue';

    import { deepMerge } from '@/helpers.js';

    export default {
        components: {
            FetcherLayout,
            NavPageLink,
            OrganizerForm
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
    <FetcherLayout :src="routes.fetch">
        <template #navbar-right>
            <NavPageLink :href="route('form.index')">
                <i18n-t keypath="common.nav.go" />
            </NavPageLink>
            <NavPageLink :href="route('dashboard')">
                <i18n-t keypath="common.nav.dashboard" />
            </NavPageLink>
        </template>
        <template #default="data">
            <OrganizerForm v-bind="deepMerge(data, {routes})" />
        </template>
    </FetcherLayout>
</template>

<style scoped>

</style>
