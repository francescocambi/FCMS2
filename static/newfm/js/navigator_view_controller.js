/**
 * Created by Francesco on 16/03/15.
 */

function NavigatorViewController(params) {

    this.directoryResource = new DirectoryResource(params.directoryResourceUrls);

    this.selectedDirectory = undefined;

    this.directoryTreeRoot = undefined;

    this.draggingItem = undefined;

    this.folderTreeList = $('#folders-tree-list');

    this.observers = [];

    this.deleteConfirmationDialogHtml = "" +
    "<div>" +
    "<p>Are you sure you want to delete this folder and all files inside it?</p>" +
    "</div>";

}

NavigatorViewController.prototype.folderSelected = function (event) {
    var clickedFolder;
    var selectedFolder = $('.selected-folder');

    //Retrieve clicked folder
    if( $(event.target).hasClass('folder-line') ) {
        clickedFolder = $(event.target);
    } else {
        clickedFolder = $(event.target).parent();
    }

    //If clicked folder is in edit mode, ignore click
    if (clickedFolder.find('input').size() > 0) return;

    //Checks if clicked folder was already selected
    if ( !selectedFolder.is(clickedFolder) ) {
        //If it was not deselect previously selected folder
        selectedFolder.removeClass('selected-folder');
        //Select clicked folder
        clickedFolder.addClass('selected-folder');
        //Retrieve Directory object clicked and set it as an object property
        this.selectedDirectory = clickedFolder.parent().data('directory');
    }

    //Notify observers
    this.notify({
        type: 'folderSelected',
        selectedDirectory: this.selectedDirectory
    });
};

NavigatorViewController.prototype.notify = function (message) {
  jQuery.each(this.observers, function (i, observer) {
      //try {
          observer.update(message);
      //} catch (err) {
      //    console.log("Catched: "+err);
      //}
  });
};

NavigatorViewController.prototype.addObserver = function (observer) {
    this.observers.push(observer);
};

NavigatorViewController.prototype.removeObserver = function (observer) {
    var i = this.observers.indexOf(observer);
    if (i > -1) this.observers.splice(i, 1);
};

NavigatorViewController.prototype.refreshFolderList = function () {
    showMessage('wait');

    var navController = this;
    this.directoryResource.loadTree(function (root) {
        //Set controller directoryTreeRoot equals
        //to just loaded tree root element
        navController.directoryTreeRoot = root;

        //Invoke redrawing of directory tree list
        navController.drawFolderList(root);

        //Set event handlers
        $('.toggle-content').click(navController.toggleFolderContent);
        var folderLine = $('.folder-line');
        folderLine.click(function (event) {
            navController.folderSelected(event);
        });

        //Root is not draggable
        folderLine.first().attr('draggable', 'false');

        //Expand root directory and trigger file list refresh
        folderLine.first().click();
        folderLine.first().siblings('.fa-plus-square').click();

        showMessage('success');
    });
};

NavigatorViewController.prototype.drawFolderList = function (root) {
    this.folderTreeList.append(
        this.drawFolderListItem(root)
    );
};

NavigatorViewController.prototype.drawFolderListItem = function (directory) {
    var controller = this;
    var hasSubdirectories = directory.subdirectories.length > 0;

    //Creates li element
    var li = $('<li></li>');
    //Checks if directory has subdirectories
    if (hasSubdirectories) {
        //Yes, so add plus button
        li.append($('<i class="fa fa-plus-square toggle-content"></i>'));
    } else {
        //No, so mark as empty folder
        li.addClass('leaf-folder');
    }

    //Binds li element with directory object
    li.data("directory", directory);

    //Creates folder-line element
    var folderLine = $('<div class="folder-line" ' +
    'draggable="true" ondragover="allowDrop(event)">' +
    '<i class="fa fa-folder"></i><span>'+directory.name+'</span></div>');

    //Attach drag and drop event handler
    folderLine[0].ondrop = function (event) {
        controller.drop(event);
    };

    folderLine[0].ondragstart = function (event) {
        controller.draggingItem = new DraggingItem(event.target, function (source, target) {
            controller.dropFolder(source, target);
        });
    };

    li.append(folderLine);

    if (hasSubdirectories) {
        //Creates ul for subdirectories
        var ul = $('<ul class="folder-list"></ul>').hide();

        //For each subdirectory of directory, creates a new list item
        //and append those at the new ul list
        jQuery.each(directory.subdirectories, function (index, subdir) {
            ul.append(
                controller.drawFolderListItem(subdir)
            );
        });

        //Append subdirectories ul to this directory li element
        li.append(ul);
    }

    return li;
};

NavigatorViewController.prototype.toggleFolderContent = function (event) {
    //Retrieve ul to collapse
    var li = $(event.target).parent();
    var ul = li.children('ul:hidden');
    if (ul.size() > 0) {
        //Open sublist
        ul.show();
        li.children('.fa').removeClass('fa-plus-square').addClass('fa-minus-square');
        li.children('.folder-line').children('.fa-folder')
            .removeClass('fa-folder')
            .addClass('fa-folder-open');
    }
    else if (li.children('ul:visible').size() > 0) {
        //Close sublist
        li.children('ul').hide();
        li.children('.fa').removeClass('fa-minus-square').addClass('fa-plus-square');
        li.children('.folder-line').children('.fa-folder-open')
            .removeClass('fa-folder-open')
            .addClass('fa-folder');
    }
};

NavigatorViewController.prototype.createFolder = function () {
    //New directory must be subdirectory of something
    if (this.selectedDirectory == undefined) return;

    //Creates new dir as a subdirectory of currently selected folder
    var newDirectory = new Directory({
        name: 'New Folder',
        parent: this.selectedDirectory,
        subdirectories: []
    });
    var newFolder = this.drawFolderListItem(newDirectory);
    this.selectedDirectory.addSubdirectory(newDirectory);

    //Begin folder inline editing
    this.displayFolderInlineEditor(newFolder.children('.folder-line'));

    var selectedFolder = $('.selected-folder');

    //Checks if selected folder is leaf
    //In this case plus button and ul must be added
    if (selectedFolder.parent().hasClass('leaf-folder')) {
        this.createSubfoldersView(selectedFolder);
        //Open subfolders list
        selectedFolder.siblings('i').click();
    }

    selectedFolder.siblings('ul').append(newFolder);

    //Set folder-line event handlers
    newFolder.find('.folder-line').click(function (event) {
        navController.folderSelected(event);
    });
};

NavigatorViewController.prototype.displayFolderInlineEditor = function (folderLine) {
    //folderLine not undefined
    if (folderLine == undefined) return;
    //Check if editor isn't already displayed
    if (folderLine.find('input').size() > 0) return;
    //Checks directory so prevent renaming of root directory
    if (folderLine.parent().data('directory').isRoot()) return;

    //Replace folder-line text with text field
    var textField = $('<input type="text">');
    textField.val(folderLine.children('span').text());
    folderLine.children('span').text('');
    folderLine.append(textField);
    //Attach event handler to txt-folder-name field
    var navController = this;
    textField.blur(function (event) {
        navController.inlineEditingDidFinish(folderLine);
    });

    //TODO Take focus on textFiled input element
    textField.focus();
    //???
};

NavigatorViewController.prototype.inlineEditingDidFinish = function (folderLine) {

    //Takes text field
    var textField = folderLine.children('input[type="text"]');
    //Display folder name in span element
    folderLine.children('span').text(textField.val());
    //Remove directory name editor text field
    textField.remove();

    // Commit changes on server using directoryResource
    var directory = folderLine.parent().data('directory');
    var oldPath = directory.getPath();
    directory.name = textField.val();
    this.directoryResource.move(oldPath, directory.getPath());
};

NavigatorViewController.prototype.showDeleteConfirmationDialog = function () {
    var navController = this;
    $(this.deleteConfirmationDialogHtml).dialog({
        title: 'Confirm Folder Deletion',
        resizable: false,
        modal: true,
        buttons: {
            "Delete": function () {
                navController.deleteFolder($('.selected-folder'));
                $(this).dialog("close");
            },
            Cancel: function() {
                $(this).dialog("close");
            }
        }
    });
};

NavigatorViewController.prototype.deleteFolder = function (folderLine) {
    var folderLi = folderLine.parent();
    var directory = folderLi.data('directory');

    var navController = this;

    // Sends delete directory request to server
    this.directoryResource.deleteDirectory(directory, function () {
        delete directory;
        var parentUl = folderLi.parent();
        folderLi.remove();

        if (parentUl.children().size() == 0)
            navController.removeSubfoldersView(parentUl);

        $('#folders-tree-list').children().first().children('.folder-line').click();
    });
};

NavigatorViewController.prototype.drop = function (event) {
    var target = $(event.target);
    var source = this.draggingItem;

    if (target.is(source.element)) return;

    //Calls drop handler function
    source.dropHandler(target);
};

NavigatorViewController.prototype.dropFolder = function (source, target) {

    var targetDirectory = target.parents('.folder-line').parent().data('directory');
    var sourceDirectory = source.parent().data('directory');

    //Prevents dropping an element on itself
    if (targetDirectory == sourceDirectory) return;

    var oldPath = sourceDirectory.getPath();
    //Change source parent directory with target directory
    sourceDirectory.setParentDirectory(targetDirectory);
    //Commit changes on server
    var navController = this;
    this.directoryResource.move(oldPath, sourceDirectory.getPath(), function () {
        //Update navigator view
        //Checks if li element has an ul for subfolders
        var ul;
        var folderLine = target.parents('.folder-line');
        if (folderLine.siblings('ul').size() == 0) {
            //If not, creates that ul and plus button
            ul = navController.createSubfoldersView(folderLine);
        } else {
            ul = folderLine.siblings('ul');
        }

        var parentUlNode = source.parent().parent();
        //Append dropped element to target ul
        ul.append(source.parent());

        //If dragged item was the last element in the ul
        //make previus parent node an empty-folder node
        if (parentUlNode.children().size() == 0) {
            navController.removeSubfoldersView(parentUlNode);
        }
    });
};

NavigatorViewController.prototype.createSubfoldersView = function (folderLine) {
    //If not, creates that ul and plus button
    var plusButton = $('<i class="fa fa-plus-square toggle-content"></i>');
    plusButton.click(this.toggleFolderContent);
    folderLine.parent().prepend(plusButton);
    var ul = $('<ul class="folder-list"></ul>').hide();
    folderLine.parent().append(ul);
    folderLine.parent().removeClass('leaf-folder');

    //Returns created ul element for subfolders
    return ul;
};

NavigatorViewController.prototype.removeSubfoldersView = function (ulNode) {
    //make previus parent node an empty-folder node
    var parentLiNode = ulNode.parent();
    //Make li an empty-folder node and removes plus button
    parentLiNode.addClass('leaf-folder');
    parentLiNode.children('.fa').remove();
    //Swap folder icon with closed folder icon
    parentLiNode.find('.folder-line i').removeClass('fa-folder-open').addClass('fa-folder');
    //Removes empty ul
    ulNode.remove();
};

NavigatorViewController.prototype.updateDraggingItem = function (item) {
    this.draggingItem = item;
};