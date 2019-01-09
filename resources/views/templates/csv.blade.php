@include('templates/modal')
<script type="text/x-template" id="csv-template">
    <modal>
        <span slot="header"><h3>@lang('form.csv.title')</h3></span>

        <div slot="body">

            <div class="alert alert-info" role="alert">
                <span class="fas fa-question-cirle"></span> @lang('form.csv.help', [
                        'excel' => '<a href="https://support.office.com/fr-fr/article/Importer-ou-exporter-des-fichiers-texte-txt-ou-csv-5250ac4c-663c-47ce-937b-339e391393ba" class="alert-link">',
                        'calc' => '<a href="https://help.libreoffice.org/Calc/Importing_and_Exporting_CSV_Files/fr" class="alert-link">',
                        'elink' => '</a>'
                    ])
            </div>

            @lang('form.csv.format')
            <table class="table table-bordered heavy-borders">
                <tbody><tr><td>@lang('form.csv.column1')</td><td>@lang('form.csv.column2')</td><td>@lang('form.csv.column3')</td></tr></tbody>
            </table>

            <div class="alert alert-danger" role="alert">
                @lang('form.csv.warning')
            </div>

            <form id="uploadCsv" @submit.prevent="emitSubmit" @reset="emitCancel">
                <input type="file" accept=".csv" required="required" />
            </form>
        </div>

        <div slot="footer">
            <button type="reset" form="uploadCsv" class="btn btn-warning"><span class="fas fa-stop-circle"></span> @lang('form.csv.cancel')</button>
            <button type="submit" form="uploadCsv" class="btn btn-primary"><span class="fas fa-upload"></span> @lang('form.csv.import')</button>
        </div>
    </modal>
</script>
