<template>
    <div>
        <div v-if="expired" class="alert alert-warning" role="alert">
            {{ $t('organizer.expired', {expires_at: expirationDateLong}) }}
        </div>
        <table class="table table-hover">
            <caption>{{ $t('organizer.list.caption') }}</caption>
            <thead>
                <tr class="table-active">
                    <th style="width: 33%" scope="col">
                        {{ $t('organizer.list.name') }}
                    </th>
                    <th style="width: 33%" scope="col">
                        {{ $t('organizer.list.email') }}
                    </th>
                    <th :style="canWithdraw ? 'width: 30%' : 'width: 33%'" scope="col">
                        {{ $t('organizer.list.status') }}
                    </th>
                    <th v-if="canWithdraw" style="width: 3%" scope="col">
                        {{ $t('organizer.list.withdraw') }}
                    </th>
                </tr>
            </thead>
            <tbody>
                <tr
                    is="vue:participant"
                    v-for="(participant, k) in participants"
                    :key="participant.hash"
                    v-bind="participant"
                    :participants="participants"
                    :expired="expired"
                    :canWithdraw="canWithdraw"
                    @update:email="(email) => updateEmail(k, email)"
                    @update:name="(name) => updateName(k, name)"
                ></tr>
            </tbody>
        </table>
        <button type="button" class="btn btn-danger" @click="confirmPurge">
            <i class="fas fa-trash" />
            {{ $t('organizer.purge.button') }}
        </button>
        <template v-if="draw.next_solvable">
            <tooltip direction="right">
                <template #tooltip>
                    <picture>
                        <source srcset="../../../images/rune-haugseng-UCzjZPCGV1Y-unsplash.webp" type="image/webp" />
                        <source srcset="../../../images/rune-haugseng-UCzjZPCGV1Y-unsplash.jpg" type="image/jpg" />
                        <img class="media-object" src="../../../images/rune-haugseng-UCzjZPCGV1Y-unsplash.jpg" />
                    </picture>
                    <div class="text-content">
                        <h3>{{ $t('organizer.download.button_initial-tooltip.title') }}</h3>
                        <p>{{ $t('organizer.download.button_initial-tooltip.content') }}</p>
                    </div>
                </template>
                <template #default>
                    <button type="button" class="btn btn-primary" @click="download">
                        <i class="fas fa-download" />
                        {{ $t('organizer.download.button_initial') }}
                    </button>
                </template>
            </tooltip>
            <tooltip direction="right">
                <template #tooltip>
                    <picture>
                        <source srcset="../../../images/mike-arney-9r-_2gzP37k-unsplash.webp" type="image/webp" />
                        <source srcset="../../../images/mike-arney-9r-_2gzP37k-unsplash.jpg" type="image/jpg" />
                        <img class="media-object" src="../../../images/mike-arney-9r-_2gzP37k-unsplash.jpg" />
                    </picture>
                    <div class="text-content">
                        <h3>{{ $t('organizer.download.button_final-tooltip.title') }}</h3>
                        <p>{{ $t('organizer.download.button_final-tooltip.explain') }}</p>
                        <p class="border border-white border-1 rounded pl-2 pr-2 font-italic">{{ $t('organizer.download.button_final-tooltip.limit', {expires_at: expirationDateLong, deleted_at: deletionDateLong}) }}</p>
                    </div>
                </template>
                <template #default>
                    <button :disabled="!expired" type="button" class="btn btn-primary" @click="downloadPlus">
                        <i class="fas fa-download" />
                        {{ $t('organizer.download.button_final') }}
                    </button>
                </template>
            </tooltip>
        </template>
        <tooltip v-else direction="right">
            <template #tooltip>
                <picture>
                    <source srcset="../../../images/rune-haugseng-UCzjZPCGV1Y-unsplash.webp" type="image/webp" />
                    <source srcset="../../../images/rune-haugseng-UCzjZPCGV1Y-unsplash.jpg" type="image/jpg" />
                    <img class="media-object" src="../../../images/rune-haugseng-UCzjZPCGV1Y-unsplash.jpg" />
                </picture>
                <div class="text-content">
                    <h3>{{ $t('organizer.download.button-tooltip.title') }}</h3>
                    <p>{{ $t('organizer.download.button-tooltip.content') }}</p>
                </div>
            </template>
            <template #default>
                <button type="button" class="btn btn-primary" @click="download">
                    <i class="fas fa-download" />
                    {{ $t('organizer.download.button') }}
                </button>
            </template>
        </tooltip>
    </div>
</template>

<script>
    /*import Vue from 'vue';

    import VuejsDialog from 'vuejs-dialog';
    Vue.use(VuejsDialog);*/

    import { download } from '../../partials/helpers.js';

    import Moment from 'moment';

    import fetch from '../../partials/fetch.js';
    import Echo from '../../partials/echo.js';
    import { deepMerge } from '../../partials/helpers.js';

    import Tooltip from '../tooltip.vue';
    import DefaultForm from '../form.vue';
    import Participant from './participant.vue';

    export default {
        components: {
            Tooltip,
            Participant
        },
        extends: DefaultForm,
        props: {
            participants: {
                type: Object,
                required: true
            },
            draw: {
                type: Object,
                required: true
            },
            changeEmailUrls: {
                type: Object,
                required: true
            },
            withdrawalUrls: {
                type: Object,
                required: true
            },
            routes: {
                type: Object,
                required: true
            }
        },
        computed: {
            canWithdraw() {
                return Object.keys(this.participants).length > 3;
            },
            checkUpdates() {
                return !!Object.values(this.participants).find(
                    participant => participant.mail.delivery_status !== 'error'
                );
            },
            expired() {
                return Moment(this.draw.expires_at).isBefore(Moment(), "day");
            },
            expirationDateShort() {
                return Moment(this.draw.expires_at).format('YYYY-MM-DD');
            },
            expirationDateLong() {
                return new Date(this.draw.expires_at).toLocaleString('fr-FR', {day: 'numeric', month: 'long', year: 'numeric'});
            },
            deletionDateLong() {
                return new Date(this.draw.deleted_at).toLocaleString('fr-FR', {day: 'numeric', month: 'long', year: 'numeric'});
            }
        },

        created() {
            Echo.channel('draw.'+this.draw.hash)
                .listen('.mail.update', data => {
                    var key = Object.keys(this.participants).find(key => this.participants[key].mail.id === data.id);

                    if(key) {
                        this.participants[key].mail.delivery_status = data.delivery_status;
                        this.participants[key].mail.updated_at = data.updated_at;
                    }
                });

            window.localStorage.setItem('secretsanta', JSON.stringify(
                deepMerge(
                    JSON.parse(window.localStorage.getItem('secretsanta')) || {},
                    {
                        [this.draw.hash]: {
                            title: this.draw.mail_title,
                            creation: this.draw.created_at,
                            expiration: this.draw.expires_at,
                            organizerName: this.draw.organizer_name,
                            links: {
                                org: {link: window.location.href}
                            }
                        }
                    }
                )
            ));
        },

        methods: {
            confirmPurge() {
                let options = {
                    okText: this.$t('organizer.purge.confirm.ok'),
                    cancelText: this.$t('organizer.purge.confirm.cancel'),
                    verification: this.$t('organizer.purge.confirm.value'),
                    verificationHelp: this.$t('organizer.purge.confirm.help'),
                    type: 'hard',
                    customClass: 'purge'
                };

                let message = {
                    title: this.$t('organizer.purge.confirm.title', {deletion: this.deletionDateLong}),
                    body: ''
                };
                if(this.draw.next_solvable && !this.expired) {
                    message.body = this.$t('organizer.purge.confirm.body_final'); // Won't be able to download final recap + dearSanta
                } else if(this.expired) {
                    message.body = this.$t('organizer.purge.confirm.body_expired'); // Won't be able to download recap anymore
                } else {
                    message.body = this.$t('organizer.purge.confirm.body_nofinal'); // Won't be able to download recap anymore + DearSanta
                }

                this.$dialog
                    .confirm(message, options)
                    .then(this.purge);
            },
            purge() {
                return fetch(this.routes.deleteUrl, 'DELETE')
                    .then(data => {
                        this.$dialog
                            .alert(data.message)
                            .then(() => window.location.pathname = '/');
                    });
            },
            updateEmail(k, email) {
                this.participants[k].email = email;
                this.participants[k].mail.delivery_status = 'created';
                this.participants[k].mail.updated_at = new Date().getTime();

                return fetch(this.changeEmailUrls[this.participants[k].hash], 'POST', {
                    email: email
                });
            },
            confirmWithdrawal(k) {
                let options = {
                    okText: this.$t('organizer.withdraw.confirm.ok'),
                    cancelText: this.$t('organizer.withdraw.confirm.cancel'),
                    verification: this.$t('organizer.withdraw.confirm.value'),
                    verificationHelp: this.$t('organizer.withdraw.confirm.help'),
                    type: 'hard',
                    customClass: 'withdraw'
                };

                this.$dialog
                    .confirm({
                        title: this.$t('organizer.withdraw.confirm.title', {deletion: this.deletionDateLong}),
                        body: this.$t('organizer.withdraw.confirm.body', {name: this.participants[k].name})
                    }, options)
                    .then(() => this.withdraw(k));
            },
            withdraw(k) {
                var url = this.withdrawalUrls[this.participants[k].hash];
                this.$delete(this.participants, k);

                return fetch(url)
                    .then(data => {
                        this.$dialog
                            .alert(data.message);
                    });
            },
            download() {
                fetch(this.routes.csvInitUrl, 'GET', '', {responseType: 'blob'})
                    .then(response => {
                        download(response, 'secretsanta_'+this.expirationDateShort+'_init.csv', 'text/csv');
                    });
            },
            downloadPlus() {
                fetch(this.routes.csvFinalUrl, 'GET', '', {responseType: 'blob'})
                    .then(response => {
                        download(response, 'secretsanta_'+this.expirationDateShort+'_full.csv', 'text/csv');
                    });
            }
        }
    };
</script>

<style>
    @import "~vuejs-dialog/dist/vuejs-dialog.min.css";

    .table td {
        vertical-align: middle !important;
    }

    table caption {
        display: none;
    }

    .purge .dg-btn--ok {
      color: #a82824;
      background-color: #fefefe;
      border-color: #a82824;
    }
</style>
