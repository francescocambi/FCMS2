//Setting up newUrlDialog
var newUrlDialog = $('#newUrlDialog').dialog({
    title: 'Nuovo Url',
    autoOpen: false,
    width: 300,
    height: 170,
    modal: true,
    buttons: {
        "Aggiungi": insertUrl,
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
    insertUrl();
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

function insertUrl() {
    var newUrl = nud_urlField.val();

    //Check unique
    checkUniqueUrl(newUrl, function (unique) {
        if (!unique) {
            alert("L'url inserito è già in uso. Inserirne un altro.");
        } else {
            var row = $("#urlrowtpl").clone(true)
                .attr("id", "")
                .addClass("urlRow")
                .show()
                .insertBefore($('#newurlrow'));
            row.find('td:first').text(newUrl);
            row.find('input[type="hidden"]').val(newUrl);
            newUrlDialog.dialog('close');
        }
    });
}

function updateUrl() {

    var updatingRow = $('#updatingRow');
    oldUrl = updatingRow.children('td:first').text();
    newUrl = uud_urlField.val();
    var refactor = uud_refactorCheck.prop('checked');

    //Check unique
    checkUniqueUrl(newUrl, function (unique) {
        //New url isn't unique
        if (!unique) {
            alert("L'url inserito è già in uso. Inserirne un altro.");
        } else {
            if (refactor) {
                //Get links list from server
                $.getJSON('/admin/pages/linkRefactoringPreview', {oldUrl: oldUrl}, function (data) {
                    if (data.status) {
                        if (data.data.length == 0) {
                            updateUrlRow(updatingRow, newUrl);
                            updateUrlDialog.dialog('close');
                        }
                        else
                            showLinkRefactoringDialog(data.data);
                    }
                })
            } else {
                //Update url in table
                updateUrlRow(updatingRow, newUrl);
                updateUrlDialog.dialog('close');
            }
        }
    });
}

function checkUniqueUrl(url, callback) {
    //Checks that url isn't in url table
    var uniquecheck = true;
    $('.urlRow').children('input[type="hidden"]').each(function(index, element) {
        if (url == element.value) {
            uniquecheck = false;
        }
    });

    if (!uniquecheck) {
        callback(false);
        return;
    }

    $.getJSON('/admin/pages/checkUrlUnique', {
        url: url
    }, function (data) {
        if (data.status) {
            callback(data.data.unique);
        }
    });
}

function updateUrlRow(updatingRow, newUrl) {
    //Update url in table
    updatingRow.attr('id', '').children('td:first').text(newUrl);
    updatingRow.find('input[type="hidden"]').val(newUrl);
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
    data += '&oldUrl=' + oldUrl + '&newUrl=' + newUrl;
    //console.log(data);
    $.getJSON('/admin/pages/linkRefactoring', data, function (data) {
        if (data.status) {
            //alert('Operazione completata!');
            updateUrlRow(updatingRow, newUrl);
        }
    });
    urlRefactoringPreviewDialog.dialog('close');
}

//Azione pulsante elimina url
$(".delurl").click(function (event) {
    $(event.target).parent().parent().remove();
});