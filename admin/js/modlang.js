/**
 * Created by francesco on 10/10/14.
 */

/**
 * Created by francesco on 10/9/14.
 */

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

//Azione pulsante apertura file manager sul server
$('.openfileman').click(function(event) {
    CustomRoxyFileBrowser($(event.target).prev().attr('id'));
});

/* Controllo vincolo di unicità sul codice della lingua. */
function checkLangCodeUnique(id, target, callback) {
    target.value = target.value.trim();
    var txtvalue = target.value;
    if (txtvalue == "") {
        $(target).css("border", "solid 2px rgb(202, 60, 60)");
        return false;
    }
    $.getJSON("langws.php?action=checkcode&code="+txtvalue+"&langid="+id, function(data) {
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
                displayErrorDialog("Errore", "Impossibile usare lo stesso codice per pi&ugrave; lingue. Usarne un altro.");
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
    //Checks description not null
    var descfield = $('input[name="description"]');
    descfield.val(descfield.val().trim());
    if (descfield.val() == "") {
        descfield.css("border", "solid 2px rgb(202, 60, 60)");
        return false;
    } else {
        descfield.css("border", "solid 2px rgb(28, 184, 65)");
    }

    //Checks if language code is unique
    var codefield = $('input[name="code"]');
    checkLangCodeUnique($("#id").val(), codefield[0], function (result) {
        if (!result) {
            codefield[0].focus();
            return;

        }

        //Submit form
        $("#language-editor-form").submit();
    });
});