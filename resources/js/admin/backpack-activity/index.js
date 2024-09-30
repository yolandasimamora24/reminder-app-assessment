$(document).ready(function () {
    $('#crudTable').on('processing.dt', function (e, settings, processing) {
        const qs = window.location.search;
        const button = $('a[href*=export-csv]');
        const url = button.attr('href').split('?')[0];
        button.attr('href', url + qs);
    });
});
