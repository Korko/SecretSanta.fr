@include('templates/modal')
<script type="text/x-template" id="csv-template">
    <modal>
        <span slot="header"><h3>@lang('form.csv.title')</h3></span>

        <div slot="body">
            @lang('form.csv.format')
            <table class="table table-bordered">
                <tbody><tr class="info"><td>@lang('form.csv.column1')</td><td>@lang('form.csv.column2')</td><td>@lang('form.csv.column3')</td></tr></tbody>
            </table>

            <div class="alert alert-danger" role="alert">
                @lang('form.csv.warning')
            </div>

            <form id="uploadCsv" @submit.prevent="emitSubmit" @reset="emitCancel">
                <input type="file" accept=".csv" required="required" />
            </form>
        </div>

        <div slot="footer">
            <button type="reset" form="uploadCsv" class="btn btn-warning"><span class="glyphicon glyphicon-cancel"></span> @lang('form.csv.cancel')</button>
            <button type="submit" form="uploadCsv" class="btn btn-primary"><span class="glyphicon glyphicon-upload"></span> @lang('form.csv.import')</button>
        </div>
    </modal>
</script>
