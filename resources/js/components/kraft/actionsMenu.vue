<script>
    import Icon from './icon.vue';

    let uid = 0;

    // The "⋯" menu of a participant: arrows move between the items, Escape closes it and gives the focus back
    export default {
        components: { Icon },
        props: {
            label: { type: String, required: true },
            // [{ label, icon, action, danger, disabled }]
            items: { type: Array, required: true },
        },
        data: () => ({ open: false, id: 'k-menu-' + (++uid) }),
        beforeUnmount() {
            document.removeEventListener('mousedown', this.outside);
        },
        methods: {
            toggle() {
                this.open ? this.close() : this.show();
            },
            show(last = false) {
                this.open = true;
                document.addEventListener('mousedown', this.outside);
                this.$nextTick(() => this.focusItem(last ? this.buttons().length - 1 : 0));
            },
            close(focusButton = true) {
                this.open = false;
                document.removeEventListener('mousedown', this.outside);
                if (focusButton) {
                    this.$refs.button.focus();
                }
            },
            outside(event) {
                if (!this.$el.contains(event.target)) {
                    this.close(false);
                }
            },
            buttons() {
                return [...this.$el.querySelectorAll('[role=menuitem]:not([disabled])')];
            },
            focusItem(index) {
                const buttons = this.buttons();
                buttons[(index + buttons.length) % buttons.length]?.focus();
            },
            move(step) {
                this.focusItem(this.buttons().indexOf(document.activeElement) + step);
            },
            choose(item) {
                this.close();
                item.action();
            },
        },
    };
</script>

<template>
    <div class="pg-menu-wrap" @keydown.esc.stop="open && close()">
        <button
            ref="button"
            type="button"
            class="pg-iconbtn participant-actions"
            :aria-label="label"
            aria-haspopup="menu"
            :aria-expanded="open ? 'true' : 'false'"
            :aria-controls="id"
            @click="toggle"
            @keydown.down.prevent="show()"
            @keydown.up.prevent="show(true)"
        ><icon name="more" :size="18" :stroke="0" /></button>
        <ul
            v-show="open"
            :id="id"
            class="pg-menu"
            role="menu"
            :aria-label="label"
            @keydown.down.prevent="move(1)"
            @keydown.up.prevent="move(-1)"
            @keydown.home.prevent="focusItem(0)"
            @keydown.end.prevent="focusItem(-1)"
            @keydown.tab="close(false)"
        >
            <li v-for="item in items" :key="item.label" role="none">
                <button type="button" role="menuitem" :class="[item.class, { 'pg-danger': item.danger }]" :disabled="item.disabled" tabindex="-1" @click="choose(item)">
                    <icon :name="item.icon" :size="16" />{{ item.label }}
                </button>
            </li>
        </ul>
    </div>
</template>
