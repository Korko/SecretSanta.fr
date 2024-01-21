<script>
    import Layout from '@/Layouts/Main.vue';

    import NavPageLink from '@/Components/NavPageLink.vue';

    export default {
        components: {
            Layout,
            NavPageLink
        }
    }
</script>

<template>
    <Layout>
        <template #navbar-right>
            <NavPageLink :href="route('form.index')">
                <i18n-t keypath="common.nav.go" />
            </NavPageLink>
            <NavPageLink :href="route('dashboard')">
                <i18n-t keypath="common.nav.dashboard" />
            </NavPageLink>
        </template>
        <template #content>
            <div class="polaroid-wrapper">
                <div class="item">
                    <div class="polaroid">
                        <div class="caption"></div>
                    </div>
                    <p class="wasted">Wasted</p>
                </div>
            </div>
            <div class="error-content">
                <h1 class="section-title text-center"><i18n-t keypath="404.title" /></h1>
                <p class="lead text-center">
                    <slot name="subtitle">
                        <i18n-t keypath="404.page_subtitle" />
                    </slot>
                </p>

                <p class="back text-center">
                    <a href="/" class="btn btn-primary"><i18n-t keypath="404.back" /></a>
                </p>
            </div>
        </template>
    </Layout>
</template>

<style scoped>

</style>
