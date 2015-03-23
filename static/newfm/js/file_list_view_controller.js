/**
 * Created by Francesco on 11/03/15.
 */

function FileListViewController(params) {

    this.fileResource = new FileResource(params.fileResourceUrls);

    this.selectedFile = undefined;

    this.selectedSorter = File.nameSorterFactory('asc');

    this.files = [];

    this.filterString = "";

    this.fileListTable = $('#file-list-table').children('tbody');

    this.deleteConfirmationDialogHtml = "" +
    "<div>" +
    "<p>Are you sure you want to delete selected file?</p>" +
    "</div>";

    this.draggingItemObservers = [];

}

FileListViewController.prototype.addDraggingItemObserver = function (observer) {
    this.draggingItemObservers.push(observer);
};

FileListViewController.prototype.removeDraggingItemObserver = function (observer) {
    var index = this.draggingItemObservers.indexOf(observer);
    if (index > 0)
        this.draggingItemObservers.splice(index, 1);
};

FileListViewController.prototype.notifyDraggingItemObservers = function (draggingItem) {
    jQuery.each(this.draggingItemObservers, function (index, observer) {
        if (observer.updateDraggingItem != undefined)
            observer.updateDraggingItem(draggingItem);
    });
};

FileListViewController.prototype.refreshFileList = function (directory) {
    showMessage('wait');
    var controller = this;
    this.fileResource.loadAll(directory, function (files) {
        //Set controller.files = files
        controller.files = files;
        //Then trigger table redraw
        //controller.redrawFileTable
        controller.redrawFileTable();

        showMessage('success');
    })
};

FileListViewController.prototype.redrawFileTable = function () {
    var files = this.files;

    //If a filter string was set, filter files
    if (this.filterString.length > 0)
        files = jQuery.grep(files,
            File.filterFunctionFactory(this.filterString)
        );

    //Then sorts filteredfiles with controller.sorter
    //that is the currently selected sorter for files
    files = files.sort(this.selectedSorter);

    //Clear files table
    this.fileListTable.children().remove();

    //Then calls createFileTableRow(file) that creates
    //for each file a table row to insert in file table
    var controller = this;
    jQuery.each(files, function (index, object) {
        //Append row to file table
        controller.fileListTable.append(
            controller.drawFileTableRow(index, object)
        );
    });
};

FileListViewController.prototype.filterFiles = function (event) {
    //Retrieve search string
    this.filterString = $(event.target).val();

    this.redrawFileTable();
};

FileListViewController.prototype.drawFileTableRow = function (index, file) {
    var date = new Date(file.lastUpdate*1000);

    var dateTimeString = stringLeftFill(date.getDay(), 2, '0')+"/"+stringLeftFill((date.getMonth()+1), 2, '0')+"/"+date.getFullYear();
    dateTimeString += " "+stringLeftFill(date.getHours(), 2, '&nbsp;')+":"+stringLeftFill(date.getMinutes(), 2, '0');

    var extension = file.name.split('.').pop().toLowerCase();
    var icon = FILE_ICONS[extension];

    if (icon == undefined) icon = 'fa-file-o';

    var html = '<tr draggable="true">' +
        '<td class="file-icon"><i class="fa '+icon+'"></i></td>' +
        '<td class="file-name">'+file.name+'</td>' +
        '<td class="file-size">'+formatFileSize(file.size)+'</td>' +
        '<td class="file-lastupdate">'+dateTimeString+'</td>' +
        '</tr>';

    //Attach selection event handler
    var row = $(html);
    var controller = this;
    row.click(function (event) {
        controller.fileSelected(event);
    });

    //Attach drag and drop event handler
    row[0].ondragstart = function (event) {
        var draggingItem = new DraggingItem(event.target, function (source, target) {
            controller.fileDropped(source, target);
        });
        controller.notifyDraggingItemObservers(draggingItem);
    };

    //Attach file object to file row
    row.data("file", file);

    return row;
};

FileListViewController.prototype.update = function (message) {
    if (message.type == 'folderSelected') {
        this.refreshFileList(message.selectedDirectory);
    }
};

FileListViewController.prototype.orderChanged = function (event) {
    var orderValue = $(event.target).val().split('-');

    switch (orderValue[0]) {
        case 'name':
            this.selectedSorter = File.nameSorterFactory(orderValue[1]);
            break;
        case 'size':
            this.selectedSorter = File.sizeSorterFactory(orderValue[1]);
            break;
        case 'lastupdate':
            console.log($(event.target).val());
            this.selectedSorter = File.lastUpdateSorterFactory(orderValue[1]);
            break;
    }

    this.redrawFileTable();
};

FileListViewController.prototype.fileSelected = function (event) {
    var clickedRow;
    var selectedRow = $('.selected-file');

    //Checks if event.target is tr element and retrieves it
    if ( $(event.target).prop('tagName').toLowerCase() == "tr" )
        clickedRow = $(event.target);
    else
        clickedRow = $(event.target).parents('tr');

    //Checks if clicked row was already selected
    if ( !selectedRow.is(clickedRow) ) {
        //If it was not deselect previously selected file
        selectedRow.removeClass('selected-file');
        //Select clicked row
        clickedRow.addClass('selected-file');
    }

    //Retrieve file object
    this.selectedFile = clickedRow.data('file');
};

FileListViewController.prototype.launchFileDownload = function () {
    this.fileResource.download($('.selected-file').data('file'));
};

FileListViewController.prototype.displayFilePreview = function () {
    var dialog = $('#preview-dialog').clone();

    dialog.attr('id', '');
    dialog.dialog({
        title: "Preview",
        autoOpen: false,
        width: $(window).width()*0.75,
        height: $(window).height()*0.8
    });
    dialog.children('iframe').attr('src', this.selectedFile.getUrl());
    dialog.dialog('open');
};

FileListViewController.prototype.showDeleteConfirmationDialog = function () {
    var controller = this;
    $(this.deleteConfirmationDialogHtml).dialog({
        title: 'Confirm File Deletion',
        resizable: false,
        modal: true,
        buttons: {
            "Delete": function () {
                var selectedFile = $('.selected-file');
                if (selectedFile.size() > 0)
                    controller.deleteFile(selectedFile);
                $(this).dialog("close");
            },
            Cancel: function() {
                $(this).dialog("close");
            }
        }
    });
};

FileListViewController.prototype.deleteFile = function (fileRow) {
    var file = fileRow.data('file');

    this.fileResource.remove(file, function () {
        delete file;
        fileRow.remove();
    });
};

FileListViewController.prototype.displayFileInlineEditor = function (fileRow) {
    //fileRow not undefined
    if (fileRow == undefined) return;
    //Checks editor not already displayed
    if (fileRow.find('.file-name input').size() > 0) return;

    //Replace file name with text field
    var textField = $('<input type="text">');
    var fileNameCell = fileRow.find('.file-name');
    textField.val(fileNameCell.text());
    fileNameCell.text('');
    fileNameCell.append(textField);
    //Attach event handler to txt-file-name field
    var controller = this;
    textField.blur(function (event) {
        controller.inlineEditingDidFinish(fileRow);
    });

    textField.focus();
};

FileListViewController.prototype.inlineEditingDidFinish = function (fileRow) {
    var fileNameCell = fileRow.find('.file-name');
    //Takes text field
    var textField = fileNameCell.find('input[type="text"]');
    //Display file name in cell element and removes file name editor field
    textField.remove();
    fileNameCell.text(textField.val());

    //Commit changes on server using fileResource
    var file = fileRow.data('file');
    var oldPath = file.getPath();
    file.name = textField.val();
    this.fileResource.move(oldPath, file.getPath());
};

FileListViewController.prototype.fileDropped = function (sourceElement, targetElement) {
    var targetDirectory = targetElement.parents('.folder-line').parent().data('directory');
    var sourceFile = sourceElement.data('file');

    //console.log(sourceFile.name+" dropped on "+targetDirectory.name);

    var oldPath = sourceFile.getPath();
    //Changes parent directory of dropped file
    sourceFile.parentDirectory = targetDirectory;
    //Send changes on server
    this.fileResource.move(oldPath, sourceFile.getPath(), function () {
        //Changes saved so remove fileRow from file list table
        sourceElement.remove();
    });
};

FileListViewController.prototype.exportFile = function () {
    var file = this.selectedFile;
    var integration = getUrlParam('integration');

    switch(integration.toLowerCase()) {
        case 'ckeditor':
            window.opener.CKEDITOR.tools.callFunction(getUrlParam('CKEditorFuncNum'), file.getUrl());
            self.close();
            break;
        case 'tinymce3':
            var url = file.getUrl();
            var win = tinyMCEPopup.getWindowArg('window');
            win.document.getElementById(tinyMCEPopup.getWindowArg('input')).value = url;
            if (typeof(win.ImageDialog) != "undefined") {
                if (win.ImageDialog.getImageData)
                    win.ImageDialog.getImageData();

                if (win.ImageDialog.showPreviewImage)
                    win.ImageDialog.showPreviewImage(url);
            }
            tinyMCEPopup.close();
            break;
        case 'tinymce4':
            var win = (window.opener?window.opener:window.parent);
            win.document.getElementById(getUrlParam('input')).value = file.getUrl();
            if (typeof(win.ImageDialog) != 'undefined') {
                if (win.ImageDialog.getImageData)
                    win.ImageDialog.getImageData();
                if (win.ImageDialog.showPreviewImage)
                    win.ImageDialog.showPreviewImage(file.getUrl());
            }
            win.tinyMCE.activeEditor.windowManager.close();
            break;
        default:
            CustomExportHandler.exportFile(file);
            break;
    }
};