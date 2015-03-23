/**
 * Created by francesco on 10/10/14.
 */

/**
 * Created by francesco on 10/9/14.
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
    $.post("/admin/languages/checkCode", {code: txtvalue, id: id} , function(data) {
        if (data.status) {
            if (data.data.unique) {
                $(target).css("border", "solid 2px rgb(28, 184, 65)");
                callback(true);
            } else {
                $(target).css("border", "solid 2px rgb(202, 60, 60)");
                displayErrorDialog("Errore", "Impossibile usare lo stesso codice per pi&ugrave; lingue. Usarne un altro.");
                callback(false);
            }
        }
    }, 'json');
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