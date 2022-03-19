<script>
    import Modal from './Modal.vue';

    const jschardet = require("jschardet")

    export default {
        components: { Modal },
        data() {
            return {
                analyzing: false
            };
        },
        methods: {
            getEncoding: async function(file) {
                return new Promise((resolve, reject) => {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        let csvResult = e.target.result.split(/\r|\n|\r\n/);
                        resolve(jschardet.detect(csvResult.toString()).encoding);
                    }
                    reader.readAsBinaryString(file);
                });
            },
            emitSubmit: function() {
                const file = this.$refs.fileSelector.files[0];

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
                <input type="file" accept=".csv" required="required" ref="fileSelector" />
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
