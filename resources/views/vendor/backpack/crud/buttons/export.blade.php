<a href="{{ config('app.url') . '/' . $crud->route . '/export-csv' }}" class="btn btn-primary export-btn" data-style="zoom-in">
    <span class="ladda-label">
        <i class="la la-download"></i>{{ trans('backpack::crud.export.export') }}
    </span>
</a>
<style>
    .export-btn:not(:first-child) {
        margin-left: 0.5rem !important;
    }
</style>
