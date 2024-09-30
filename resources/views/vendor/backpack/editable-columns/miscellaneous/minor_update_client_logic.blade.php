@push('after_scripts')
    <script>
        function enableInsuranceRerunButton(input) {
            const input_fields = ['first_name', 'last_name', 'dob', 'member_id', 'insurance company'];
            if( input_fields.includes(input.name) ){
                const activeRow = input.parentElement.parentElement.parentElement;
                const rerunButton = activeRow.querySelector(".rerun-insurance");
                const edited = rerunButton.getAttribute('data-' + input.name) !== input.value;
                const shipped = rerunButton.getAttribute('data-shipped') === '1';
                const isBillingUser = rerunButton.getAttribute('data-is-billing-user') === '1';
                const eligibilityStatus = activeRow.querySelector("[name=eligibility_status]")?.checked ?? activeRow.querySelector("[data-input=eligibility_status]")?.text.trim() === 'Yes' ?? false;
                const eligibilityConfirmation = activeRow.querySelector("[name=eligibility_confirmation]")?.checked ?? activeRow.querySelector("[data-input=eligibility_confirmation]")?.text.trim() === 'Yes' ?? false;
                const disabled = shipped
                    ? (!isBillingUser)
                    : (!(isBillingUser || edited || !eligibilityConfirmation || !eligibilityStatus));
                if(disabled) {
                    rerunButton.classList.add('disabled');
                } else {
                    rerunButton.classList.remove('disabled');
                }
            }
        }

        // Adds functionality to submit AJAX requests for the 'editable_text' column type in Backpack
        //
        // @authors: Cristian Tabacitu, Antonio Almeida
        // @date: 2021-12-01
        // @versions: Backpack 4.1

        if (typeof activeElement === 'undefined') {
            var activeElement = {
                name: null,
                parent: null,
            };
        }

        if (typeof forceReloadAfterUpdate === 'undefined') {
            var forceReloadAfterUpdate = {{ var_export(\CRUD::getOperationSetting('forceReloadAfterUpdate') ?? false) }};
        }

        if (typeof registerMinorEditInputEvents === 'undefined') {

            var registerMinorEditInputEvents = function (element, e, confirmText = 'false') {
                e?.preventDefault();

                // Check if events are registered
                let container = element.closest('[data-column-editable]');
                if(container.dataset.columnEventsRegistered !== 'false') return;
                container.dataset.columnEventsRegistered = true;

                switch (element.type) {
                    case 'text':
                        textEventsHandler(element, e);
                        break;

                    case 'checkbox':
                        checkboxEventsHandler(element, e, confirmText);
                        break;

                    case 'select-one':
                        selectEventsHandler(element, e);
                        break;
                }

                // capture keys pressed inside the input for a better UX
                keydownHandler(container, container.querySelector('[tabindex]') || container.querySelector('input, select'));
            }

            // Helpers
            function focusElement(element) {
                if(!element) return;

                element.focus();
                if(element.type === 'text') {
                    element.setSelectionRange(element.parentElement.dataset.columnSelectOnClick ? 0 : element.value.length, element.value.length);
                }

                // Save current active element
                activeElement.name = document.activeElement.dataset.focus;
                activeElement.parent = document.activeElement.closest('tr');
            }

            function focusActiveElement() {
                let element = activeElement.parent?.querySelector(`[data-focus="${activeElement.name}"]`);
                focusElement(element);
            }

            function findEditableIndex(input) {
                let index = -1;
                input.closest('tr').querySelectorAll('[data-column-editable] input, [data-column-editable] select').forEach((element, i) => {
                    if(element === input) index = i;
                });
                return index;
            }

            function noty(type, text) {
                if(type && text) new Noty({ type, text }).show();
            }

            function keydownHandler(container, input) {
                // capture keys pressed inside the input for a better UX
                $(input).keydown(function (event) {

                    // on Enter submit the value with AJAX
                    if (event.key === 'Enter') {
                        $(this).trigger('click');

                        if(input.type === 'text') {
                            $(this).trigger('save');
                            $(this).trigger('blur');
                        }
                    }

                    // on Space submit the value with AJAX
                    if (event.key === ' ') {
                        if(container.dataset.columnType === 'checkbox') {
                            event.preventDefault();
                            $(this).trigger('click');
                        }
                    }

                    // Move backward
                    if (event.key === 'Tab') {
                        event.preventDefault();

                        $(this).trigger('save');
                        $(this).trigger('blur');

                        // Move forward
                        if(!event.shiftKey) {
                            // Find next editable input
                            let isEditable, isVisible, td = input.closest('td');
                            do {
                                td = td.nextElementSibling;
                                isEditable = td?.querySelector('[data-column-editable]');
                                isVisible = td?.style.display !== 'none';
                            } while(td && !isEditable);

                            if(td && isEditable && isVisible) {
                                return focusElement(td.querySelector('[tabindex]') || td.querySelector('input, select'));
                            }

                            // go to next line
                            let tr = input.closest('tr');
                            if(tr.classList.contains('dt-hasChild')) tr = tr.nextElementSibling;
                            if(tr.nextElementSibling) {
                                return focusElement(tr.nextElementSibling.querySelector('[data-column-editable] input'));
                            }

                            // go back to first line
                            return focusElement(input.closest('tbody').firstElementChild.querySelector('[data-column-editable] input'));
                        }

                        // Move backward
                        if(event.shiftKey) {
                            // Find next editable input
                            let isEditable, td = input.closest('td');
                            do {
                                td = td.previousElementSibling;
                                isEditable = td?.querySelector('[data-column-editable]');
                                isVisible = td?.style.display !== 'none';
                            } while(td && !isEditable);

                            if(td && isEditable && isVisible) {
                                return focusElement(td.querySelector('[tabindex]') || td.querySelector('input, select'));
                            }

                            // go to previous line
                            let queryEditableTd = 'td:not([style="display: none;"]) [data-column-editable]';
                            let tr = input.closest('tr').previousElementSibling;
                            if(tr?.previousElementSibling?.classList.contains('dt-hasChild')) tr = tr.previousElementSibling;
                            if(tr) {
                                let editables = tr.querySelectorAll(`${queryEditableTd} input, ${queryEditableTd} [tabindex]`);
                                return focusElement(editables[editables.length - 1]);
                            }

                            // go back to last line
                            let editables = input.closest('tbody').lastElementChild.querySelectorAll(`${queryEditableTd} input, ${queryEditableTd} [tabindex]`);
                            return focusElement(editables[editables.length - 1]);
                        }
                    }

                    // On Up move up one row
                    if (event.key === 'ArrowUp') {
                        event.preventDefault();
                        $(this).trigger('save');

                        // find input index
                        let index = findEditableIndex(input);

                        // finx previous row
                        let tr = input.closest('tr').previousElementSibling;
                        if(tr?.previousElementSibling?.classList.contains('dt-hasChild')) tr = tr.previousElementSibling;
                        if(tr) {
                            return focusElement(tr.querySelectorAll('[data-column-editable] input, [data-column-editable] select')[index]);
                        }
                    }

                    // On Down move down one row
                    if (event.key === 'ArrowDown') {
                        event.preventDefault();
                        $(this).trigger('save');

                        // find input index
                        let index = findEditableIndex(input);

                        // find next row
                        let tr = input.closest('tr');
                        if(tr.classList.contains('dt-hasChild')) tr = tr.nextElementSibling;
                        if(tr.nextElementSibling) {
                            return focusElement(tr.nextElementSibling.querySelectorAll('[data-column-editable] input, [data-column-editable] select')[index]);
                        }
                    }

                    // On Escape cancel everything
                    if (event.key === 'Escape') {
                        event.preventDefault();

                        // hide the input and show the text
                        input.value = input.parentElement.dataset.columnInitialValue;
                        $(this).trigger('blur');
                    }
                });
            }

            function saveCrudTableElements(element, i = 0) {
                saveDetailsRowState(element, i);
                saveBulkActionState(element, i);
            }

            function restoreCrudTableElements(cell) {
                restoreBulkActionState(cell);
                restoreDetailsRowState(cell);
            }

            function saveDetailsRowState(element, i = 0) {
                let hasDetailsRow = element.closest('.dataTable').dataset.hasDetailsRow === '1';
                let tr = element.closest('tr');

                // Temporarly move details control to body
                if(hasDetailsRow && i === 0) {
                    document.body.appendChild(tr.querySelector('.details-control'));
                }
            }

            function saveBulkActionState(element, i = 0) {
                let hasBulkActions = element.closest('.dataTable').dataset.hasBulkActions === '1';
                // Temporarly move the bulk checkbox to the  body
                if(hasBulkActions && i === 0) {
                    let rowSibling = element.parentElement.previousElementSibling;
                    while(rowSibling) {
                        if(rowSibling.classList.contains('crud_bulk_actions_checkbox')) {
                            document.body.appendChild(rowSibling);
                            break;
                        }
                        rowSibling = rowSibling.previousElementSibling;
                    }
                }
            }

            function restoreDetailsRowState(cell) {
                // Move details control back to cell node
                if(document.body.lastElementChild.classList.contains('details-control')) {
                    cell.node().prepend(document.body.lastElementChild);
                }
            }

            function restoreBulkActionState(cell) {
                // Move bulk actions back to cell node
                if(document.body.lastElementChild.classList.contains('crud_bulk_actions_checkbox')) {
                    cell.node().prepend(document.body.lastElementChild);
                }
            }

            function updateDependentColumns(element, data) {
                let stringCleaner = /[^\w\d]/gi;
                let insideModal = element.closest('.modal');
                let tr = element.closest('tr');
                let td = element.closest('td');
                let trIndex = [...element.closest('tr').parentElement.children].filter(e => !e.previousElementSibling?.classList.contains('shown')).indexOf(element.closest('tr'));
                let tdIndex = [...td.parentElement.children].indexOf(td);
                let hasDetailsRow = element.closest('.dataTable').dataset.hasDetailsRow === '1';

                // add updated class to the row
                let setUpdated = (element, content) => {
                    if(element.innerHTML.replaceAll(stringCleaner, '') !== content.replaceAll(stringCleaner, '')) {
                        element.classList.add('updated');
                        setTimeout(() => element.classList.remove('updated'), 2000);
                    }
                };

                // if it's inside modal
                if(insideModal) {
                    tableColumns = element.closest('tbody').querySelectorAll('tr > td:last-child');
                    trIndex = Number(element.closest('tr').dataset.dtRow);
                    tdIndex = Number(element.closest('tr').dataset.dtColumn);

                    // update the values in modal
                    data.row?.forEach((content, i) => {
                        // avoid updating the same column
                        if(tdIndex !== i) {
                            setUpdated(tableColumns[i], content);
                            tableColumns[i].innerHTML = content;
                        }
                    });
                }

                // update table data
                data.row?.forEach((content, i) => {
                    // avoid updating the same column
                    if(tdIndex !== i) {
                        saveCrudTableElements(element, i);

                        // Update cell content
                        let cell = crud.table.cell(trIndex, i);
                        setUpdated(cell.node(), content);
                        cell.data(content);

                        // remove details-control from the request
                        cell.node().querySelector('.details-control')?.remove();

                        restoreCrudTableElements(cell);
                    }
                });

                // get back focus to the active input
                focusActiveElement();
            }

            function completeRequest() {
                if(forceReloadAfterUpdate) {
                    window.crud.table.ajax.reload();

                    return;
                }
                // Re-apply formatting to action column as dropdown
                if (typeof formatActionColumnAsDropdown !== 'undefined' && $('#crudTable').data('has-line-buttons-as-dropdown')) {
                    formatActionColumnAsDropdown();
                }
            }

            // Handlers
            function textEventsHandler(input, e) {
                // select all if select-on-click is selected
                if(input.parentElement.dataset.columnSelectOnClick) {
                    input.setSelectionRange(0, input.value.length);
                }

                // on blur, if the value has been CHANGED but not SAVED, make the text grey
                $(input).on('blur', function(event) {
                    if (input.value !== input.parentElement.dataset.columnInitialValue) {
                        if(input.parentElement.dataset.columnSaveOnFocusout) {
                            $(input).trigger('save');
                        } else {
                            input.style.color = input.parentElement.dataset.textColorUnsaved;
                        }
                    }
                });

                // on save, make an AJAX request and interpret the result
                $(input).on('save', function(event) {
                    event.preventDefault();

                    // if no changes were actually made, don't make an AJAX request
                    if (input.value === input.parentElement.dataset.columnInitialValue || input.sync) {
                        return;
                    }

                    // save current focused element
                    focusElement(input);

                    let onSaveRequestError = () => {
                        textAnimationHandler(false, input, input.parentElement.dataset);

                        // Undo value
                        if(input.parentElement.dataset.onErrorTextValueUndo) {
                            input.value = input.parentElement.dataset.columnInitialValue;
                        }
                    }

                    // submit the value to be saved
                    input.sync = true;
                    $.ajax({
                        url: input.parentElement.dataset.route + window.location.search,
                        type: 'POST',
                        data: {
                            id: input.parentElement.dataset.entryId,
                            attribute: input.parentElement.dataset.columnName,
                            value: input.value,
                            row: input.closest('tr')._DT_RowIndex,
                            fake: input.parentElement.dataset.fake,
                            ...window.crud.table.ajax.params(),
                        },
                        success: result => {
                            // noty the user about the result
                            noty(result['bubble_type'], result['bubble_message']);

                            // if the result was not saved, make the text red for ever
                            // to indicate to the user that it was NOT SAVED
                            if (!result['saved']) {
                                onSaveRequestError();
                                return;
                            }

                            // Update initial value
                            input.parentElement.dataset.columnInitialValue = input.value;

                            // if the used input was in a modal update the contents of the related table input
                            if (typeof crud !== 'undefined' && crud.table.responsive !== false) {
                                if(input.closest('.modal')) {
                                    let { columnName, entryId } = input.parentElement.dataset;
                                    let inputInTable = document.querySelector(`#crudTable [data-column-editable][data-column-name="${columnName}"][data-entry-id="${entryId}"] input`);
                                    inputInTable.value = inputInTable.parentElement.dataset.columnInitialValue = input.value;
                                } else {
                                    let container = input.closest('[data-column-editable]');
                                    container.dataset.columnEventsRegistered = 'false';
                                    container.dataset.columnInitialValue = input.value;
                                    input.setAttribute('value', input.value);

                                    saveCrudTableElements(input);

                                    // update cell html
                                    let cell = crud.table.cell(input.closest('td'));
                                    cell.data(input.closest('td').innerHTML);

                                    // update input reference
                                    input = document.querySelector(`#crudTable [data-column-editable][data-column-name="${input.parentElement.dataset.columnName}"][data-entry-id="${input.parentElement.dataset.entryId}"] input`);

                                    restoreCrudTableElements(cell);
                                }
                            }

                            // if the result is just true, make the text green for a while
                            // to indicate to the user that it WAS SAVED
                            textAnimationHandler(true, input, input.parentElement.dataset);

                            // update dependants
                            if(input.parentElement.dataset.autoUpdateRow) {
                                updateDependentColumns(input, result);
                            }

                            completeRequest();
                            /* Enables the insurance rerun button */
                            if (window.location.href.indexOf("/admin/insurance") !== -1) {
                                enableInsuranceRerunButton(input);
                            }
                        },
                        error: result => {
                            noty('error', result?.responseJSON?.message || '{{ __('backpack.editable-columns::minor_update.error_saving') }}');
                            onSaveRequestError();
                        },
                        complete: result => input.sync = false,
                    });
                });
            }

            function textAnimationHandler(success, element, data) {
                let color = success ? data.onSuccessTextColor : data.onErrorTextColor;
                let duration = success ? data.onSuccessTextColorDuration : data.onErrorTextColorDuration;

                // Set color
                element.style.color = color;

                // Animate
                if(Number(duration) !== 0) {
                    setTimeout(() => element.style.color = '', duration);
                }
            }

            function checkboxEventsHandler(input, e, confirmText) {
                // on save, make an AJAX request and interpret the result
                $(input).on('save', function(event) {
                    event.preventDefault();

                    let container = input.closest('[data-column-editable]');
                    let status = container.querySelector('.ec-status');

                    if(confirmText && confirmText !== 'false') {
                        if (!confirm(confirmText)) {
                            input.checked = !input.checked;
                            return;
                        }
                    }

                    // if not changes were actually made, don't make an AJAX request
                    if (Number(input.checked) === Number(container.dataset.columnInitialValue) || input.sync) {
                        return;
                    }

                    // save current focused element
                    focusElement(input);

                    let onSaveRequestError = () => {
                        // if the result is just true, make the text red for ever
                        // to indicate to the user that it was NOT SAVED
                        checkboxAnimationHandler(false, status, container.dataset);

                        // Undo value
                        input.checked = !input.checked;
                    }

                    // submit the value to be saved
                    input.sync = true;
                    $.ajax({
                        url: container.dataset.route + window.location.search,
                        type: 'POST',
                        data: {
                            id: container.dataset.entryId,
                            attribute: container.dataset.columnName,
                            value: Number(input.checked),
                            row: input.closest('tr')._DT_RowIndex,
                            fake: container.dataset.fake,
                            ...window.crud.table.ajax.params(),
                        },
                        success: result => {
                            // if the result is a bubble notification, show it
                            noty(result['bubble_type'], result['bubble_message']);

                            if (!result['saved']) {
                                onSaveRequestError();
                                return;
                            }

                            // if the used input was in a modal update the contents of the related table input
                            if (typeof crud !== 'undefined' && crud.table.responsive !== false) {
                                if(input.closest('.modal')) {
                                    let inputInTable = document.querySelector(`#crudTable [data-column-editable][data-column-name="${container.dataset.columnName}"][data-entry-id="${container.dataset.entryId}"] input`);
                                    inputInTable.checked = inputInTable.closest('[data-column-type="checkbox"]').dataset.columnInitialValue = input.checked;
                                } else {
                                    container.dataset.columnEventsRegistered = 'false';
                                    container.dataset.columnInitialValue = Number(input.checked);

                                    input.checked ? input.setAttribute('checked', true) : input.removeAttribute('checked');

                                    saveCrudTableElements(input);

                                    // update cell html
                                    let cell = crud.table.cell(input.closest('td'));
                                    cell.data(input.closest('td').innerHTML);

                                    // update input reference
                                    input = document.querySelector(`#crudTable [data-column-editable][data-column-name="${container.dataset.columnName}"][data-entry-id="${container.dataset.entryId}"] input`);
                                    status = input.closest('[data-column-editable]').querySelector('.ec-status');

                                    restoreCrudTableElements(cell);
                                }
                            }

                            // if the result is just true, make the text green for a while
                            // to indicate to the user that it WAS SAVED
                            checkboxAnimationHandler(true, status, container.dataset);

                            // update dependants
                            if(container.dataset.autoUpdateRow) {
                                updateDependentColumns(input, result);
                            }

                            completeRequest();

                            /* Enables the insurance rerun button */
                            if (window.location.href.indexOf("/admin/insurance") !== -1) {
                                enableInsuranceRerunButton(input);
                            }
                        },
                        error: result => {
                            noty('error', result?.responseJSON?.message || '{{ __('backpack.editable-columns::minor_update.error_saving') }}');
                            onSaveRequestError();
                        },
                        complete: result => input.sync = false,
                    });
                });

                // capture keys pressed inside the input for a better UX
                input.onchange = function () {
                    $(input).trigger('save');
                    $(input).trigger('focus');
                }

                // Checkboxes send this event
                if (e.type === 'change') {
                    $(input).trigger('save');
                    $(input).trigger('focus');
                }

                input.closest('[data-column-type="checkbox"]').onclick = e => {
                    e.stopPropagation();
                }
            }

            function checkboxAnimationHandler(success, element, data) {
                let color = success ? data.onSuccessStatusColor : data.onErrorStatusColor;
                let duration = success ? data.onSuccessStatusColorDuration : data.onErrorStatusColorDuration;

                // Set color
                element.style.backgroundColor = color;

                // Animate
                if(Number(duration) !== 0) {
                    setTimeout(() => element.style.backgroundColor = '', duration);
                }
            }

            function selectEventsHandler(input, e, confirmText) {
                // on blur, if the value has been CHANGED but not SAVED, make the text grey
                $(input).on('blur', function(event) {
                    if (input.value !== input.parentElement.dataset.columnInitialValue) {
                        $(input).trigger('save');
                    }
                });

                // on change, add the listener only in case the user has it enabled
                if(input.parentElement.dataset.columnSaveOnChange) {
                    $(input).on('change', function(event) {
                        if (input.value !== input.parentElement.dataset.columnInitialValue) {
                            $(input).trigger('save');
                        }
                    });
                }

                // on save, make an AJAX request and interpret the result
                $(input).on('save', function(event) {
                    event.preventDefault();

                    // if no changes were actually made, don't make an AJAX request
                    if (input.value === input.parentElement.dataset.columnInitialValue || input.sync) {
                        return;
                    }

                    // save current focused element
                    focusElement(input);

                    let onSaveRequestError = () => {
                        textAnimationHandler(false, input, input.parentElement.dataset);

                        // Undo value
                        if(input.parentElement.dataset.onErrorTextValueUndo) {
                            input.value = input.parentElement.dataset.columnInitialValue;
                        }
                    }

                    // submit the value to be saved
                    input.sync = true;
                    $.ajax({
                        url: input.parentElement.dataset.route + window.location.search,
                        type: 'POST',
                        data: {
                            id: input.parentElement.dataset.entryId,
                            attribute: input.parentElement.dataset.columnName,
                            value: input.value,
                            row: input.closest('tr')._DT_RowIndex,
                            fake: input.parentElement.dataset.fake,
                            ...window.crud.table.ajax.params(),
                        },
                        success: result => {
                            // noty the user about the result
                            noty(result['bubble_type'], result['bubble_message']);

                            // if the result was not saved, make the text red for ever
                            // to indicate to the user that it was NOT SAVED
                            if (!result['saved']) {
                                onSaveRequestError();
                                return;
                            }

                            // Update initial value
                            input.parentElement.dataset.columnInitialValue = input.value;

                            // if the used input was in a modal update the contents of the related table input
                            if (typeof crud !== 'undefined' && crud.table.responsive !== false) {
                                if(input.closest('.modal')) {
                                    let { columnName, entryId } = input.parentElement.dataset;
                                    let inputInTable = document.querySelector(`#crudTable [data-column-editable][data-column-name="${columnName}"][data-entry-id="${entryId}"] select`);

                                    inputInTable.value = inputInTable.parentElement.dataset.columnInitialValue = input.value;
                                } else {
                                    let container = input.closest('[data-column-editable]');
                                    container.dataset.columnEventsRegistered = 'false';
                                    container.dataset.columnInitialValue = input.value;
                                    input.setAttribute('value', input.value);

                                    // update selected option before updating cell html
                                    input.querySelector('[selected]')?.removeAttribute('selected');
                                    input.querySelector(`[value="${input.value}"]`)?.setAttribute('selected', 'selected');

                                    saveCrudTableElements(input);

                                    // update cell html
                                    let cell = crud.table.cell(input.closest('td'));
                                    cell.data(input.closest('td').innerHTML);

                                    // update input reference
                                    input = document.querySelector(`#crudTable [data-column-editable][data-column-name="${input.parentElement.dataset.columnName}"][data-entry-id="${input.parentElement.dataset.entryId}"] select`);

                                    restoreCrudTableElements(cell);
                                }
                            }

                            // if the result is just true, make the text green for a while
                            // to indicate to the user that it WAS SAVED
                            textAnimationHandler(true, input, input.parentElement.dataset);

                            // update dependants
                            if(input.parentElement.dataset.autoUpdateRow) {
                                updateDependentColumns(input, result);
                            }

                            completeRequest();

                            /* Enables the insurance rerun button */
                            if (window.location.href.indexOf("/admin/insurance") !== -1) {
                                enableInsuranceRerunButton(input);
                            }
                        },
                        error: result => {
                            noty('error', result?.responseJSON?.message || '{{ __('backpack.editable-columns::minor_update.error_saving') }}');
                            onSaveRequestError();
                        },
                        complete: result => input.sync = false,
                    });
                });
            }
        }
    </script>
@endpush

@push('after_styles')
    <style>
        @keyframes updated {
            0%, 50% { color: #869ab8; }
            100% { background-color: initial; }
        }
        .updated {
            animation: updated 2s linear 0s 1 normal forwards;
        }
    </style>
@endpush
