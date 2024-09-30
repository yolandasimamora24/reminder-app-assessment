$(document).ready(function() {
    $('[number-of-rows="0"]')
        .parents('.repeatable-group')
        .find('.add-repeatable-element-button')
        .click();
    $('#tab_personal-information .controls').hide();
    
})


