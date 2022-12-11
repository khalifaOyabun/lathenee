"use strict";

// Class definition
var KTUppy = function () {
    // console.clear();
    // console.log(Uppy);

    const Tus = Uppy.Tus;
    const XHRUpload = Uppy.XHRUpload;
    const ProgressBar = Uppy.ProgressBar;
    const StatusBar = Uppy.StatusBar;
    const FileInput = Uppy.FileInput;
    const Informer = Uppy.Informer;

    // to get uppy companions working, please refer to the official documentation here: https://uppy.io/docs/companion/
    const Dashboard = Uppy.Dashboard;
    const Dropbox = Uppy.Dropbox;
    const GoogleDrive = Uppy.GoogleDrive;
    const Instagram = Uppy.Instagram;
    const Facebook = Uppy.Facebook;
    const ScreenCapture = Uppy.ScreenCapture;
    const Webcam = Uppy.Webcam;
    const French = Uppy.locales.de_DE;

    var initUppy = function () {
        var options = {
            debug: true,
            autoProceed: false,
            proudlyDisplayPoweredByUppy: false,
            target: '.kt-uppy__dashboard',
            allowMultipleUploads: true,
            locale: French,
            inline: false,
            replaceTargetContent: true,
            showProgressDetails: true,
            height: 470,
            metaFields: [
                {id: 'name', name: 'Name', placeholder: 'file name'},
                {id: 'caption', name: 'Caption', placeholder: 'describe what the image is about'}
            ],
            restrictions: {
                maxFileSize: 1000000,
                maxNumberOfFiles: 3,
                minNumberOfFiles: 2,
                allowedFileTypes: ['image/*', 'video/*', 'application/*']
            },
            showRemoveButtonAfterComplete: true,
            closeModalOnClickOutside: false,
            disablePageScrollWhenModalOpen: true,
            browserBackButtonClose: true,
            trigger: '.kt-uppy__btn',
            plugins: ['Webcam'],
            theme: 'auto'
        };

        var uppyDashboard = Uppy.Core({
            autoProceed: true,
            restrictions: {
                maxFileSize: 1000000, // 1mb
                maxNumberOfFiles: 5,
                minNumberOfFiles: 1
            }
        });

        uppyDashboard.use(Dashboard, options);
        uppyDashboard.use(GoogleDrive, {target: Dashboard, companionUrl: 'https://companion.uppy.io'});
        uppyDashboard.use(Dropbox, {target: Dashboard, companionUrl: 'https://companion.uppy.io'});
        uppyDashboard.use(Facebook, {target: Dashboard, companionUrl: 'https://companion.uppy.io'});
        uppyDashboard.use(Instagram, {target: Dashboard, companionUrl: 'https://companion.uppy.io'});
        uppyDashboard.use(Webcam, {target: Dashboard});
        uppyDashboard.use(ScreenCapture, {target: Dashboard});
        uppyDashboard.use(XHRUpload, {
            endpoint: '/backend/_ajax.php?app=backend&action=uploadFileUppy&from=' + $('[data-part-id-node]').data('folder-name') + '&id=' + $('[data-part-id-node]').data('part-id'),
            method: 'post',
            getResponseData(responseText, response) {
                let parsedResponse = secureparse(responseText);
                if (parsedResponse === false) {
                    return responseText;
                }
                return parsedResponse;
            }
        });

        uppyDashboard.run();

        uppyDashboard.on('upload-success', (file, response) => {
            if (response.status === 200) {
                $('[data-no-file-found-message]').hide('clip');
                $(document).find('[data-toggle-checkbox-doc-list]').hide("slide");
                $(document).find('[data-accroche-aside-actions]').hide("blind");
                $(document).find('[data-dropdown-menu-manage]').show("drop");
                $(document).find('[data-doc-list]').append(response.body);
                notify3("Chargement réussi !", 'success');
            } else {
                notify3("Échec lors du chargement!", 'error');
            }
        });

        uppyDashboard.on('file-removed', (file, reason) => {
            if (reason === 'removed-by-user') {
                $.get("/backend/_ajax.php?app=backend&module=" + $('[data-part-id-node]').data('folder-name') + "&action=removeUploadedFileUppy&from=" + $('[data-part-id-node]').data('folder-name') + "&filename=" + file.name + "&id=" + $('[data-part-id-node]').data('part-id')).done(function (returned) {
                    let back = secureparse(returned);
                    if (back[0] === 'success') {
                        $(document).find('[data-filename="' + back[2] + '"]').remove();
                        if ($(document).find('[data-filename]').length === 0) {
                            $('[data-no-file-found-message]').show('clip');
                        }
                    }
                    notify3(back[1], back[0]);
                });
            }
        });

        $(document).on('click', '.uppy-Dashboard-close', function () {
            uppyDashboard.reset();
        });
    };

    return {
        // public functions
        init: function () {
            initUppy();
//            swal.fire({
//                "title": "Notice",
//                "html": "Uppy demos uses <b>https://master.tus.io/files/</b> URL for resumable upload examples and your uploaded files will be temporarely stored in <b>tus.io</b> servers.",
//                "type": "info",
//                "buttonsStyling": false,
//                "confirmButtonClass": "btn btn-brand kt-btn kt-btn--wide",
//                "confirmButtonText": "Ok, I understand",
//                "onClose": function (e) {
//                    console.log('on close event fired!');
//                }
//            });
        }
    };
}();

KTUtil.ready(function () {
    KTUppy.init();
});