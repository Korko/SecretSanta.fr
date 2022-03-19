<script>
    import FetcherLayout from './FetcherLayout.vue';

    import NavPageLink from '../Components/NavPageLink.vue';
    import PendingDrawForm from '../Components/PendingDrawForm.vue';

    export default {
        components: {
            FetcherLayout,
            NavPageLink,
            PendingDrawForm
        },
        props: {
            routes: {
                type: Object
            },
            pending: {
                type: Object
            }
        }
    }
</script>

<template>
    <FetcherLayout :route="routes.fetch">
        <template #navbar-right>
            <NavPageLink :href="routes.form">
                <i18n-t keypath="common.nav.go" />
            </NavPageLink>
            <NavPageLink :href="routes.dashboard">
                <i18n-t keypath="common.nav.dashboard" />
            </NavPageLink>
        </template>
        <template #default="data">
            <PendingDrawForm :routes="routes" :pending="{...pending, ...data.pending}"/>
        </template>
    </FetcherLayout>
</template>

<style scoped>

</style>