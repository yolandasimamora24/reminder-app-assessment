@php
    use Ramsey\Uuid\Uuid;

    $column_name = $column['name'];
    $buttonText = '<span>Add Notes</span>';
    $notesCount = $entry[$column_name] ? count(json_decode($entry[$column_name])) : 0;

    if ($notesCount) {
        $buttonText = "<span>Show Notes</span> <span class='badge badge-light badge-pill'>$notesCount</span>";
    }
@endphp

<script>
    if (!$('#editable-notes-modal').length) {

        let emptyState = `
            <div class="h-100 d-flex justify-content-center align-items-center flex-column empty-state" style="min-height: 30vh">
                <div class="h1 mb-4">
                    <i class="fas fa-wind"></i>
                </div>
                <div class="h5 text-center">
                    There are no notes
                </div>
            </div>
        `

        let buttonDefaultState = `
            <i class="la la-sticky-note"></i> Add Notes
        `

        // Function to generate the unique code
        function generateUniqueCode() {
            const timestamp = new Date().getTime(); // Current timestamp
            const randomString = Math.random().toString(36).substring(2, 10); // Random alphanumeric string

            // Combine and hash the values to create a unique code
            return timestamp + '-' + randomString;
        }

        function adjustTextAreaHeight() {
            let chatBox = $('#editable-notes-modal .modal__chat-input')
            let chatContainer = $('#editable-notes-modal .modal__notes-container')

            // used to reset the height if the text inside the textarea is less than the container height
            chatBox.css({"height": 'auto'})


            // if the position is on the lowest scroll point, then make sure it's always scrolled to the bottom
            if (chatContainer[0].scrollTop >= chatContainer[0].scrollHeight - chatContainer[0].clientHeight) {
                modalContainerScrollToBottom()
            }

            // adjust the height to the text scroll height
            chatBox.css({"height": chatBox[0].scrollHeight})

            let chatBoxHeight = $('#editable-notes-modal .modal-footer').css('height')
            chatContainer.css({"height": `calc(70vh - ${chatBoxHeight})`})
        }


        // scroll the modal body to the bottom
        function modalContainerScrollToBottom() {
            let modalBody = $('#editable-notes-modal .modal-body')
            let containerHeight = $(modalBody).prop('scrollHeight')
            $(modalBody).scrollTop(containerHeight)
        }


        // restore form data, used for cancel edit or create a note
        function restoreFormData() {
            let form = $('#editable-notes-modal form')
            $(form).data('mode', 'create')
            $(form).removeData('id')
            $(form).find('textarea').val('')
        }


        function removeEditedText() {
            $('.modal__chat-edited-text').remove()
        }

        function noteSubmission() {

            let form = $('#editable-notes-modal form')
            let noteElement = $(form).find('textarea')
            let note = noteElement.val().trim()

            // do not process empty note
            if (!note.length) {
                return false
            }

            let formMode = $(form).data('mode') ?? 'create'
            let datasets = $(form).data()

            let {columnName, entryId, route, noteHistories} = datasets

            // if the datasets are not available, abort the processes
            if (datasets.length === 0) {
                alert('Something went wrong, please clear the cache and refresh the browser')
                return false
            }


            // create HTTP call to update/create note
            let callHttpServer = function (id, columnName, route, noteHistories) {
                return axios({
                    method: 'post',
                    url: `${route}`,
                    data: {
                        id: `${entryId}`,
                        attribute: `${columnName}`,
                        value: JSON.parse(JSON.stringify(noteHistories)),
                    }
                }).then(() => {

                    // remove the placeholder
                    let emptyState = $('.empty-state')
                    if ($(emptyState).length) {
                        $(emptyState).remove()
                    }
                })
            }


            // if only we use the edit a note instead of create a new note
            if (formMode === 'edit') {

                let id = $(form).data('id')

                // find the edited note
                for (let counter = 0; counter < noteHistories.length; counter++) {
                    // if the note already found, then stop the iteration
                    if (noteHistories[counter]['id'] === id) {
                        noteHistories[counter]['note'] = note
                        noteHistories[counter]['updated_at'] = "{{ now() }}"
                        break;
                    }
                }


                callHttpServer(entryId, columnName, route, noteHistories)
                    .then(() => {
                        $(`#editable-notes-modal .modal__chat[data-id*=${id}]`)
                            .data('note', note)
                            .find('.modal__user-chat')
                            .text(note)


                        // empty the text area
                        noteElement.val('')

                        // update the note histories
                        $(`button[data-entry-id=${entryId}].editable-notes-button`).data('note-histories', noteHistories)
                        $('#editable-notes-modal form').data('note-histories', noteHistories)

                        restoreFormData()
                        removeEditedText()
                        adjustTextAreaHeight()
                        modalContainerScrollToBottom()
                    })

            } else {

                let value = {
                    id: generateUniqueCode(),
                    user_id: "{{ backpack_auth()->id() }}",
                    full_name: "{{ backpack_auth()->user()->full_name() }}",
                    created_at: "{{ now() }}",
                    formatted_created_at: "{{ now()->isoFormat('MMM, Do YYYY - hh:mm A') }}",
                    note
                }

                // define the noteHistories as an array
                if (noteHistories == null || noteHistories === '') {
                    noteHistories = []
                }

                // add the record
                noteHistories.push(value)


                // get latest user id
                let latestUserId = $('.modal__chat:last').data('user-id')


                callHttpServer(entryId, columnName, route, noteHistories)
                    .then(() => {

                        // define the chat margin between chat box
                        let marginY = 'mt-2'
                        if (parseInt(latestUserId) !== parseInt(value['user_id'])) {
                            marginY = 'mt-4'
                        }

                        // append another note record
                        $('#editable-notes-modal .modal__notes-container').append(`
                            <div data-id="${value['id']}" data-user-id=${value['user_id']} data-note="${value['note']}" class="modal__chat position-relative rounded-lg p-2 mw-75 ${marginY}" style="background-color: #F1F4F8; align-self: end; color: #1B2A4E;">
                                <span title="edit|delete on click" class="edit-icon">
                                    <i class="las la-ellipsis-v"></i>
                                </span>
                                <div class="modal__user-group mb-2">
                                    <small class="d-block font-weight-bold">${value['full_name']}</small>
                                </div>
                                <p class="modal__user-chat break-spaces">${value['note']}</p>
                                <div class="modal__chat-date text-right">
                                    <small>${value['formatted_created_at']}</small>
                                </div>
                            </div>
                        `)
                    })
                    .then(() => {

                        // empty the text area
                        noteElement.val('')

                        // update the button data set and button text
                        $(`button[data-entry-id=${entryId}].editable-notes-button`).data('note-histories', noteHistories).html(`
                            <i class="la la-sticky-note"></i>
                            <span>Show Notes</span>
                            <span class='badge badge-light badge-pill'>${noteHistories.length}</span>
                        `)

                        $('#editable-notes-modal form').data('note-histories', noteHistories)

                        restoreFormData()
                        adjustTextAreaHeight()
                        modalContainerScrollToBottom()

                        let modalContainer = document.querySelector('#editable-notes-modal .modal__notes-container')
                        modalContainer.scrollTop = 10000
                    })

            }
        }


        function noteDeletion() {
            let form = $('#editable-notes-modal form')
            let datasets = $(form).data()
            let {id, columnName, entryId, route, noteHistories} = datasets


            // find the deleted note
            for (let counter = 0; counter < noteHistories.length; counter++) {
                // if the note already found, then stop the iteration
                if (noteHistories[counter]['id'] === id) {
                    noteHistories.splice(counter, 1);
                    break;
                }
            }


            axios({
                method: 'post',
                url: route,
                data: {
                    id: entryId,
                    attribute: columnName,
                    value: JSON.parse(JSON.stringify(noteHistories)),
                }
            }).then(() => {

                // selected button on table
                let selectedButton = $(`button[data-entry-id=${entryId}].editable-notes-button`)


                // remove the note
                $(`.modal__chat[data-id*=${id}]`).remove()
                $('#editable-notes-modal form').data('note-histories', noteHistories)


                // if there is no note then replace it with empty state
                if (noteHistories.length === 0) {
                    $('.modal__notes-container').html(emptyState)
                    $(selectedButton).html(buttonDefaultState)
                } else {
                    // update the button badge note histories length
                    $(selectedButton).data('note-histories', noteHistories).find('.badge').text(noteHistories.length)
                }

                adjustTextAreaHeight()

                restoreFormData()
            })
        }


        $('body').prepend(`
            <style>
                .hide-scrollbar::-webkit-scrollbar {
                    display: none;
                }

                .modal__notes-container {
                    max-height: 70vh;
                }

                /*.modal__notes-container.modal__notes-container--hidden-scroll::-webkit-scrollbar {*/
                /*    display: none;*/
                /*}*/

                .modal__chat {
                    width: fit-content;
                    max-width: 80%;
                }

                .break-spaces {
                    white-space: break-spaces;
                }

                .skeleton-loader {
                    width: 100%;
                    height: 15px;
                    display: block;
                    background: linear-gradient(
                        to right,
                        rgba(255, 255, 255, 0),
                        rgba(255, 255, 255, 0.5) 50%,
                        rgba(255, 255, 255, 0) 80%
                    ),
                    lightgray;
                    background-repeat: repeat-y;
                    background-size: 50px 500px;
                    background-position: 0 0;
                    animation: shine 1s infinite;
                }

                @keyframes shine {
                    to {
                        background-position: 100% 0, /* move highlight to right */ 0 0;
                    }
                }

                .space-y-1 > * {
                    margin-top: 0.5rem; /* You can adjust this value to control the vertical spacing */
                }
                .edit-icon {
                    float:right;
                    cursor: pointer;
                }
            </style>
        `).append(`
            <div id="editable-notes-modal" class="modal fade" tabindex="-1">
                <div class="modal-dialog modal-dialog-centered modal-lg">
                    <div class="modal-content">
                        <div class="modal-header border-0">
                            <h5 class="modal-title">Notes</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        </div>
                        <div class="modal-body modal__notes-container d-flex flex-column" style="overflow-y: auto; height: calc(70vh - 70px)"></div>
                        <div class="modal-footer flex-column border-0 p-0">
                            <form method="post" class="m-0 modal__input-container d-flex w-100 p-3 bg-light align-items-center justify-content-between" style="gap: 1rem; border-bottom-right-radius: 0.3rem; border-bottom-left-radius: 0.3rem;">
                                <label class="m-0 w-100">
                                    <textarea
                                        rows="1"
                                        class="modal__chat-input hide-scrollbar w-100 bg-transparent border-0"
                                        style="resize: none; outline: none; max-height: 150px;"
                                        name="note"
                                        placeholder="Type a note"></textarea>
                                </label>
                                <button type="submit" class="btn btn-primary rounded-pill align-self-end">Save</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            <div id="delete-notes-confirmation-modal" class="modal">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-body modal__notes-container d-flex flex-column justify-content-center align-items-center">
                            <div class="d-flex flex-column">
                                <span class="font-weight-bold">Do you want to delete the note?</span>
                                <p>The deleted note could not be recovered. Please mind your action.</p>
                            </div>

                            <div class="d-flex justify-content-center align-items-center" style="gap: 1rem">
                                <button class="btn btn-danger modal__delete-notes-confirmation-delete">Yes, delete</button>
                                <button class="btn modal__delete-notes-cancel-delete">No, cancel</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        `)


        // abort/cancel deletion confirmation
        $(document).on('click', '.modal__delete-notes-cancel-delete', function() {
            $('#delete-notes-confirmation-modal').modal('hide')
            restoreFormData()
        })


        // confirm deletion
        $(document).on('click', '.modal__delete-notes-confirmation-delete', function() {
            noteDeletion()
            $('#delete-notes-confirmation-modal').modal('hide')
            // $('#modal__chat-context-menu').remove()
            // adjustTextAreaHeight()
        })

        // set the consultation id on the modal
        $(document).on('click', '.editable-notes-button', function (e) {

            let datasets = $(e.target).closest('button.editable-notes-button').data()

            const unusableDatasets = ['toggle', 'target']

            // iterate through the data from the button
            for (let dataset in datasets) {

                // push dataset to the modal
                if (!unusableDatasets.includes(dataset)) {
                    let dashedDataset = dataset.replace(/([a-z])([A-Z])/g, '$1-$2').toLowerCase();
                    $('#editable-notes-modal form').data(dashedDataset, datasets[dataset])
                }
            }

            // empty the container
            let modalNoteContainer = $('#editable-notes-modal .modal__notes-container')
            $(modalNoteContainer).html('')

            // check if the histories exists
            if (datasets['noteHistories'].length) {

                let index = 0
                let marginTop = 'mt-2'
                let previousUserId = ''

                // append the chat to the container
                for (let noteHistory of datasets['noteHistories']) {

                    // differentiate margin between different person
                    if (noteHistory['user_id'] && previousUserId.toString() !== noteHistory['user_id'].toString()) {
                        previousUserId = noteHistory['user_id']
                        marginTop = 'mt-4'
                    } else {
                        marginTop = 'mt-2'
                    }

                    // ourselves chat color
                    let chatBackgroundColor = '#F1F4F8'
                    let chatColor = '#1B2A4E'
                    let chatPosition = 'end'

                    // other chat color
                    if (parseInt(noteHistory['user_id']) !== {{ backpack_auth()->id() }}) {
                        chatBackgroundColor = '#7C69EF'
                        chatColor = '#FFFFFF'
                        chatPosition = 'baseline'
                    }

                    let createdAt = noteHistory['formatted_created_at'] ?? noteHistory['created_at']
                    let fullName = noteHistory['full_name'] ?? noteHistory['user_id']

                    let chatBox = `
                        <div data-id="${noteHistory['id']}" data-user-id="${noteHistory['user_id']}" data-note="${noteHistory['note']}" class="modal__chat position-relative rounded-lg p-2 mw-75 ${marginTop}" style="background-color: ${chatBackgroundColor}; align-self: ${chatPosition}; color: ${chatColor};">
                            <span title="edit|delete on click" class="edit-icon">
                                <i class="las la-ellipsis-v"></i>
                            </span>
                            
                            <div class="modal__user-group mb-2">
                                <small class="d-block font-weight-bold">${fullName ?? ''}</small>
                            </div>
                            <p class="modal__user-chat break-spaces">${noteHistory['note']}</p>
                            <div class="modal__chat-date text-right">
                                <small>${createdAt}</small>
                            </div>
                        </div>
                    `

                    $(modalNoteContainer).append(chatBox)
                    index++
                }
            } else {

                // if the notes empty, then replace it with empty state
                $(modalNoteContainer).html(emptyState)
            }

            modalContainerScrollToBottom()
        })


        $('#editable-notes-modal').on('shown.bs.modal', function () {
            modalContainerScrollToBottom()
        })

        // remove context menu when left click on everywhere
        $(document).on('click', function (e) {
            if ($(e.target).parents(".edit-icon").length === 0 && !$(e.target).closest('#modal__chat-context-menu').length) {
                // remove the context menu if exists
                $('#modal__chat-context-menu').remove()
            }
        })
        
        // create click (context menu) for delete and edit note
        $(document).on('click', '.edit-icon', function (e) {
            e.preventDefault()
            if (!$(e.target).closest('#modal__chat-context-menu').length) {
                const contextMenuWidth = 160;
                const contextMenuHeight = 95;
                // Get the dimensions and position of the modal__chat element
                const $modalChat = $(e.currentTarget).closest('.modal__chat');
                const modalChatWidth = $modalChat.outerWidth();
                const modalChatHeight = $modalChat.outerHeight();
                const modalChatOffset = $modalChat.offset();
                // Calculate the idealX and idealY coordinates for the context menu
                let idealX = e.pageX - modalChatOffset.left;
                let idealY = e.pageY - modalChatOffset.top;
                // Check if the context menu would overflow on the right side
                if (idealX + contextMenuWidth > modalChatWidth) {
                    idealX = modalChatWidth - contextMenuWidth;
                }
                // Check if the context menu would overflow on the bottom
                if (idealY + contextMenuHeight > modalChatHeight) {
                    idealY = modalChatHeight - contextMenuHeight;
                }
                // remove the context menu if exists
                $('#modal__chat-context-menu').remove()

                $(e.currentTarget).closest('.modal__chat').append(`
                    <div id="modal__chat-context-menu" class="card rounded" style="z-index: 999; width: 10rem; left: ${idealX}px; top: ${idealY}px; position: absolute;">
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item p-1 modal__chat-edit">
                                <button class="btn w-100 text-left"><i class="las la-pen"></i> Edit</button>
                            </li>
                            <li class="list-group-item p-1 modal__chat-delete">
                                <button class="btn w-100 text-left"><i class="las la-trash"></i> Delete</button>
                            </li>
                        </ul>
                    </div>
                `)
            }
        });
        

        // click on edit on the context menu
        $(document).on('click', '.modal__chat-edit', function (e) {
            let modalChatData = $(e.target).closest('.modal__chat').data()
            let form = $('#editable-notes-modal form')

            $(form).data('id', modalChatData['id']).data('mode', 'edit')
            $('#editable-notes-modal form textarea').val(modalChatData['note'])

            // remove existing edited text if exists
            removeEditedText()

            $('.modal-footer').prepend(`
                <div class="d-flex gap-5 mb-3 ml-3 modal__chat-edited-text p-3 position-relative align-self-baseline rounded" style="gap: 1rem; width: 90%; background-color: #F1F4F8;">
                    <p class="hide-scrollbar w-100 break-spaces" style="max-height: 100px; overflow-y: auto;">${modalChatData['note']}</p>
                    <button type="button" class="modal__chat-cancel-edit btn rounded text-white align-self-baseline position-absolute d-flex justify-content-center align-items-center bg-danger" style="top: -10px; right: -10px; width: 30px; height: 30px;">
                        <i class="las la-times font-weight-bold" style="-webkit-text-stroke: 1px;"></i>
                    </button>
                </div>
            `)

            $('#modal__chat-context-menu').remove()
            adjustTextAreaHeight()
        })


        // cancel the edit mode, and set the form to "create" mode again
        $(document).on('click', '.modal__chat-cancel-edit', function () {
            removeEditedText()
            restoreFormData()
            adjustTextAreaHeight()
        })

        // click on edit and delete on the context menu
        $(document).on('click', '.modal__chat-delete', function (e) {

            // set form for deletion mode
            let modalChatData = $(e.target).closest('.modal__chat').data()
            let form = $('#editable-notes-modal form')

            $(form).data('id', modalChatData['id']).data('mode', 'delete').data('entry-id', modalChatData['entryId'])

            $('#delete-notes-confirmation-modal').modal('show')
            $('#modal__chat-context-menu').remove()
        })


        // set the stacked modal to appear above the editable notes modal (manually)
        $('#delete-notes-confirmation-modal').on('shown.bs.modal', function () {
            let zIndex = 1040 + ($('.modal-backdrop').length * 10)

            $('#delete-notes-confirmation-modal').css({
                'z-index': zIndex + 10
            })
            $('.modal-backdrop.show.fade').css({
                'z-index': zIndex
            })
        }).on('hidden.bs.modal', function () {

            let zIndex = 1030 + ($('.modal-backdrop').length + 10)

            $('.modal-backdrop.show.fade').css({
                'z-index': zIndex
            })
        })


        // submit form data
        $(document).on('submit', '#editable-notes-modal form', function (event) {
            event.preventDefault()
            noteSubmission()
        })


        // still form submission but using ctrl + enter
        $(document).on('keydown', '#editable-notes-modal form', function (event) {
            if (event.key === "Enter" && (event.metaKey || event.ctrlKey)) {
                event.preventDefault()
                noteSubmission()
            }
        })


        // expand the text area when the text > container
        $(document).on('keydown keyup', '#editable-notes-modal .modal__chat-input', function () {
            adjustTextAreaHeight()
        })
    }
</script>


<button
    type="button"
    class="editable-notes-button btn btn-sm btn-primary"
    data-toggle="modal"
    data-target="#editable-notes-modal"
    data-note-histories="{{ $entry[$column['name']] }}"
    data-route="{{ $column['route'] ?? url($crud->getRoute().'/minor-update') }}"
    data-column-name="{{ $column['name'] }}"
    data-entry-id="{{ $entry->getKey() }}"
>
    <i class="la la-sticky-note"></i> {!! $buttonText !!}
</button>
