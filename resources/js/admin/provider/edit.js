$(document).ready(function () {
    const compact_license = $("input[name='compact_license']");
    const license_state = $('select[name=license_state]');
    const home_license_state = $('select[name=home_license_state]');

    init();

    function init() {
        var hasCompactLicense = compact_license.val() == 1 ? true : false;
        home_license_state.prop('disabled', !hasCompactLicense);
        home_license_state.val(license_state.val()).change();
        license_state.prop('disabled', true);

        checkHomeLicenseState();
    }

    $("input[name='compact_license']").on('change', function() {
        var hasCompactLicense = $(this).val() == 1 ? true : false;
        home_license_state.prop('disabled', !hasCompactLicense);

        if (!hasCompactLicense) {
            resetCompactStates();
        } else {
            home_license_state.val(license_state.val()).change();
        }

        checkHomeLicenseState();
    });

    $("input[name='npi_number']").on('change', function() {
        const npiNumber = $(this).val();
        const providerId = $("input[name='id']").val();

        $('button[type="submit"]').attr('disabled', true);

        if (npiNumber !== '') {
            $.ajax({
                url: '/admin/provider/state/',
                data: {
                    'npi': npiNumber,
                    'provider': providerId,
                },
                type: 'GET',
                dataType: 'json',
                success: function success(resp) {
                    var { license_state, compact_states } = resp;
                    compact_states = JSON.parse(compact_states);
                    
                    if (license_state !== '') {
                        $("input[name='license_state_val']").val(license_state).change();
                        $('select[name=license_state]').val(license_state).change();
                        $('select[name=home_license_state]').val(license_state).change();
                        $('button[type="submit"]').attr('disabled', false);
                    }

                    var hasCompactLicense = compact_license.val() == 1 ? true : false;
                    if (compact_states.length > 0 && hasCompactLicense) {
                        checkCompactStates(compact_states);
                    }
                },
                error: function error(jqXHR, textStatus, errorThrown) {
                    console.error('Error:', textStatus, errorThrown);
                }
            });
        } else {
            $('select[name=license_state]').val('').change();
            $('select[name=home_license_state]').val('').change();
            $('button[type="submit"]').attr('disabled', false);
        }
    });

    $("select[name=home_license_state]").on('change', function() {
        const home_license_state = $(this).val();
        const providerId = $("input[name='id']").val();

        $('button[type="submit"]').attr('disabled', true);

        if (home_license_state !== '') {
            $.ajax({
                url: '/admin/provider/compact-states/',
                data: {
                    'state': home_license_state,
                    'provider': providerId,
                },
                type: 'GET',
                dataType: 'json',
                success: function success(resp) {
                    var { success, compact_states } = resp;
                    compact_states = JSON.parse(compact_states);
                    
                    var hasCompactLicense = compact_license.val() == 1 ? true : false;
                    if (success && compact_states.length > 0 && hasCompactLicense) {
                        checkCompactStates(compact_states);
                    } else {
                        resetCompactStates();
                    }

                    $('button[type="submit"]').attr('disabled', false);
                    checkHomeLicenseState();
                },
                error: function error(jqXHR, textStatus, errorThrown) {
                    console.error('Error:', textStatus, errorThrown);
                }
            });
        } else {
            resetCompactStates();
            $('button[type="submit"]').attr('disabled', false);
        }
    });

    function checkHomeLicenseState() {
        const statesInput = $(`div[bp-field-name="states"] input[value="${license_state.val()}"]`);
        statesInput.prop('checked', true).change();

        let states = $('input[name="states"]').val();
        let currentStates = (states != '') ? JSON.parse(states) : [];

        if (currentStates.length <= 0 || !currentStates.includes(license_state.val())) {
            currentStates.push(license_state.val());
        }

        $('input[name="states"]').val(JSON.stringify(currentStates));
    }

    function resetCompactStates() {
        const checkboxes = document.querySelectorAll('div[bp-field-name="states"] input[type="checkbox"]');

        checkboxes.forEach(checkbox => {
            checkbox.checked = false;
        });

        $('input[name="states"]').val('');
    }

    function checkCompactStates(states) {
        states.forEach(state => {
            let chkBox = $(`div[bp-field-name="states"] input[value="${state}"]`);

            if (chkBox.length) {
                chkBox.prop('checked', true).change();
            }
        });

        $('input[name="states"]').val(JSON.stringify(states));
    }
});