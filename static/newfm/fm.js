/**
 * Created by Francesco on 07/03/15.
 */

$('button').addClass('pure-button');
//$('button').addClass('pure-button-primary');

var FILE_ICONS = {
    png: 'fa-file-image-o',
    jpg: 'fa-file-image-o',
    jpeg: 'fa-file-image-o',
    gif: 'fa-file-image-o',
    pdf: 'fa-file-pdf-o',
    xls: 'fa-file-excel-o',
    xlsx: 'fa-file-excel-o',
    doc: 'fa-file-word-o',
    docx: 'fa-file-word-o',
    ppt: 'fa-file-powerpoint-o',
    pptx: 'fa-file-powerpoint-o',
    zip: 'fa-file-zip-o',
    rar: 'fa-file-zip-o',
    tar: 'fa-file-zip-o',
    gz: 'fa-file-zip-o',
    bz: 'fa-file-zip-o',
    bz2: 'fa-file-zip-o',
    php: 'fa-file-code-o',
    js: 'fa-file-code-o',
    twig: 'fa-file-code-o',
    html: 'fa-file-code-o',
    xhtml: 'fa-file-code-o',
    htm: 'fa-file-code-o',
    css: 'fa-file-code-o',
    xml: 'fa-table',
    json: 'fa-table',
    sh: 'fa-cog',
    htaccess: 'fa-cog',
    sqlite: 'fa-database',
    sql: 'fa-database',
    log: 'fa-file-text-o',
    txt: 'fa-file-text-o'

};

var draggingItem = undefined;

//Handler errori ajax
$(document).ajaxError(function (event, jqxhr, settings, thrownError) {
    var description = "";
    if (jqxhr.responseText != "")
        description = jQuery.parseJSON(jqxhr.responseText).exception;
    showMessage('error');
    console.log("Errore:", thrownError, "-- Description:", description);
});

function allowDrop(event) {
    event.preventDefault();
}

function drag(event) {
    draggingItem = $(event.target);
    //console.log("Dragging "+$(event.target).text());
}

function DraggingItem(element, dropHandler) {
    this.element = element;
    this.handlerFunction = dropHandler;

    this.dropHandler = function (targetElement) {
        this.handlerFunction($(this.element), $(targetElement));
    };
}

/**
 * Show message status indicator
 * @params string status can be one of [success, wait, error]
 */
function showMessage(status) {
    $('#messages').children().hide();
    $('#'+status+'-message').show();
}

/**
 * Converts an int number of bytes in a human
 * readable file size
 * @param a int number of bytes
 * @returns {string} human readable size
 */
function formatFileSize(a,b,c,d,e){
    return (b=Math,c=b.log,d=1e3,e=c(a)/c(d)|0,a/b.pow(d,e)).toFixed(2)
    +' '+(e?'kMGTPEZY'[--e]+'B':'B')
}

/**
 * Fills string with filler on the left to
 * make string become long length
 * @param string
 * @param length
 * @param filler
 */
function stringLeftFill(string, length, filler) {
    string += '';
    for (var i=string.length; i<length; i++) {
        string = filler+string;
    }
    return string;
}

/**
 * Extracts varName value from url querystring
 * @param varName
 * @param url if not specified takes current location
 * @returns {string}
 */
function getUrlParam(varName, url) {
    var ret = '';
    if (!url)
        url = self.location.href;
    if (url.indexOf('?') > -1) {
        url = url.substr(url.indexOf('?') + 1);
        url = url.split('&');
        for (i = 0; i < url.length; i++) {
            var tmp = url[i].split('=');
            if (tmp[0] && tmp[1] && tmp[0] == varName) {
                ret = tmp[1];
                break;
            }
        }
    }
    return ret;
}

var fileListController;

var navController;

$(document).ready(function () {
    showMessage('wait');
    $.when(
        $.getScript('/static/newfm/js/download.js'),
        $.getScript('/static/newfm/js/directory.js'),
        $.getScript('/static/newfm/js/file.js'),
        $.getScript('/static/newfm/js/file_resource.js'),
        $.getScript('/static/newfm/js/file_list_view_controller.js'),
        $.getScript('/static/newfm/js/directory_resource.js'),
        $.getScript('/static/newfm/js/navigator_view_controller.js'),
        $.getScript('/static/newfm/js/CustomExportHandler.js')
    ).then(init);
});

function init() {
    fileListController = new FileListViewController({
        fileResourceUrls: {
            listContentServiceUrl: listContentServiceUrl,
            moveFileServiceUrl: moveFileServiceUrl,
            deleteFileServiceUrl: deleteFileServiceUrl,
            downloadFileServiceUrl: downloadFileServiceUrl
        }
    });
    navController = new NavigatorViewController({
        directoryResourceUrls: {
            getDirectoryTreeServiceUrl: getDirectoryTreeServiceUrl,
            moveDirectoryServiceUrl: moveDirectoryServiceUrl,
            deleteDirectoryServiceUrl: deleteDirectoryServiceUrl
        }
    });

    File.rootFolderUrl = rootFolderUrl;

    navController.addObserver(fileListController);
    fileListController.addDraggingItemObserver(navController);
    navController.refreshFolderList();
    $('#txt-search').change(function (event) {
        fileListController.filterFiles(event);
    }).keyup(function (event) {
        fileListController.filterFiles(event);
    });
    $('#sel-order').change(function (event) {
        fileListController.orderChanged(event);
    });

    $('#btn-preview-file').click(function (event) {
        fileListController.displayFilePreview();
    });

    $('#btn-download-file').click(function (event) {
        fileListController.launchFileDownload();
    });

    $('#btn-create-folder').click(function (event) {
        navController.createFolder();
    });

    $('#btn-rename-folder').click(function (event) {
        navController.displayFolderInlineEditor($('.selected-folder'));
    });

    $('#btn-delete-folder').click(function (event) {
        navController.showDeleteConfirmationDialog();
    });

    $('#btn-delete-file').click(function (event) {
        if ($('.selected-file').size() > 0)
            fileListController.showDeleteConfirmationDialog();
    });

    $('#btn-rename-file').click(function (event) {
        if ($('.selected-file').size() == 1)
            fileListController.displayFileInlineEditor($('.selected-file'));
    });

    $('#btn-export-file').click(function (event) {
        if ($('.selected-file').size() == 1)
            fileListController.exportFile();
    });

    // Begin Dropzone.js Configuration

    $('#upload-modal').dialog({
        title: "Upload Status",
        autoOpen: false
        //width: $(window).width()*0.65,
        //height: $(window).height()*0.85
    });

    var previewNode = document.querySelector('#template');
    previewNode.id = "";
    var previewTemplate = previewNode.parentNode.innerHTML;
    previewNode.parentNode.removeChild(previewNode);

    var myDropzone = new Dropzone($('#file-view')[0], {
        url: uploadFileServiceUrl,
        thumbnailWidth: 40,
        thumbnailHeight: 40,
        parallelUploads: 2,
        previewTemplate: previewTemplate,
        autoQueue: false,
        previewsContainer: "#previews",
        clickable: '#btn-add-file'
    });

    myDropzone.on("addedfile", function (file) {
        $('#upload-modal').dialog('option','width', $(window).width()*0.65)
            .dialog('option','height', $(window).height()*0.85)
            .dialog('open');
    });

    myDropzone.on("queuecomplete", function (file, response) {
        $('.selected-folder').click();
    });

    myDropzone.on('sending', function (file, xhr, formData) {
        formData.append('folderPath', navController.selectedDirectory.getPath());
    });

    document.querySelector('#overall-start').onclick = function() {
        myDropzone.enqueueFiles(myDropzone.getFilesWithStatus(Dropzone.ADDED));
    };

    document.querySelector("#overall-cancel").onclick = function () {
        myDropzone.removeAllFiles(true);
    };

    // End Dropzone.js Configuration

    showMessage('success');

}
