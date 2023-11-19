<script>
    import { download, deepMerge } from '@/helpers.js';

    import { fetch } from '@/Modules/fetch.js';
    import Echo from '@/Modules/echo.js';

    import Tooltip from '@/Components/Tooltip.vue';
    import ParticipantRow from '@/Components/OrganizerParticipantRow.vue';

    export default {
        components: {
            Tooltip,
            ParticipantRow
        },
        props: {
            participants: {
                type: Object,
                required: true
            },
            draw: {
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
            finished() {
                return !!this.draw.finished_at;
            },
            endDateLong() {
                return new Date(this.draw.finished_at).toLocaleString('fr-FR', {day: 'numeric', month: 'long', year: 'numeric'});
            },
            deletionDateLong() {
                return new Date(this.draw.deletes_at).toLocaleString('fr-FR', {day: 'numeric', month: 'long', year: 'numeric'});
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
                            deletion: this.draw.deletes_at,
                            organizerName: this.draw.organizer.name,
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
                if(this.draw.next_solvable && !this.finished) {
                    message.body = this.$t('organizer.purge.confirm.body_final'); // Won't be able to download final recap + dearSanta
                } else if(this.finished) {
                    message.body = this.$t('organizer.purge.confirm.body_finished'); // Won't be able to download recap anymore
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
            resendTarget(k) {
                this.participants[k].mail.delivery_status = 'created';
                this.participants[k].mail.updated_at = new Date().getTime();

                return fetch(this.participants[k].resendTargetUrl);
            },
            updateEmail(k, email) {
                this.participants[k].email = email;
                this.participants[k].mail.delivery_status = 'created';
                this.participants[k].mail.updated_at = new Date().getTime();

                return fetch(this.participants[k].changeEmailUrl, 'POST', {
                    email: email
                }).catch(data => Promise.reject(data.errors.email[0]));
            },
            updateName(k, name) {
                this.participants[k].name = name;

                return fetch(this.participants[k].changeNameUrl, 'POST', {
                    name: name
                }).catch(data => Promise.reject(data.errors.name[0]));
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
                return fetch(this.participants[k].withdrawalUrl)
                    .then(data => {
                        this.$delete(this.participants, k);

                        this.$dialog
                            .alert(data.message);
                    });
            },
            download() {
                fetch(this.routes.csvInitUrl, 'GET', '', {responseType: 'blob'})
                    .then(response => {
                        download(response, 'secretsanta_'+this.draw.hash+'_init.csv', 'text/csv');
                    });
            },
            downloadPlus() {
                fetch(this.routes.csvFinalUrl, 'GET', '', {responseType: 'blob'})
                    .then(response => {
                        download(response, 'secretsanta_'+this.draw.hash+'_full.csv', 'text/csv');
                    });
            }
        }
    };
</script>

<template>
    <div>
        <div v-if="finished" class="alert alert-warning" role="alert">
            {{ $t('organizer.finished', {finished_at: endDateLong}) }}
        </div>
        <table class="table table-hover">
            <caption>{{ $t('organizer.list.caption') }}</caption>
            <thead>
                <tr class="table-active">
                    <th :style="finished ? 'width: 25%' : 'width: 33%'" scope="col">
                        {{ $t('organizer.list.name') }}
                    </th>
                    <th :style="finished ? 'width: 25%' : 'width: 33%'" scope="col">
                        {{ $t('organizer.list.email') }}
                    </th>
                    <th v-if="finished" style="width: 25%" scope="col">
                        {{ $t('organizer.list.target') }}
                    </th>
                    <th :style="canWithdraw ? 'width: 25%' : 'width: 33%'" scope="col">
                        {{ $t('organizer.list.status') }}
                    </th>
                    <th v-if="! finished && canWithdraw" style="width: 3%" scope="col">
                        {{ $t('organizer.list.withdraw') }}
                    </th>
                </tr>
            </thead>
            <tbody>
                <tr
                    is="vue:ParticipantRow"
                    v-for="(participant, k) in participants"
                    :key="participant.hash"
                    v-bind="participant"
                    :participants="participants"
                    :finished="finished"
                    :canWithdraw="canWithdraw"
                    :updateEmail="(email) => updateEmail(k, email)"
                    :updateName="(name) => updateName(k, name)"
                    @resend="() => resendTarget(k)"
                ></tr>
            </tbody>
        </table>
        <template v-if="!finished">
            <tooltip direction="top">
                <template #tooltip>
                    <picture>
                        <source srcset="../../images/srikanta-h-u-TrGVhbsUf40-unsplash.avif" type="image/avif" />
                        <source srcset="../../images/srikanta-h-u-TrGVhbsUf40-unsplash.webp" type="image/webp" />
                        <source srcset="../../images/srikanta-h-u-TrGVhbsUf40-unsplash.jpg" type="image/jpg" />
                        <img class="media-object" src="../../images/srikanta-h-u-TrGVhbsUf40-unsplash.jpg" />
                    </picture>
                    <div class="text-content">
                        <h3>{{ $t('organizer.end.button-tooltip.title') }}</h3>
                        <p>{{ $t('organizer.end.button-tooltip.content') }}</p>
                    </div>
                </template>
                <template #default>
                    <button type="button" class="btn btn-warning" @click="confirmEnd">
                        <i class="fas fa-calendar-check" />
                        {{ $t('organizer.end.button') }}
                    </button>
                </template>
            </tooltip>
        </template>
        <tooltip direction="top">
            <template #tooltip>
                <picture>
                    <source srcset="../../images/lynda-hinton-QyDLHeUerd4-unsplash.avif" type="image/avif" />
                    <source srcset="../../images/lynda-hinton-QyDLHeUerd4-unsplash.webp" type="image/webp" />
                    <source srcset="../../images/lynda-hinton-QyDLHeUerd4-unsplash.jpg" type="image/jpg" />
                    <img class="media-object" src="../../images/lynda-hinton-QyDLHeUerd4-unsplash.jpg" />
                </picture>
                <div class="text-content">
                    <h3>{{ $t('organizer.purge.button-tooltip.title') }}</h3>
                    <p>{{ $t('organizer.purge.button-tooltip.content') }}</p>
                </div>
            </template>
            <template #default>
                <button type="button" class="btn btn-danger" @click="confirmPurge">
                    <i class="fas fa-trash" />
                    {{ $t('organizer.purge.button', {deletes_at: deletionDateLong}) }}
                </button>
            </template>
        </tooltip>
        <template v-if="draw.next_solvable">
            <tooltip direction="top">
                <template #tooltip>
                    <picture>
                        <source srcset="../../images/rune-haugseng-UCzjZPCGV1Y-unsplash.avif" type="image/avif" />
                        <source srcset="../../images/rune-haugseng-UCzjZPCGV1Y-unsplash.webp" type="image/webp" />
                        <source srcset="../../images/rune-haugseng-UCzjZPCGV1Y-unsplash.jpg" type="image/jpg" />
                        <img class="media-object" src="../../images/rune-haugseng-UCzjZPCGV1Y-unsplash.jpg" />
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
            <tooltip direction="top">
                <template #tooltip>
                    <picture>
                        <source srcset="../../images/mike-arney-9r-_2gzP37k-unsplash.avif" type="image/avif" />
                        <source srcset="../../images/mike-arney-9r-_2gzP37k-unsplash.webp" type="image/webp" />
                        <source srcset="../../images/mike-arney-9r-_2gzP37k-unsplash.jpg" type="image/jpg" />
                        <img class="media-object" src="../../images/mike-arney-9r-_2gzP37k-unsplash.jpg" />
                    </picture>
                    <div class="text-content">
                        <h3>{{ $t('organizer.download.button_final-tooltip.title') }}</h3>
                        <p>{{ $t('organizer.download.button_final-tooltip.explain') }}</p>
                    </div>
                </template>
                <template #default>
                    <button :disabled="!finished" type="button" class="btn btn-primary" @click="downloadPlus">
                        <i class="fas fa-download" />
                        {{ $t('organizer.download.button_final') }}
                    </button>
                </template>
            </tooltip>
        </template>
        <tooltip v-else direction="top">
            <template #tooltip>
                <picture>
                    <source srcset="../../images/rune-haugseng-UCzjZPCGV1Y-unsplash.avif" type="image/avif" />
                    <source srcset="../../images/rune-haugseng-UCzjZPCGV1Y-unsplash.webp" type="image/webp" />
                    <source srcset="../../images/rune-haugseng-UCzjZPCGV1Y-unsplash.jpg" type="image/jpg" />
                    <img class="media-object" src="../../images/rune-haugseng-UCzjZPCGV1Y-unsplash.jpg" />
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

<style scoped>
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
