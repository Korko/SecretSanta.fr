<script>
export default {
    props: {
        questions: {
            type: Array,
            required: true
        }
    },
    data() {
        return {
            qnas: this.questions
        };
    }
}
</script>

<template>
    <div>
        <div class="card" v-for="(qna, i) in qnas" :key="i">
            <p :id="`question${i}`" class="card-header" @click="$set(qna, 'showed', !qna.showed)" :aria-expanded="qna.showed" :aria-controls="`answer${i}`">{{ qna.question }}</p>
            
            <transition name="fade">
                <div :id="`answer${i}`" class="card-body" v-show="qna.showed">
                    <p>{{ qna.answer }}</p>
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
