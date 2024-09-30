$(document).ready(function() {
    $('select[name="status"]').change(function(){
        var selectedValue = $(this).val();
        if (selectedValue === 'pending') {
            $('select[name="type"]').val('scheduled');
        }
    });
})