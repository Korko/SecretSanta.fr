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
            },
            app_email: {
                type: String
            },
            questions: {
                type: Object
            }
        },
        data() {
            return {
                selectedCategory: Object.keys(this.questions)[0],
                categories: Object.keys(this.questions),
                qnas: this.questions,
                showed: {}
            };
        },
        computed: {
            selectedQuestions() { return Object.keys(this.qnas[this.selectedCategory]); },
            selectedAnswers() { return Object.values(this.qnas[this.selectedCategory]); }
        },
        watch: {
            selectedCategory() { this.showed = {}; }
        }
    }
</script>

<template>
    <Layout>
        <template #navbar-right>
            <NavPageLink :href="route('form.index')">
                <i18n-t keypath="common.nav.go" />
            </NavPageLink>
            <NavLink :href="`mailto:`+app_email">
                <i18n-t keypath="faq.nav.contact" />
            </NavLink>
            <NavPageLink :href="route('dashboard')">
                <i18n-t keypath="common.nav.dashboard" />
            </NavPageLink>
        </template>
        <template #content>
            <div>
                <ul class="nav nav-tabs">
                    <li
                        v-for="(category, i) in categories"
                        :key="i"
                        class="nav-item"
                    >
                        <a
                            class="nav-link"
                            :class="{active: (selectedCategory === category)}"
                            href="#"
                            @click="selectedCategory = category"
                        >
                            <i18n-t :keypath="`faq.categories.${category}`" />
                        </a>
                    </li>
                </ul>
                <div
                    v-for="(question, i) in selectedQuestions"
                    :key="`${selectedCategory}_${i}`"
                    class="card"
                >
                    <p
                        :id="`question${i}`"
                        class="card-header"
                        :aria-expanded="showed[i]"
                        :aria-controls="`answer${i}`"
                        @click="showed[i] = !showed[i]"
                    >
                        {{ question }}
                    </p>

                    <transition name="fade">
                        <ul
                            v-if="Array.isArray(selectedAnswers[i])"
                            v-show="showed[i]"
                            :id="`answer${i}`"
                            class="card-body"
                        >
                            <li v-for="answer in selectedAnswers[i]">{{ answer }}</li>
                        </ul>
                        <div
                            v-else
                            v-show="showed[i]"
                            :id="`answer${i}`"
                            class="card-body"
                        >
                            <p>{{ selectedAnswers[i] }}</p>
                        </div>
                    </transition>
                </div>
            </div>
        </template>
    </Layout>
</template>

<style scoped>
    #content {
        padding-top: 140px;
        padding-bottom: 60px;
    }

    .fade-enter-active {
        transition: all 0.5s ease;
    }
    .fade-leave-active {
        transition: all 0.2s ease;
    }
    .fade-enter-from, .fade-leave-to {
        opacity: 0;
    }

    .card {
        margin: 10px 60px;
    }
    .card .card-header {
        cursor: pointer;
    }
    .card .card-header:after {
        content: '';
        position: absolute;
        width: 1.5rem;
        height: 1.5rem;
        border-style: solid;
        border-color: #000;
        border-width: 0.2rem 0 0 0.2rem;
        transform-origin: 0.5rem 0.5rem; /* 1/3 width 1/3 height */
        right: 1.25rem; /* right padding of card-header */
        top: 1rem; /* top padding of card-header + transform-origin / 2 */
        transform: rotate(45deg) scaleX(-1) scaleY(-1);
        transition: all 1s; /* twice the fade delay */
    }
    .card .card-header[aria-expanded='true']:after {
        transform: rotate(45deg);
    }
</style>
