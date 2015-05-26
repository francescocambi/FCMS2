//Setting up newUrlDialog
var newUrlDialog = $('#newUrlDialog').dialog({
    title: 'Nuovo Url',
    autoOpen: false,
    width: 300,
    height: 170,
    modal: true,
    buttons: {
        "Aggiungi": newUrl,
        Cancel: function () {
            $(this).dialog('close');
        }
    },
    close: function () {
        $(this).find("form")[0].reset();
    }
});

var nud_urlField = newUrlDialog.find('#nud-url');

newUrlDialog.find('form').on("submit", function (event) {
    event.preventDefault();
    newUrl();
});

//Setting up updateUrlDialog
var updateUrlDialog = $('#updateUrlDialog').dialog({
    title: 'Modifica Url',
    autoOpen: false,
    width: 330,
    height: 220,
    modal: true,
    buttons: {
        "Modifica": updateUrl,
        Cancel: function () {
            $(this).dialog('close');
        }
    },
    close: function () {
        $(this).find("form")[0].reset();
    }
});

var uud_urlField = updateUrlDialog.find('#uud-url');
var uud_refactorCheck = updateUrlDialog.find('#uud-refactor');

updateUrlDialog.find('form').on("submit", function (event) {
    event.preventDefault();
    updateUrl();
});

var urlRefactoringPreviewDialog = $('#urlRefactoringPreviewDialog').dialog({
    title: 'Proposta di modifica',
    autoOpen: false,
    width: 900,
    height: 600,
    modal: true,
    buttons: {
        "Conferma": refactorLinks,
        Cancel: function () {
            $(this).dialog('close');
        }
    },
    close: function () {
        $(this).find('table:first tbody').children().remove();
        $('#updatingRow').attr('id','');
    }
});


var newUrl = "";
var oldUrl = "";

$('#newurl').click(function (event) {
    newUrlDialog.dialog('open');
});

$('.modurl').click(function (event) {
    $(event.target).parents('tr:first').attr('id', 'updatingRow');
    var urlValue = $(event.target).parents('.urlRow')
        .children(':first')
        .text();
    uud_urlField.val(urlValue);
    updateUrlDialog.dialog('open');
});

function newUrl() {
    var row = $("#urlrowtpl").clone(true)
        .attr("id", "")
        .addClass("urlRow")
        .show()
        .insertBefore($('#newurlrow'));
    row.find('td:first').text(nud_urlField.val());
    newUrlDialog.dialog('close');
}

function updateUrl() {
    var updatingRow = $('#updatingRow');
    oldUrl = updatingRow.children('td:first').text();
    newUrl = uud_urlField.val();
    var refactor = uud_refactorCheck.prop('checked');

    if (refactor) {
        //Get links list from server
        $.getJSON('/admin/pages/linkRefactoringPreview', {oldUrl: oldUrl}, function (data) {
            if (data.status) {
                showLinkRefactoringDialog(data.data);
            }
        })
    } else {
        //Update url in table
        updatingRow.attr('id', '').children('td:first').text(newUrl);
        updateUrlDialog.dialog('close');
    }
}

function showLinkRefactoringDialog(links) {
    var table = urlRefactoringPreviewDialog.find('table:first tbody');
    jQuery.each(links, function (index, link) {
        var html = '<tr>' +
            '<td><input type="checkbox" checked name="refactoringBlocks['+link.blockId+']"></td>' +
            '<td>'+link.blockName+'</td>' +
            '<td>'+link.hrefAttribute+'</td>' +
            '<td><button type="button" class="pure-button show-link-content">Dettagli</button></td>' +
            '</tr><tr style="display: none;">' +
            '<td colspan="4">'+link.linkBody+'</td>' +
            '</tr>';
        table.append($(html));
    });
    $('.show-link-content').click(function (event) {
        var nextRow = $(event.target).parents('tr:first').next();
        if (nextRow.find(':visible').size() > 0)
            nextRow.hide();
        else
            nextRow.show();
    });
    //$('#updatingRow').attr('id', '');
    updateUrlDialog.dialog('close');
    urlRefactoringPreviewDialog.dialog('open');
}

function refactorLinks() {
    var updatingRow = $('#updatingRow');
    var data = urlRefactoringPreviewDialog.find('form').serialize();
    data += '&oldUrl='+oldUrl+'&newUrl='+newUrl;
    //console.log(data);
    $.getJSON('/admin/pages/linkRefactoring', data, function (data) {
        if (data.status) {
            alert('Operazione completata!');
            updatingRow.children('td:first').text(newUrl);
        }
    });
    urlRefactoringPreviewDialog.dialog('close');
}

//Azione pulsante Modifica Url
$(".modifica_old").click(function (event) {
    var datafield = $(event.target).parent().prev();
    var datainput = $(event.target).parent().next();
    if (datafield.attr("mode") == 1) {
        //In corso di modifica
        datafield.attr("mode", 0);
        var url = datafield.children().get(0).value;
        datafield.children().remove();
        datafield.text(url);
        datainput.val(url);
        $(event.target).parent().children().css('color', 'black');

    } else {
        //Vuole avviare modifica
        datafield.attr("mode", 1);
        var url = datafield.text();
        datafield.text(""); //toglie l'url
        $("<input type=\"text\" value=\""+url+"\">").appendTo(datafield); //mette casella testo con url
        $(event.target).parent().children().css('color', '#0078e7'); //button diventa primary
    }

});

//Azione pulsante elimina url
$(".delurl").click(function (event) {
    $(event.target).parent().parent().remove();
});

function checkUrlUnique(url, pageid, callback) {
    var params = {
        url: url,
        pageid: pageid
    };
    $.getJSON('/admin/pages/checkUrlUnique', params, function (data) {
        if (data.status) {
            if (data.data.unique) {
                callback(true);
            } else {
                displayErrorDialog("Errore", "Impossibile usare lo stesso nome per pi&ugrave; pagine. Usarne un altro.");
                callback(false);
            }
        }
    });

}