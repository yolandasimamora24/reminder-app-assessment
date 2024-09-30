$(document).ready(function () {
    const baseUrl = window.location.href.split('/admin')[0];
    const location = $(`select[name="location"]`).val();
    showStates(location === 'us');

    $(`select[name="location"]`).on('change', function () {
        showStates($(this).val() === 'us');
    });

    function showStates(show) {
        if (show) {
            $('.job-states').removeClass('d-none');
        } else {
            $('.job-states').addClass('d-none');
        }
    }
});
