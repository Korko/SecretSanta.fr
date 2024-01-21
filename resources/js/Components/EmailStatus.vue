<script>
    import Tooltip from '@/Components/Tooltip.vue';

    export default {
        components: {
            Tooltip
        },
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
                default: 5*60*1000 // 5m delay config('mail.resend_delay')
            }
        },
        data: () => ({
            recent: true,
            recentUpdateTimeout: null
        }),
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
        <template v-if="can_redo || delivery_status === 'error'">
            <tooltip v-if="recent" direction="left">
                <template #tooltip>
                    <div class="text-content">
                        {{ $t(`common.email.recent`) }}
                    </div>
                </template>
                <template #default>
                    <button :disabled="true" type="button" class="btn btn-outline-secondary">
                        <i class="fas fa-redo" />
                        {{ $t(`common.email.redo`) }}
                    </button>
                </template>
            </tooltip>
            <button v-else :disabled="disabled" type="button" class="btn btn-outline-secondary" @click="$emit('redo')">
                <i class="fas fa-redo" />
                {{ $t(`common.email.redo`) }}
            </button>
        </template>
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

    .fa-spinner {
        animation-name: spin;
        animation-duration: 40000ms;
        animation-iteration-count: infinite;
        animation-timing-function: linear;
    }
    @keyframes spin {
        from {transform:rotate(0deg);}
        to {transform:rotate(360deg);}
    }
</style>
