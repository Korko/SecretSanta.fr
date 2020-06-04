<script>
    import Modal from './modal.vue';
    export default {
        components: { Modal },
        methods: {
            emitSubmit: function() {
                this.$emit('import', $('#uploadCsv input[type=file]')[0].files[0]);
                this.$emit('close');
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
            <button class="btn btn-warning" @click="$refs.reset.click()">
                <span class="fas fa-stop-circle" />
                {{ $t('form.csv.cancel') }}
            </button>
            <button class="btn btn-primary" @click="$refs.submit.click()">
                <span class="fas fa-upload" />
                {{ $t('form.csv.import') }}
            </button>
        </template>
    </modal>
</template>
