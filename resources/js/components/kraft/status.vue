<script>
    import Icon from './icon.vue';

    // An email's delivery, as a pill: received (green), on its way (kraft), failed (red, with the reason below),
    // reported as the wrong recipient or not taking part (gold)
    export default {
        components: { Icon },
        props: {
            mail: { type: Object, required: true },
            report: { type: String, default: null },
            // The organizer's list explains it below the pill
            detailed: { type: Boolean, default: false },
        },
        computed: {
            kind() {
                if (this.report) {
                    return 'warn';
                }
                return { received: 'ok', error: 'err' }[this.mail.delivery_status] || 'wait';
            },
            icon() {
                return { ok: 'check', err: 'warning', warn: 'flag', wait: 'clock' }[this.kind];
            },
            label() {
                if (this.report) {
                    return this.$t('organizer.report.' + this.report);
                }
                const status = this.mail.delivery_status;
                return this.$t(['created', 'sending'].includes(status) ? 'pages.status.waiting' : 'pages.status.' + ({ received: 'received', error: 'error' }[status] || 'sent'));
            },
            detail() {
                if (!this.detailed) {
                    return null;
                }
                if (this.report) {
                    return this.$t('organizer.report.help.' + this.report);
                }
                if (this.mail.delivery_status === 'error') {
                    return this.mail.bounce_reason ? this.$t('common.email.bounce.' + this.mail.bounce_reason) : this.$t('pages.status.error_unknown');
                }
                return this.mail.delivery_status === 'sent' ? this.$t('pages.status.not_opened') : null;
            },
        },
    };
</script>

<template>
    <span class="pg-status-wrap">
        <span class="pg-status" :class="'pg-status--' + kind"><icon :name="icon" :size="14" :stroke="2.4" />{{ label }}</span>
        <span v-if="detail" class="pg-status-detail">{{ detail }}</span>
    </span>
</template>
