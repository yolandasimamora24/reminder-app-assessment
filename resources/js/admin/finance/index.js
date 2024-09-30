$(document).ready(function() {
    const cancellation_period = $("input[name=cancellation_period]");
    const cancellation_fee = $("input[name=cancellation_fee]");

    cancellation_period.add(cancellation_fee).prop("disabled", true);

    $("input.switch-input").on("change", function() {
        const isChecked = $(this).is(":checked");
        cancellation_period.add(cancellation_fee).prop("disabled", !isChecked);
    });
});