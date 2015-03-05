/**
 * Created by Francesco on 05/03/15.
 */
/**
 * Generates and open a new jquery ui dialog with title and message
 * passed as arguments to display errors for the user
 * @param title
 * @param message
 * @param description
 */
function displayErrorDialog(title, message, description) {
    var html = '<div class="message-error-dialog"><p>'+message+'</p>';
    if (description != undefined && description != "")
        html += '<p>'+description+'</p>';

    html += '<button type="button" class="pure-button pure-button-primary red-button" style="float: right;">Ok</button>' +
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

//Handler errori ajax
$(document).ajaxError(function (event, jqxhr, settings, thrownError) {
    var description = "";
    if (jqxhr.responseText != "")
        description = jQuery.parseJSON(jqxhr.responseText).exception;
    displayErrorDialog("Errore", "Errore: "+thrownError, description);
});