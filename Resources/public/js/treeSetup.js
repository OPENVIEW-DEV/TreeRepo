/*
    Aggiorna l'elenco dei file e l'albero delle cartelle dopo il clic su una cartella
*/
function updateActiveDir(data) {
    // se vedo la root folder, non permetto di caricare file
    if (data.parentid === null) {
        $('#btn-addfiles').removeClass('btn-success')
                .addClass('btn-disabled')
                .attr('disabled', 'disabled');

    } else {
        $('#btn-addfiles').removeClass('btn-disabled')
                .addClass('btn-success')
                .attr('disabled', false);
    }
    // cancello tutti i file selezionati per l'upload
    $('tr.template-upload').empty();
    // imposto i dati della directory nell'elenco file
    $('#tree-files').data('parentid', data.id);         // aggiorna parent-id in elenco file nella cartella
    $('#btn-newfolder').data('parentid', data.id);      // aggiorna parent id in pulsante nuova cartella
    $('#foldername').text(data.name);                   // aggiorna nome cartella in widget elenco file
    // avvia aggiornamento elenco file
    aggiornaElencoFile();
}



/* 
    mostra l'elenco dei file basandosi sull'elenco json indicato 
*/
function mostraElencoFile(elencoJson) {
    $('#tree-files').empty();
    // se ho elemento da mostrare
    if (elencoJson.data.length > 0) {
        for (var i=0; i<elencoJson.data.length; i++) {
            var s = '<tr>' +
                '<td><i class="icon icon-file-alt"></i> ' +
                elencoJson.data[i].name + '</td>' +
                '<td></td>' +
                '<td class="center"><i class="icon icon-zoom-in bigger-120 blue"></i> ' +
                    '<i class="icon icon-edit bigger-120 blue"></i> ' +
                    '<i class="icon icon-trash bigger-120 red"></i></td>' +
                '</tr>';
            $('#tree-files').append(s);
        }
    } else {
        var s = '<div class="alert alert-info">Cartella vuota</div>';
        $('#tree-files').append(s);
    }
    //$('#tree-files').append('<div><pre>' + JSON.stringify(elencoJson) + '</pre></div>');
}



/* 
    aggiorno elenco file 
*/
function aggiornaElencoFile() {
    var parentid = $('#tree-files').data('parentid');
    var url = Routing.generate('openview_treerepo_node_rpclistfile', {'nodeid': parentid});
    $('#tree-files').empty();
    $.ajax({
        url: url,
        type: 'POST',
        dataType: 'json',
        success: function (response) {
            if (response.status == "OK") {
                // mostra elenco file
                //console.log('Elenco ricevuto: ' + JSON.stringify(response));
                mostraElencoFile(response);
            } else {
                // mostra messaggio errore dati
                //console.log('Ricevuto elenco con errore: ' + JSON.stringify(response));
                $('#tree-files').append('<div class="alert alert-warning">Errori nella generazione dell\'elenco degli elementi.</div>')
            }
        },
        error: function (response, textStatus, errorThrown) {
            // mostra messaggio di errore chiamata
            //console.log('Errore di comunicazione: ' + JSON.stringify(response));
            $('#tree-files').append('<div class="alert alert-warning">Errore di comunicazione.</div>')
        }
    });
}

jQuery(function($){
    // applica datatable
    $('#filestable').dataTable({
        "bFilter": false,
        "bInfo": false,
        "bPaginate": false,
        "bSort": false
    });
    // applica treeview
    $('#tree-folders').ace_tree({
        dataSource: new DataSourceTree({ url: Routing.generate('openview_treerepo_node_rpclistdir', null) }),
        multiSelect: false,
        cacheItems: true,
        loadingHTML: '<div class="tree-loading"><i class="icon-refresh icon-spin blue"></i></div>',
        'open-icon' : 'icon-folder-open',
        'close-icon' : 'icon-folder-close',
        'selectable' : true,
        'selected-icon' : 'icon-ok',
        'unselected-icon' : 'icon-remove'
    });
    updateActiveDir({'parentid':null, 'name':'/'});         // aggiorna elenco file in directory iniziale

    /* 
     * quando apro o chiudo una cartella 
     * 
     * */
    $('#tree-folders').on('opened', function (evt, data) {
        updateActiveDir(data);
    });
    $('#tree-folders').on('closed', function (evt, data) {
        updateActiveDir(data);
    });

    /* se clicco il btn per creare una cartella */
    $('#btn-newfolder').click(function(e) {
        // legge parent id
        var parentid = $(this).data('parentid');
        if (parentid == 'undefined') {
            parentid = null;
        }
        // costruisce URL dove andare
        var url = Routing.generate('openview_treerepo_node_newfolder', {'nodeid': parentid });
        //console.log('url: ' + url);
        // manda a quell'URL
        window.location = url;
    });
});
