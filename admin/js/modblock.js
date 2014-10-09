/**
 * Created by francesco on 10/9/14.
 */

tinymce.init({
    theme: "modern",
    image_advtab: true,
    image_class_list: [
        {title: "Null", value: ""},
        {title: "Visualizzatore Img", value: "ingrandimento"}
    ],
    plugins: [
        "advlist autolink link image lists charmap print preview hr anchor pagebreak spellchecker",
        "searchreplace wordcount visualblocks visualchars code fullscreen insertdatetime media nonbreaking",
        "save table contextmenu directionality emoticons template paste textcolor"
    ],
    toolbar: "insertfile undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image | print preview media fullpage | forecolor backcolor emoticons",
    relative_urls: false,
    file_browser_callback: RoxyFileBrowser
});

function RoxyFileBrowser(field_name, url, type, win) {
    var roxyFileman = 'fileman/index.html';
    if (roxyFileman.indexOf("?") < 0) {
        roxyFileman += "?type=" + type;
    }
    else {
        roxyFileman += "&type=" + type;
    }
    roxyFileman += '&input=' + field_name + '&value=' + document.getElementById(field_name).value;
    roxyFileman += '&integration=tinymce4';
    tinyMCE.activeEditor.windowManager.open({
        file: roxyFileman,
        title: 'File Manager',
        width: 850,
        height: 650,
        resizable: "yes",
        plugins: "media",
        inline: "yes",
        close_previous: "no"
    }, {     window: win,     input: field_name    });
    return false;
}

function CustomRoxyFileBrowser(field_name) {
    var roxyFileman = 'fileman/index.html';
    if (roxyFileman.indexOf("?") < 0) {
        roxyFileman += "?input=" + field_name;
    }
    else {
        roxyFileman += "&input=" + field_name;
    }
    roxyFileman += '&integration=custom&dialogid=roxyCustomPanel';
    console.log(roxyFileman);

    $('#roxyCustomPanel').children().first().attr("src",roxyFileman);
    $('#roxyCustomPanel').dialog({
        modal:true,
        width:875,
        height:600,
        title: 'File Manager'
    });

    return false;
}

//Handler errori ajax
$(document).ajaxError(function (event, jqxhr, settings, thrownError) {
    displayErrorDialog("Errore", "Errore: "+thrownError);
});

//Azione pulsante modifica blocco
$(".modblock").click(function(event) {
    var block = $(event.target).parent().parent();
    var contentdiv = $(event.target).parent().siblings().filter(".blockcontentdiv");
    var textarea = block.children().filter(".blockcontent");
    textarea.val(contentdiv.html());
    contentdiv.remove();
    textarea.show();
    tinyMCE.execCommand('mceAddEditor', false, textarea.attr("id"));
    //Nasconde pulsanti
    block.children().filter(".blockbuttons").children().filter(".modblock").hide();
    block.children().filter(".blockbuttons").children().filter(".applyblock").show();
});

//Azione pulsante salva blocco
$(".applyblock").click(function(event) {
    var block = $(event.target).parent().parent();
    var contentinput = $(event.target).parent().siblings().filter(".blockcontent");
    tinyMCE.execCommand('mceRemoveEditor', false, contentinput.attr("id"));
    var contentdiv = $('<div class="blockcontentdiv"></div>');
    contentdiv.html(contentinput.val());
    contentinput.hide().after(contentdiv);
    //Nasconde pulsanti
    block.children().filter(".blockbuttons").children().filter(".modblock").show();
    block.children().filter(".blockbuttons").children().filter(".applyblock").hide();
});

//Azione pulsante apertura file manager sul server
$('.openfileman').click(function(event) {
    CustomRoxyFileBrowser($(event.target).prev().attr('id'));
});

/* Controllo vincolo di unicità sul nome blocco. */
function checkBlockNameUnique(id, target, callback) {
    target.value = target.value.trim();
    var txtvalue = target.value;
    if (txtvalue == "") {
        $(target).css("border", "solid 2px rgb(202, 60, 60)");
        return false;
    }
    $.getJSON("blockws.php?action=checkname&name="+txtvalue+"&blockid="+id, function(data) {
        if (data.status == "error") {
            //Show error dialog
            $("#erd-errdata").val(data.errormessage);
            $("#error-dialog").dialog("open");
            return false;
        }
        if (data.status == "ok") {
            if (data.result == "true") {
                $(target).css("border", "solid 2px rgb(28, 184, 65)");
                callback(true);
            } else {
                $(target).css("border", "solid 2px rgb(202, 60, 60)");
                displayErrorDialog("Errore", "Impossibile usare lo stesso nome per pi&ugrave; blocchi. Usarne un altro.");
                callback(false);
            }
        }
    });
}

/**
 * Generates and open a new jquery ui dialog with title and message
 * passed as arguments to display errors for the user
 * @param title
 * @param message
 */
function displayErrorDialog(title, message) {
    var html = '<div class="message-error-dialog"><p>'+message+'</p>' +
        '<button type="button" class="pure-button pure-button-primary red-button" style="float: right;">Ok</button>' +
        '</div>';
    var dialog = $(html).dialog({
        title: title,
        modal: true,
        autoOpen: true
    });
    dialog.find("button").click(function() {
        dialog.dialog("close");
    });
}

/* Submit form action */
$('#save-all').click(function() {
    //Checks if user has provided some content for the block (if insert mode)
    if (!UPDATE_MODE) {
        if ( $('[name="content"]').val() == "" && tinymce.activeEditor.getContent() == "") {
            displayErrorDialog("Errore", "Impossibile creare un nuovo blocco senza contenuto.");
            return;
        }
    }
    //Checks if block name is unique
    var namefield = $('input[name="name"]');
    checkBlockNameUnique($("#id").val(), namefield[0], function (result) {
        if (!result) {
            namefield[0].focus();
            return;

        }

        //Submit form
        $("#block-editor-form").submit();
    });
});