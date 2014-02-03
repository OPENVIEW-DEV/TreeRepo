/*
 * Sets up the uploader
 */


/*
 * jQuery File Upload Plugin JS Example 8.9.1
 * https://github.com/blueimp/jQuery-File-Upload
 *
 * Copyright 2010, Sebastian Tschan
 * https://blueimp.net
 *
 * Licensed under the MIT license:
 * http://www.opensource.org/licenses/MIT
 */

/* global $, window */

$(function () {
    'use strict';

    // Initialize the jQuery File Upload widget:
    $('#fileupload').fileupload({
        // Uncomment the following to send cross-domain cookies:
        //xhrFields: {withCredentials: true},
        url: '',
        maxChunkSize: 262144    // 256Kb
    });

    // Enable iframe cross-domain access via redirect option:
    $('#fileupload').fileupload(
        'option',
        'redirect',
        window.location.href.replace(
            /\/[^\/]*$/,
            '/cors/result.html?%s'
        )
    );

    if (window.location.hostname === 'blueimp.github.io') {
        // Demo settings:
        $('#fileupload').fileupload('option', {
            url: '//jquery-file-upload.appspot.com/',
            // Enable image resizing, except for Android and Opera,
            // which actually support image resizing, but fail to
            // send Blob objects via XHR requests:
            disableImageResize: /Android(?!.*Chrome)|Opera/
                .test(window.navigator.userAgent),
            maxFileSize: 5000000,
            acceptFileTypes: /(\.|\/)(gif|jpe?g|png)$/i
        });
        // Upload server status check for browsers with CORS support:
        if ($.support.cors) {
            $.ajax({
                url: '//jquery-file-upload.appspot.com/',
                type: 'HEAD'
            }).fail(function () {
                $('<div class="alert alert-danger"/>')
                    .text('Upload server currently unavailable - ' +
                            new Date())
                    .appendTo('#fileupload');
            });
        }
    } else {
        // Load existing files:
        $('#fileupload').addClass('fileupload-processing');
        $.ajax({
            // Uncomment the following to send cross-domain cookies:
            //xhrFields: {withCredentials: true},
            url: $('#fileupload').fileupload('option', 'url'),
            dataType: 'json',
            context: $('#fileupload')[0]
        }).always(function () {
            $(this).removeClass('fileupload-processing');
        }).done(function (result) {
            $(this).fileupload('option', 'done')
                .call(this, $.Event('done'), {result: result});
        });
        /*
         * Intercetto evento di aggiunta file 
         */
        $('#fileupload').bind('fileuploadadded', function(e, data) {
            console.log('File aggiunto alla lista dei file da uploadare');
        });
        /* 
         * Intercetto evento upload (di un file o nessuno, all'avvio) completato        
         * - leggo info sul file: filename, parentid
         * - creo il nodo via ajax
         * - cancello la riga dall'elenco dei file uploadati
         */
        $('#fileupload').bind('fileuploadcompleted', function(e, data) {
            // in data['result']['files'] trovo l'array dei file uploadati
            var filesList = data['result']['files'];
            for (var i=0; i<filesList.length; i++) {
                var file = filesList[i];
                var fileName = file['name'];
                var parentId = $('#btn-newfolder').data('parentid');
                console.log('Elaboro file: ' + fileName + ' con parent: ' + parentId);
                var url = Routing.generate('openview_treerepo_node_rpcaddnode', {
                    'parentid': parentId,
                    'filename': fileName
                });
                $.ajax({'url': url})
                .success(function() {
                    // elimina la riga del file dall'elenco
                    $('tr[data-filename="' + fileName + '"]').remove();
                })
                .error(function() {
                    alert('upload fallito');
                });
            }
        });
    }

});