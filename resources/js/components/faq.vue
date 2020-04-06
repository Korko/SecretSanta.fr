<script>
export default {
    props: {
        questions: {
            type: Object,
            required: true
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
    <div>
        <ul class="nav nav-tabs">
            <li class="nav-item" v-for="category in categories">
                <a :class="{ 'nav-link': true, 'active': (selectedCategory === category) }" href="#" @click="selectedCategory = category">{{ $t(`faq.categories.${category}`) }}</a>
            </li>
        </ul>
        <div class="card" v-for="(question, i) in selectedQuestions" :key="`${selectedCategory}_${i}`">
            <p :id="`question${i}`" class="card-header" @click="$set(showed, i, !showed[i])" :aria-expanded="showed[i]" :aria-controls="`answer${i}`">{{ question }}</p>

            <transition name="fade">
                <div :id="`answer${i}`" class="card-body" v-show="showed[i]">
                    <p>{{ selectedAnswers[i] }}</p>
                </div>
            </transition>
        </div>
    </div>
</template>

<style>
    .fade-enter-active, .fade-leave-active {
        transition: all .5s;
    }
    .fade-enter, .fade-leave-to {
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
        border-width: .2rem 0 0 .2rem;
        transform-origin: .5rem .5rem; /* 1/3 width 1/3 height */
        right: 1.25rem; /* right padding of card-header */
        top: 1rem; /* top padding of card-header + transform-origin / 2 */
        transform: rotate(45deg);
        transition: all 1s; /* twice the fade delay */
    }
    .card .card-header[aria-expanded='true']:after {
        transform: rotate(45deg) scaleX(-1) scaleY(-1);
    }
</style>
