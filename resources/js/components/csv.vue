<script>
    import Modal from './modal.vue';

    export default {
        components: { Modal },
        data() {
            return {
                analyzing: false
            };
        },
        methods: {
            // ponytail: UTF-8, else Latin-1 family; add a real detector if other charsets show up
            getEncoding: async function(file) {
                const bytes = new Uint8Array(await file.arrayBuffer());
                try {
                    new TextDecoder('utf-8', { fatal: true }).decode(bytes);
                    return 'UTF-8';
                } catch (e) {
                    // 0x80-0x9F are printable only in Windows-1252 (€ œ ’...), control chars in ISO-8859-x
                    if (bytes.some(b => b >= 0x80 && b <= 0x9F)) return 'windows-1252';
                    // € Š š Ž ž Œ œ Ÿ in ISO-8859-15 (¤ ¦ ¨ ´ ¸ ¼ ½ ¾ in ISO-8859-1, unlikely in names)
                    if (bytes.some(b => [0xA4, 0xA6, 0xA8, 0xB4, 0xB8, 0xBC, 0xBD, 0xBE].includes(b))) return 'iso-8859-15';
                    return 'iso-8859-1';
                }
            },
            emitSubmit: function() {
                const file = this.$el.querySelector('#uploadCsv input[type=file]').files[0];

                this.analyzing = true;
                this.getEncoding(file).then(encoding => {
                    this.$emit('import', file, encoding);
                    this.$emit('close');
                    this.analyzing = false;
                });
            },
            emitCancel: function() {
                this.$emit('close');
            }
        }
    };
</script>

<template>
    <modal @close="$emit('close')">
        <template #header>
            <h3>{{ $t('form.csv.title') }}</h3>
        </template>

        <template #body>
            <div class="alert alert-info" role="alert">
                <span class="fas fa-question-cirle" />
                <span
                    v-html="
                        $t('form.csv.help', {
                            excel:
                                '<a href=\'https://support.office.com/fr-fr/article/Importer-ou-exporter-des-fichiers-texte-txt-ou-csv-5250ac4c-663c-47ce-937b-339e391393ba\' class=\'alert-link\'>',
                            calc:
                                '<a href=\'https://help.libreoffice.org/Calc/Importing_and_Exporting_CSV_Files/fr\' class=\'alert-link\'>',
                            elink: '</a>'
                        })
                    "
                />
            </div>

            {{ $t('form.csv.format') }}
            <table class="table table-bordered heavy-borders">
                <tbody>
                    <tr>
                        <td>{{ $t('form.csv.column1') }}</td>
                        <td>{{ $t('form.csv.column2') }}</td>
                        <td>{{ $t('form.csv.column3') }}</td>
                    </tr>
                </tbody>
            </table>

            <div class="alert alert-danger" role="alert">
                {{ $t('form.csv.warning') }}
            </div>

            <form id="uploadCsv" @submit.prevent="emitSubmit" @reset="emitCancel">
                <input type="file" accept=".csv" required="required" />
                <button class="hidden" type="submit" ref="submit"></button>
                <button class="hidden" type="reset" ref="reset"></button>
            </form>
        </template>

        <template #footer>
            <button v-if="analyzing" :disabled="true" class="btn btn-primary">
                <span class="fas fa-upload" />
                {{ $t('form.csv.analyzing') }}
            </button>
            <template v-else>
                <button class="btn btn-warning" @click="$refs.reset.click()">
                    <span class="fas fa-stop-circle" />
                    {{ $t('form.csv.cancel') }}
                </button>
                <button class="btn btn-primary" @click="$refs.submit.click()">
                    <span class="fas fa-upload" />
                    {{ $t('form.csv.import') }}
                </button>
            </template>
        </template>
    </modal>
</template>
