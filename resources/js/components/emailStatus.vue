<script>
    export default {
        props: {
            can_redo: {
                type: Boolean,
                default: true
            },
            delivery_status: {
                type: String,
                required: true
            },
            last_update: {
                type: [String, Number],
                default: null
            },
            disabled: {
                type: Boolean,
                default: false
            },
            rateLimit: {
                type: Number,
                default: 5*60*1000 // 5m delay
            }
        },
        data() {
            return {
                recent: true,
                recentUpdateTimeout: null
            };
        },
        computed: {
            icon() {
                return {
                    created: "fas fa-spinner",
                    sending: "fas fa-spinner",
                    sent: "fas fa-check",
                    received: "fas fa-check",
                    error: "fas fa-exclamation-triangle"
                }[this.delivery_status];
            }
        },
        watch: {
            last_update: {
                immediate: true,
                handler() {
                    this.recent = this.isRecent();
                    if(this.recent) {
                        this.recentUpdateTimeout && clearTimeout(this.recentUpdateTimeout);
                        this.recentUpdateTimeout = setTimeout(() => {
                            this.recent = this.isRecent();
                        }, this.rateLimit - this.getDelay());
                    }
                }
            }
        },
        methods: {
            getDelay() {
                return (new Date()).getTime() - (new Date(this.last_update)).getTime();
            },
            isRecent() {
                return this.getDelay() < this.rateLimit;
            }
        }
    }
</script>

<template>
    <div>
        <span>{{ $t(`common.email.status.${delivery_status}`) }} <i :class="[icon, delivery_status]"></i></span>
        <button v-if="can_redo || delivery_status === 'error'" :disabled="recent || disabled" type="button" class="btn btn-outline-secondary" @click="$emit('redo')">
            <i class="fas fa-redo" />
            {{ $t(`common.email.redo`) }}
        </button>
    </div>
</template>

<style scoped>
    .input-group-append {
        display: inline;
        margin-left: 10px;
    }
    .error {
        color: red;
    }
    .sent {
        color: orange;
    }
    .received {
        color: green;
    }
</style>