@php
    use Ramsey\Uuid\Uuid;

    $column_name = $column['name'];
    $buttonText = '<span>Upload File</span>';
    $icon = 'la-cloud-upload-alt';
    $fileCount = $entry[$column_name] ? count(json_decode($entry[$column_name])) : 0;

    if ($fileCount) {
        $icon = 'la-photo-video';
        $buttonText = "<span>View All</span> <span class='badge badge-success badge-pill'>$fileCount</span>";
    }
@endphp

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.js"></script>

<script>
    if (!$('#editable-screenshot-modal').length) {

        let emptyState = `
            <div class="h-100 w-100 d-flex justify-content-center align-items-center flex-column empty-state" style="min-height: 30vh">
                <div class="h1 mb-4">
                    <i class="las la-photo-video"></i>
                </div>
                <div class="h5 text-center">
                    No files yet...
                </div>
            </div>
        `;

        let buttonDefaultState = `<i class="la la-cloud-upload-alt"></i> Upload File`;

        function modalContainerScrollToBottom() {
            let modalBody = $('#editable-screenshot-modal .modal-body')
            let containerHeight = $(modalBody).prop('scrollHeight')
            $(modalBody).scrollTop(containerHeight)
        }

        function restoreFormData() {
            let form = $('#editable-screenshot-modal form')
            $(form).data('mode', 'create')
            $(form).removeData('id')
        }

        function submitForm() {
            let form = $('#editable-screenshot-modal form');
            let datasets = $(form).data()

            let {columnName, entryId, route, screenshots} = datasets;

            const upload_endpoint = 'screenshot/upload';
            const formData = new FormData();

            formData.append("entryId", entryId);

            let fileInput = $(form).find('input#screenshotFileUpload')[0];

            if( fileInput.files.length > 0 ){
                for (let i = 0; i < fileInput.files.length; i++) {
                    formData.append("files[]", fileInput.files[i]);
                }

                const gridContainer = $('#editable-screenshot-modal .screenshot-container');

                axios.post(upload_endpoint, formData)
                .then(response => {
                    if( response.data.status ){

                        Swal.fire({
                            icon: 'success', title: 'File Uploaded', text: 'The selected item has been uploaded successfully', showConfirmButton: false, timer: 3000,
                        }).then(() => {
                            $(form).attr('data-screenshots', response.data.screenshots);
                            datasets.screenshots = response.data.screenshots;
                            let fileIndex = screenshots.length;

                            const newFiles = screenshots ? response.data.screenshots.filter(file => !screenshots.some(screenshot => screenshot.filename === file.filename)) : response.data.screenshots;
                            let newScreenshotItems = "";

                            newFiles.forEach(file => {
                                const filePath = '{{ Storage::disk("records")->secureUrl("insurance-screenshots") }}' + `/${file["filename"]}`;
                                const extension = filePath.split('.').pop();
                                const thumbnail = (extension === 'pdf') ? `<div class="pdf-item"><i class="las la-file-pdf" style="align-self: center;"></i></div>` : `<img src="${filePath}">`;

                                newScreenshotItems += `<div data-id="${fileIndex}" data-index="${fileIndex}" class="modal__file img-box_wrapper screenshot-item">
                                        <span title="edit|delete on click" class="modal__file-delete">
                                            <i class="las la-times"></i>
                                        </span>
                                        <div class="screenshot-content">
                                            <a data-fancybox href="${filePath}" target="_blank" class="btn btn-sm btn-link img-preview">
                                                ${thumbnail}
                                            </a>
                                        </div>
                                    </div>
                                `;

                                fileIndex++;
                            });

                            if (!gridContainer.length) {
                                $('#editable-screenshot-modal .modal__screenshots-container').html(`<div class="screenshot-container">${newScreenshotItems}</div>`);
                            } else {
                                gridContainer.append(newScreenshotItems);
                            }

                            updateFilePreview(entryId, response.data.screenshots);
                        });
                    }
                })
                .catch(error => {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: error.message,
                        timer: 10000,
                        showConfirmButton: true,
                    });
                });

                $("button#uploadBtn").prop("disabled", true);
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'No File Added',
                    text: 'Please select one or more files to upload.',
                    timer: 10000,
                    showConfirmButton: true,
                });
            }

            fileInput.form.reset();
        }

        function deleteFile() {
            let form = $('#editable-screenshot-modal form')
            let datasets = $(form).data()
            let {id, columnName, entryId, route, screenshots} = datasets;

            screenshots = screenshots.filter( (item, index) => index !== id );

            axios({
                method: 'post',
                url: route,
                data: {
                    id: entryId,
                    attribute: columnName,
                    value: JSON.parse(JSON.stringify(screenshots)),
                }
            }).then(() => {
                Swal.fire({
                    icon: 'success', title: 'File Deleted', text: 'The selected item has been deleted successfully', showConfirmButton: false, timer: 3000,
                }).then(() => {
                    $('#editable-screenshot-modal form').data('screenshots', screenshots);
                    $(form).attr('data-screenshots', screenshots);
                    datasets.screenshots = screenshots;

                    $(`.modal__file[data-id*=${id}]`).remove();
                    $('.screenshot-container').children().each(function(index, element) {
                        if (index < screenshots.length) {
                            $(element).attr("data-id", `${index}`);
                            $(element).attr("data-index", `${index}`);
                        }
                    });

                    updateFilePreview(entryId, screenshots);
                    restoreFormData();
                });
            })
        }

        function updateFilePreview( entryId, screenshots ){
            const wrapper = $(`div[data-entry-id='${entryId}']`);

            if (screenshots.length === 0) {
                $('.modal__screenshots-container').html(emptyState);
                wrapper.html(`${createActionBtn('la-cloud-upload-alt', 'Upload File', screenshots, entryId)}`);
            } else {
                $('.empty-state').remove();

                let loop = 0;
                let preview = '';

                for (let i = 0; i < 3; i++) {
                    if( i < screenshots.length ){
                        const filePath = '{{ Storage::disk("records")->secureUrl("insurance-screenshots") }}' + `/${screenshots[i]["filename"]}`;
                        const extension = filePath.split('.').pop();
                        const thumbnail = ( extension === 'pdf' ) ? `<div class="pdf-preview"><i class="las la-file-pdf" style="align-self: center;"></i></div>` : `<img src="${filePath}">`;

                        preview += `<div class="thumbnail">
                                        <a data-fancybox="" href="${filePath}" target="_blank" class="btn btn-sm btn-link img-preview">
                                            ${thumbnail}
                                        </a>
                                    </div>`;
                    } else {
                        preview += `<div class="thumbnail placeholder">
                            <div class="placeholder-preview" data-entry-id="${entryId}">
                                <i class="las la-plus" style="align-self: center;"></i>
                            </div>
                        </div>`;
                    }
                }

                wrapper.html(
                    wrapper.hasClass('screenshot__thumbnail-wrapper') ?
                    `${preview}` :
                    `<div class="screenshot__thumbnail-wrapper">${preview}</div> ${createActionBtn('la-photo-video', 'View All', screenshots, entryId)}`
                );
            }
        }

        function createActionBtn(icon, text, files, entryId) {
            return `<button
                        type="button"
                        class="editable-screenshots-button btn btn-sm btn-primary"
                        data-toggle="modal"
                        data-target="#editable-screenshot-modal"
                        data-screenshots='${JSON.stringify(files)}'
                        data-route="{{ $column['route'] ?? url($crud->getRoute().'/minor-update') }}"
                        data-column-name="{{ $column['name'] }}"
                        data-entry-id='${entryId}'
                    >
                        <i class="la ${icon}"></i>
                        <span>${text}</span>
                        ${files.length > 0 ? `<span class='badge badge-success badge-pill'>${files.length}</span>` : ''}
                    </button>`;
        }

        $('body').prepend(`
            <style>
                .hide-scrollbar::-webkit-scrollbar {
                    display: none;
                }

                .modal__screenshots-container {
                    max-height: 70vh;
                }

                /*.modal__screenshots-container.modal__screenshots-container--hidden-scroll::-webkit-scrollbar {*/
                /*    display: none;*/
                /*}*/

                .modal__file {
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
                .modal__file-delete {
                    float:right;
                    cursor: pointer;
                }

                .screenshot-container {
                    width: 100%;
                    display: flex;
                    flex-wrap: wrap;
                    justify-content: flex-start;
                    gap: 15px;
                }

                .screenshot-item {
                    width: calc(33.33% - 10px);
                    margin-bottom: 10px;
                    border: 1px solid #ccc;
                    box-sizing: border-box;
                    text-align: center;
                    background: #f1f4f8;
                    border-radius: 5px;
                    padding: 5px;
                    height: 50%;
                    min-height: 250px;
                }

                .screenshot-content {
                    width: 100%;
                    display: flex;
                    flex-direction: row;
                    justify-content: center;
                }

                .screenshot-content img {
                    max-width: 100%;
                    max-height: 100%;
                    height: 200px;
                    object-fit: cover;
                }

                .file-input-wrapper {
                    border: 3px dashed #cccccc;
                    padding: 8px;
                    margin: 5px !important;
                }

                input#screenshotFileUpload::file-selector-button {
                    border-radius: 25px;
                    padding: 0 15px;
                    height: 40px;
                    cursor: pointer;
                    background-color: #7c69ef;
                    border: 1px solid #7c69ef;
                    box-shadow: 0px 1px 0px rgba(0, 0, 0, 0.05);
                    margin-right: 10px;
                    transition: background-color 200ms;
                    color: white;
                }

                .thumbnail img {
                    height: 100px;
                    width: 100px;
                    object-fit: cover;
                    border-radius: 10px;
                }

                .screenshot__thumbnail-wrapper {
                    display: inline-flex;
                    justify-content: center;
                }

                .screenshot__wrapper {
                    display: grid;
                }

                .editable-screenshots-button {
                    width: fit-content;
                    justify-self: center;
                }

                .pdf-item {
                    font-size: 100px;
                }

                .pdf-preview {
                    font-size: 70px !important;
                }

                .placeholder-preview, .pdf-preview  {
                    height: 100px;
                    width: 100px;
                    background: #7c69ef0f;
                    padding: 0.25rem 0.5rem;
                    border-radius: 10px;
                    display: flex;
                    justify-content: center;
                    font-size: 15px;
                    color: #7c69ef;
                    cursor: pointer;
                }

                .placeholder-preview:hover {
                    background: #7c69ef4f;
                }

                .thumbnail.placeholder{
                    padding: 0.25rem 0.5rem;
                }
            </style>
        `).append(`
            <div id="editable-screenshot-modal" class="modal fade" tabindex="-1">
                <div class="modal-dialog modal-dialog-centered modal-lg">
                    <div class="modal-content">
                        <div class="modal-header border-0">
                            <h5 class="modal-title">Screenshots</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        </div>
                        <div class="modal-body modal__screenshots-container d-flex" style="overflow-y: auto; height: calc(70vh - 70px)">
                            <div class="screenshot-container"></div>
                        </div>
                        <div class="modal-footer flex-column border-0 p-0">
                            <form method="post" class="m-0 modal__input-container d-flex w-100 p-2 bg-light align-items-center justify-content-between" style="gap: 0.5rem; border-bottom-right-radius: 0.3rem; border-bottom-left-radius: 0.3rem;">
                                <div class="m-0 w-100 file-input-wrapper">
                                    <input type='file' accept='image/*' id='screenshotFileUpload' name='fileUpload[]' class='file-input w-100' multiple>
                                </div>
                                <button type="submit" id="uploadBtn" class="btn btn-primary" disabled><i class="las la-save"></i></button>
                            </form>
                        </div>
                    </div>
                </div>s
            </div>
            <div id="delete-file-confirmation-modal" class="modal">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-body d-flex flex-column justify-content-center align-items-center">
                            <div class="d-flex flex-column">
                                <span class="font-weight-bold">Are you sure you want to delete this file?</span>
                                <p>This action is permanent and cannot be undone.</p>
                            </div>

                            <div class="d-flex justify-content-center align-items-center" style="gap: 1rem">
                                <button class="btn btn-danger modal__delete-files-confirmation-delete">Yes, delete</button>
                                <button class="btn modal__delete-files-cancel-delete">No, cancel</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        `)

        $(document).on('click', '.modal__delete-files-cancel-delete', function() {
            $('#delete-file-confirmation-modal').modal('hide')
            restoreFormData()
        })

        $(document).on('click', '.modal__delete-files-confirmation-delete', function() {
            deleteFile();
            $('#delete-file-confirmation-modal').modal('hide');
        })

        $(document).on('click', '.placeholder-preview', function (e) {
            const entryId = $(this).data('entryId');
            const viewButton = $(`.editable-screenshots-button[data-entry-id="${entryId}"]`);
            $(viewButton).click();
        });

        $(document).on('click', '.editable-screenshots-button', function (e) {
            const gridContainer = $('#editable-screenshot-modal .screenshot-container');

            const datasets = $(this).data();
            const unusableDatasets = ['toggle', 'target'];

            for (let dataset in datasets) {
                if (!unusableDatasets.includes(dataset)) {
                    let dashedDataset = dataset.replace(/([a-z])([A-Z])/g, '$1-$2').toLowerCase();
                    $('#editable-screenshot-modal form').data(dashedDataset, datasets[dataset])
                }
            }

            $(gridContainer).html('');

            if (datasets['screenshots'].length > 0) {
                var screenshotContainer = "";

                datasets['screenshots'].map( (screenshot, index) => {
                    const filePath = '{{ Storage::disk("records")->secureUrl("insurance-screenshots") }}' + `/${screenshot["filename"]}`;
                    const extension = filePath.split('.').pop();
                    const thumbnail = ( extension === 'pdf' ) ? `<div class="pdf-item"><i class="las la-file-pdf" style="align-self: center;"></i></div>` : `<img src="${filePath}">`;

                    let imageBox = `
                        <div data-id="${index}" data-index="${index}" class="modal__file img-box_wrapper screenshot-item">
                            <span title="edit|delete on click" class="modal__file-delete">
                                <i class="las la-times"></i>
                            </span>
                            <div class="screenshot-content">
                                <a data-fancybox href="${filePath}" target="_blank" class="btn btn-sm btn-link img-preview">
                                    ${thumbnail}
                                </a>
                            </div>
                        </div>
                    `;

                    screenshotContainer += imageBox;
                });

                $('.modal__screenshots-container').html(`<div class='screenshot-container'>${screenshotContainer}</div>`);
            } else {
                $(gridContainer).html(emptyState);
            }

            modalContainerScrollToBottom();
        })

        $('#editable-screenshot-modal').on('shown.bs.modal', function () {
            modalContainerScrollToBottom();
        })

        $(document).on('click', '.modal__file-delete', function (e) {
            let modalFileItemData = $(e.target).closest('.modal__file').data()
            let form = $('#editable-screenshot-modal form')

            $(form).data('id', modalFileItemData['id']).data('mode', 'delete').data('entry-id', modalFileItemData['entryId'])

            $('#delete-file-confirmation-modal').modal('show')
            $('#modal__file-context-menu').remove()
        })

        $('#delete-file-confirmation-modal').on('shown.bs.modal', function () {
            let zIndex = 1040 + ($('.modal-files-backdrop').length * 10)

            $('#delete-file-confirmation-modal').css({
                'z-index': zIndex + 10
            })
            $('.modal-files-backdrop.show.fade').css({
                'z-index': zIndex
            })
        }).on('hidden.bs.modal', function () {
            let zIndex = 1030 + ($('.modal-files-backdrop').length + 10)

            $('.modal-files-backdrop.show.fade').css({
                'z-index': zIndex
            })
        })

        $(document).on('submit', '#editable-screenshot-modal form', function (event) {
            event.preventDefault();
            submitForm();
        })

        $(document).ready(function () {
            setUploadButtonState();
        });

        function setUploadButtonState(){
            const fileInput = $("input#screenshotFileUpload");
            const submitButton = $("button#uploadBtn");

            fileInput.change(function () {
                if (fileInput.get(0).files.length > 0) {
                    submitButton.prop("disabled", false);
                } else {
                    submitButton.prop("disabled", true);
                }
            });
        }

    }
</script>

<div class='screenshot__wrapper' data-entry-id="{{ $entry->getKey() }}">
    @php
        $screenshots = json_decode($entry[$column['name']]);
    @endphp

    @if (!empty($screenshots))
        <div class='screenshot__thumbnail-wrapper'>
            @for ($i = 0; $i < 3; $i++)
                @if ($i < count($screenshots))
                    @php
                        $filePath = Storage::disk('records')->secureUrl('insurance-screenshots/' . $screenshots[$i]->filename);
                        $extension = pathinfo($filePath, PATHINFO_EXTENSION);
                    @endphp

                    <div class="thumbnail">
                        <a data-fancybox="" href="{{ $filePath }}" target="_blank" class="btn btn-sm btn-link img-preview">
                            @if( $extension === 'pdf' )
                                <div class="pdf-preview">
                                    <i class="las la-file-pdf" style="align-self: center;"></i>
                                </div>
                            @else
                                <img src="{{ $filePath }}">
                            @endif

                        </a>
                    </div>
                @else
                    <div class="thumbnail placeholder">
                        <div class="placeholder-preview" data-entry-id="{{ $entry->getKey() }}">
                            <i class="las la-plus" style="align-self: center;"></i>
                        </div>
                    </div>
                @endif
            @endfor
        </div>
    @endif

    <button
        type="button"
        class="editable-screenshots-button btn btn-sm btn-primary"
        data-toggle="modal"
        data-target="#editable-screenshot-modal"
        data-screenshots="{{ $entry[$column['name']] }}"
        data-route="{{ $column['route'] ?? url($crud->getRoute().'/minor-update') }}"
        data-column-name="{{ $column['name'] }}"
        data-entry-id="{{ $entry->getKey() }}"
    >
        <i class="la {{$icon}}"></i> {!! $buttonText !!}
    </button>
</div>


