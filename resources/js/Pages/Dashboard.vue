<script>
    import Layout from '@/Layouts/Main.vue';

    import NavLink from '@/Components/NavLink.vue';
    import NavPageLink from '@/Components/NavPageLink.vue';

    export default {
        components: {
            Layout,
            NavLink,
            NavPageLink
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
            <NavPageLink :href="route('form.index')">
                <i18n-t keypath="common.nav.go" />
            </NavPageLink>
            <NavPageLink :href="route('faq')">
                <i18n-t keypath="common.nav.faq" />
            </NavPageLink>
        </template>
        <template #content>
            Dash
        </template>
    </Layout>
</template>
