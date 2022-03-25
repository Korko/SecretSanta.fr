<script>
    import Layout from './Layout.vue';

    import NavLink from '../Components/NavLink.vue';
    import NavPageLink from '../Components/NavPageLink.vue';

    export default {
        components: {
            Layout,
            NavLink,
            NavPageLink
        },
        props: {
            routes: {
                type: Object
            }
        },
        data() {
            return {
                draws: {}
            };
        },
        created() {
            this.draws = JSON.parse(window.localStorage.getItem('secretsanta')) || {};
        },
    }
</script>

<template>
    <Layout>
        <template #navbar-right>
            <NavPageLink :href="routes.form">
                <i18n-t keypath="common.nav.go" />
            </NavPageLink>
            <NavPageLink :href="routes.faq">
                <i18n-t keypath="common.nav.faq" />
            </NavPageLink>
        </template>
        <template #content>
            Dash
        </template>
    </Layout>
</template>