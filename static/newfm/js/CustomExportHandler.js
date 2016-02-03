/**
 * Created by Francesco on 23/03/15.
 */

function CustomExportHandler() {
}

CustomExportHandler.exportFile = function (file) {

    var win = (window.opener?window.opener:window.parent);
    //win.document.getElementById(getUrlParam('input')).value = file.getUrl();
    win.jQuery(win.document).find("#"+getUrlParam('input')).val(file.getUrl()).trigger('change');
    var dialogid = getUrlParam('dialogid');
    win.jQuery(win.document).find('#'+dialogid).dialog('close');

};