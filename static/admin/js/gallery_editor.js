/**
 * Created by Francesco on 09/01/16.
 */
function CustomRoxyFileBrowser(field_name) {
    var roxyFileman = '/admin/filemanager/';
    if (roxyFileman.indexOf("?") < 0) {
        roxyFileman += "?input=" + field_name;
    }
    else {
        roxyFileman += "&input=" + field_name;
    }
    roxyFileman += '&integration=custom&dialogid=roxyCustomPanel';

    $('#roxyCustomPanel').children().first().attr("src",roxyFileman);
    $('#roxyCustomPanel').dialog({
        modal:true,
        width:875,
        height:600,
        title: 'File Manager'
    });

    return false;
}

var up_tr_action = function (event) {
    if ($(event.target).parents('tr').is(':first-child')) return false;
    var row = $(event.target).parents('tr');
    var prevRow = row.prev();
    var rowOrder = parseInt(row.find('.order-cell').text())-1;
    var prevRowOrder = parseInt(prevRow.find('.order-cell').text())+1;
    var rowUrl = row.find('input[name="photos[imageUrl][]"]');
    var prevRowUrl = prevRow.find('input[name="photos[imageUrl][]"]');
    var temp = rowUrl.prop('id');
    rowUrl.prop('id', prevRowUrl.attr('id'));
    prevRowUrl.prop('id', temp);
    row.find('.order-cell').text(rowOrder);
    prevRow.find('.order-cell').text(prevRowOrder);
    row.find('[name="photos[order][]"]').val(rowOrder);
    prevRow.find('[name="photos[order][]"]').val(prevRowOrder);
    prevRow.before(row);
};

var down_tr_action = function (event) {
    if ($(event.target).parents('tr').is(':last-child')) return false;
    var row = $(event.target).parents('tr');
    var nextRow = row.next();
    var rowOrder = parseInt(row.find('.order-cell').text())+1;
    var nextRowOrder = parseInt(nextRow.find('.order-cell').text())-1;
    var rowUrl = row.find('input[name="photos[imageUrl][]"]');
    var nextRowUrl = nextRow.find('input[name="photos[imageUrl][]"]');
    var temp = rowUrl.prop('id');
    rowUrl.prop('id', nextRowUrl.attr('id'));
    nextRowUrl.prop('id', temp);
    row.find('.order-cell').text(rowOrder);
    nextRow.find('.order-cell').text(nextRowOrder);
    row.find('[name="photos[order][]"]').val(rowOrder);
    nextRow.find('[name="photos[order][]"]').val(nextRowOrder);
    nextRow.after(row);
};

var openfileman_handler = function (event) {
    CustomRoxyFileBrowser($(event.target).prev().attr('id'));
};

var removetr_handler = function (event) {
    $(event.target).parents('tr').nextAll().each(function (idx, item) {
        var orderInput = $(item).find('input[name="photos[order][]"]');
        var newOrder = parseInt(orderInput.val())-1;
        orderInput.val(newOrder);
        $(item).find('.order-cell').text(newOrder);
        $(item).find('input[name="photos[imageUrl][]"]').prop('id', "photo_"+newOrder);
    });
    $(event.target).parents('tr').remove();
    NEXT_PHOTO_ID--;
};

var photoUrlChange = function (event) {
    $(event.target).parents('td').prev().children('img').prop('src', $(event.target).val());
};

$('.up-tr').click(up_tr_action);

$('.down-tr').click(down_tr_action);

//Azione pulsante apertura file manager sul server
$('.openfileman').click(openfileman_handler);

function checkDataGalleryUnique(id, target, callback) {
    target.val(target.val().trim());
    var txtvalue = target.val();
    if (txtvalue == "") {
        $(target).css("border", "solid 2px rgb(202, 60, 60)");
        return false;
    }
    $.getJSON("/admin/galleries/checkDataGallery?datagallery="+txtvalue+"&id="+id, function(data) {
        if (data.status) {
            if (data.data.unique) {
                $(target).css("border", "solid 2px rgb(28, 184, 65)");
                callback(true);
            } else {
                $(target).css("border", "solid 2px rgb(202, 60, 60)");
                displayErrorDialog("Errore", "Impossibile usare lo stesso valore data-gallery per pi&ugrave; gallerie. Usarne un altro.");
                callback(false);
            }
        } else {
            //Show error dialog
            $("#erd-errdata").val(data.exception);
            $("#error-dialog").dialog("open");
            return false;
        }
    });
};

$('#save-all').click(function () {
    checkDataGalleryUnique($('#id').val(), $('input[name="dataGallery"]'), function (response) {
        if (response) {
            $('#gallery-editor-form').submit();
        } else {
            $('input[name="dataGallery"]')[0].focus();
        }
    });
});

$('#new-photo').click(function (event) {
    var template = $(PHOTO_ROW_TEMPLATE);
    var progressive = NEXT_PHOTO_ID++;
    template.find('input[name="photos[order][]"]').val(progressive);
    template.find('.order-cell').text(progressive);
    template.find('#photo').attr('id', 'photo_'+progressive);
    template.find(".up-tr").click(up_tr_action);
    template.find(".down-tr").click(down_tr_action);
    template.find(".openfileman").click(openfileman_handler);
    template.find(".remove-tr").click(removetr_handler);
    template.find('input[name="photos[imageUrl][]"]').change(photoUrlChange);
    $('#photo-table tbody').first().append(template);
});

$('.remove-tr').click(removetr_handler);

$('#thumb-image-txt').change(function (event) {
    $('#thumb-image-img').prop('src', $(event.target).val());
});

$('input[name="photos[imageUrl][]"]').change(photoUrlChange);