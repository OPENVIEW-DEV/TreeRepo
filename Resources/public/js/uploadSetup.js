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
    var fileList='';

    // Initialize the jQuery File Upload widget:
    $('#fileupload').fileupload({
        // Uncomment the following to send cross-domain cookies:
        //xhrFields: {withCredentials: true},
        url: '',
        maxChunkSize: 131072    // 128Kb
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
     * Vedi: https://github.com/blueimp/jQuery-File-Upload/issues/2200
     */
    $('#fileupload').bind('fileuploadadded', function(e, data) {
        // avvia direttamente upload
        if (fileList == '') {
            fileList = data;
        } else {
            fileList.files.push(data.files[0]);
            fileList.originalFiles.push(data.originalFiles[0]);
        }
        $('#fileupload').fileupload('send', {'files': fileList.files})
            .success(function (result, textStatus, jqXHR) {
                console.log('Upload completato con successo, aggiorno elenco file');
                setTimeout(function(){aggiornaElencoFile()}, 1000);
            });
    });
    
    
    
    /* 
     * Intercetto evento upload completato (di un file o nessuno, all'avvio)
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
                //console.log('upload fallito: ' + fileName);
            });
        }
    });

});