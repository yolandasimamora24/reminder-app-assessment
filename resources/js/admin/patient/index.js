$(document).ready(function() {
    // Use event delegation on a parent container
    $(".container-repeatable-elements").on("change", ".switch", function() {
        if ($(this).find(".switch-input").is(":checked")) {
            $(".switch-input").not(this).prop("checked", false);
            $(this).find(".switch-input").prop("checked", true);
            $("[data-repeatable-input-name='primary']").prop("value", 0);
            var originalString = $(this).find(".switch-input").attr('id');
            // Find the last index of underscore
            var lastIndex = originalString.lastIndexOf('_');
            // Remove everything after the last underscore
            var modifiedString = originalString.substring(0, lastIndex).replace('switch_', '');
            $('[name="' + modifiedString + '"]').val(1);
        }
    });

    $(".existing-file a, .existing-file img, .insurance-preview a, .insurance-preview img").each(function() {
        var attribute = $(this).attr("href") || $(this).attr("src");
        // Replace the URL if it matches the pattern
        var regex = /https?:\/\/[^/]+\/storage\//;
        if (regex.test(attribute)) {
            attribute = attribute.replace(regex, "/storage/insurance-images/");
            if ($(this).attr("href")) {
                $(this).attr("href", attribute);
            } else {
                $(this).attr("src", attribute);
            }
        }
    });

    function clearImage() {
        $("img")
            .filter(function() {
                return (
                    $(this).attr("src") === undefined ||
                    $(this).attr("src") === ""
                );
            })
            .attr("style", "display:none")
            .parent()
            .attr("data-fancybox", "false");
        $('input[type="file"]').attr('accept', 'image/png, image/jpeg, image/jpg, application/pdf');
    }

    $(".add-repeatable-element-button").click(function() {
        clearImage();
    });


    $(document).on('change', 'input[type="file"]', function() {
        // Get the selected file
        var fileInput = this;
        var file = fileInput.files[0];
        // Check if a file is selected
        if (file) {
            // Get the file type
            var fileType = file.type;

            // Allowed file types
            var allowedFileTypes = ["image/jpeg", "image/png", "image/jpg", "application/pdf"];

            // Check if the file type is in the allowed list
            if ($.inArray(fileType, allowedFileTypes) === -1) {
                alert("Please choose a valid PDF or image file.");
                // Clear the file input (optional)
                $(fileInput).val('');
                $('.backstrap-file-label').each(function(index, element) {
                    var value = $(element).text();
                    var result = isAllowedExtension($(element).html())
                    if (!result) {
                        $(element).html('');
                    }
                });
                return false;
            }

        }
    });

    var allowedExtensions = ['png', 'jpg', 'jpeg', 'pdf'];

    function isAllowedExtension(filename) {
        // Get the file extension
        var extension = filename.split('.').pop().toLowerCase();
        // Check if the extension is in the allowedExtensions array
        return $.inArray(extension, allowedExtensions) !== -1;
    }

    var count = $("input[data-repeatable-input-name='insurance_id_front']");

    for (i = 0; i < count.length; i++) {
        setImage(i, "front");
        setImage(i, "back");
    }

    function setImage(i, name) {
        let div = "insurance_id_" + name;
        let div_prev = "insurance_id_" + name + "_preview";

        // Select the <div> element with the specified attributes
        var $divContainer = $("div[bp-field-name='insurance." + i + "." + div + "']");

        // Find the <a> element within the <div> and get its href attribute value
        var hrefValue = $divContainer.find("a[target='_blank']").attr("href");

        if( typeof hrefValue !== 'undefined' ) {
            // Log the href value to the console
            var $divContainer = $("div[bp-field-name='insurance." + i + "." + div_prev + "']");

            // Find and update the <a> and <img> elements within the <div>
            $divContainer.find("a[data-fancybox='true']").attr("href", hrefValue);

            ext = hrefValue.split('.').pop().toLowerCase();

            if (ext == 'pdf') {
                const baseUrl = window.location.href.split('/admin')[0];
                hrefValue = baseUrl + '/images/pdf.png'
            }

            $divContainer.find("img.insurance-preview").attr("src", hrefValue);
        }
    }

    clearImage();
    
    $('.file_clear_button').click(function() {
        var a = $(this).attr('data-filename');
        $('img')
            .filter(function() {
                return this.src.match('/+a+/');
            }).addClass("full-width");

        var $aElement = $('a[href*="' + a + '"]');

        // Check if the <a> element was found
        if ($aElement.length > 0) {
            // Find the nearest <div> element and change its data-fancybox attribute
            var $divElement = $aElement.closest('div');
            $divElement.find('a').attr('data-fancybox', 'false');

            // Find the <img> element with the class "insurance-preview" and add the "d-none" class
            $aElement.find('img.insurance-preview').addClass('d-none');
        }
    });

    function init() {
        togglePregnantField();

        let weight = parseFloat($('input[name="weight"]').val());
        let height = parseFloat($('input[name="height"]').val());
        updateBMI(weight, height);
    }

    $('select[name="gender"]').click(function() {
        togglePregnantField();
    });

    function togglePregnantField() {
        const gender = $('select[name="gender"]');
        const pregnant = $('div[bp-field-name="pregnant"]');
        const isVisible = gender.val() === 'Female (F)';
        pregnant.css('visibility', isVisible ? 'visible' : 'hidden');
    }

    $('select[name="gender"]').click(togglePregnantField);

    function calculateBMI(weight, height) {
        let bmi = (weight * 703) / (height * height);
        return bmi.toFixed(2);
    }

    $('input[name="weight"]').on('change', function() {
        let weight = parseFloat($(this).val());
        let height = parseFloat($('input[name="height"]').val());
        updateBMI(weight, height);
    });

    $('input[name="height"]').on('change', function() {
        let height = parseFloat($(this).val());
        let weight = parseFloat($('input[name="weight"]').val());
        updateBMI(weight, height);
    });

    function updateBMI(weight, height) {
        let bmi = 0;

        if (weight >= 0 && height >= 0) {
            bmi = calculateBMI(weight, height);
        } else {
            bmi = "Please specify a valid height and weight...";
        }

        $('input[name="bmi"]').val(bmi);
    }

    init();
});