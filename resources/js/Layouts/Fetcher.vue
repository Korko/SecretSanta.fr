<script>
    import Layout from '@/Layouts/Main.vue';

    import Fetcher from '@/Components/Fetcher.vue';

    import { Suspense } from "vue";

    export default {
        components: {
            Layout,
            Suspense,
            Fetcher
        },
        props: {
            src: {
                type: String,
                required: true
            }
        }
    }
</script>

<template>
    <Layout>
        <template #navbar-left>
            <slot name="navbar-left" />
        </template>
        <template #navbar-right>
            <slot name="navbar-right" />
        </template>
        <template #content>
            <Suspense>
                <template #default>
                    <Fetcher :src="src">
                        <template #default="data">
                            <slot name="default" v-bind="data" />
                        </template>
                    </Fetcher>
                </template>

                <template #fallback>
                    <div>
                        <i18n-t keypath="common.fetcher.loading" />
                    </div>
                </template>
            </Suspense>
        </template>
    </Layout>
</template>

<style scoped>

</style>
