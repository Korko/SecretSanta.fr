<script>
    import Icon from './icon.vue';

    let uid = 0;

    /**
     * Confirmations and small forms of the participant and organizer pages, in place of alertify:
     * open({ title, body, warning, word, fields, ok, danger }) resolves with the fields' values, rejects when cancelled.
     * A modal dialog: the focus stays in it (Tab wraps), Escape cancels, the focus goes back to what opened it.
     * - word: to type before the action is allowed (revealing, deleting everything, withdrawing)
     * - danger: the red tag, for what cannot be undone
     */
    export default {
        components: { Icon },
        data: () => ({
            visible: false,
            options: {},
            values: {},
            typed: '',
            id: 'k-dialog-' + (++uid),
            opener: null,
            settle: null,
        }),
        computed: {
            // Typed without minding the case, accents and the spaces around it
            wordOk() {
                const normalize = s => (s || '').trim().toLocaleLowerCase().normalize('NFD').replace(/\p{Diacritic}/gu, '');
                return !this.options.word || normalize(this.typed) === normalize(this.options.word);
            },
            fieldsOk() {
                return (this.options.fields || []).every(field => !field.required || (this.values[field.name] || '').trim() !== '');
            },
        },
        methods: {
            open(options) {
                this.close(false);
                this.options = options;
                this.values = Object.fromEntries((options.fields || []).map(field => [field.name, field.value || '']));
                this.typed = '';
                this.opener = document.activeElement;
                this.visible = true;
                this.$nextTick(() => (this.$refs.dialog?.querySelector('input, textarea') || this.$refs.dialog?.querySelector('.k-dlg-cancel'))?.focus());

                return new Promise((resolve, reject) => this.settle = { resolve, reject });
            },
            confirm() {
                if (!this.wordOk || !this.fieldsOk) {
                    return;
                }
                const values = { ...this.values };
                this.settle?.resolve(values);
                this.settle = null;
                this.close();
            },
            cancel() {
                this.settle?.reject(new Error('cancelled'));
                this.settle = null;
                this.close();
            },
            close(restoreFocus = true) {
                if (!this.visible) {
                    return;
                }
                this.visible = false;
                if (restoreFocus) {
                    this.$nextTick(() => this.opener?.focus?.());
                }
            },
            // Tab and Shift+Tab go round the dialog
            trap(event) {
                const focusables = [...this.$refs.dialog.querySelectorAll('button:not([disabled]), input, textarea, select, a[href]')];
                if (!focusables.length) {
                    return;
                }
                const first = focusables[0];
                const last = focusables[focusables.length - 1];
                if (event.shiftKey && document.activeElement === first) {
                    event.preventDefault();
                    last.focus();
                } else if (!event.shiftKey && document.activeElement === last) {
                    event.preventDefault();
                    first.focus();
                }
            },
        },
    };
</script>

<template>
    <teleport to="body">
        <div v-if="visible" class="k-dlg-mask" @mousedown.self="cancel">
            <div
                ref="dialog"
                class="pg-dlg"
                role="dialog"
                aria-modal="true"
                :aria-labelledby="id + '-title'"
                :aria-describedby="id + '-body'"
                @keydown.esc.prevent="cancel"
                @keydown.tab="trap"
            >
                <h2 :id="id + '-title'">{{ options.title }}</h2>
                <button type="button" class="pg-iconbtn pg-dlg-x" :aria-label="$t('common.modal.close')" @click="cancel"><icon name="close" /></button>
                <form class="k-dlg-form" @submit.prevent="confirm">
                    <div :id="id + '-body'" class="k-dlg-body">
                        <p v-if="options.body" class="pg-p">{{ options.body }}</p>
                        <p v-if="options.warning" class="k-dlg-warning"><icon name="warning" /><span>{{ options.warning }}</span></p>
                    </div>
                    <div v-for="field in options.fields || []" :key="field.name" class="pg-field">
                        <label :for="id + '-' + field.name">{{ field.label }}</label>
                        <input
                            :id="id + '-' + field.name"
                            v-model="values[field.name]"
                            class="field"
                            :type="field.type || 'text'"
                            :required="field.required"
                            :autocomplete="field.autocomplete || 'off'"
                            :inputmode="field.inputmode"
                        />
                    </div>
                    <div v-if="options.word" class="pg-field">
                        <label :for="id + '-word'">{{ $t('common.confirm.type', { word: options.word }) }}</label>
                        <input :id="id + '-word'" v-model="typed" class="field k-dlg-word" type="text" autocomplete="off" spellcheck="false" />
                    </div>
                    <div class="pg-dlg-actions">
                        <button type="button" class="pg-link pg-link--ink k-dlg-cancel" @click="cancel">{{ options.cancel || $t('common.confirm.cancel') }}</button>
                        <span class="tag-wrap" :class="{ 'tag-wrap--danger': options.danger }" style="--hole: var(--color-papier)">
                            <button type="submit" class="tag-action k-dlg-ok" :class="{ 'tag-action--danger': options.danger }" :disabled="!wordOk || !fieldsOk">{{ options.ok }}</button>
                        </span>
                    </div>
                </form>
            </div>
        </div>
    </teleport>
</template>
