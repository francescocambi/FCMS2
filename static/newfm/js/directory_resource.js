/**
 * Created by Francesco on 12/03/15.
 */

function DirectoryResource(params) {

    this.getDirectoryTreeServiceUrl = params.getDirectoryTreeServiceUrl;
    this.moveDirectoryServiceUrl = params.moveDirectoryServiceUrl;
    this.deleteDirectoryServiceUrl = params.deleteDirectoryServiceUrl;

}

DirectoryResource.prototype.loadTree = function (callback) {

    $.getJSON(this.getDirectoryTreeServiceUrl, function (data) {
        if (data.status) {
            // Transform data.root directory tree
            //in a tree of Directory objects
            var root = Directory.directoryFactory(data.root);

            if (callback != undefined) callback(root);
        }
    });

};

DirectoryResource.prototype.move = function (oldPath, newPath, callback) {

    $.getJSON(this.moveDirectoryServiceUrl, {
        sourcePath: oldPath,
        destinationPath: newPath
    }, function (data) {
        if (data.status) {
            //All ok
            if (callback != undefined)
                callback(newPath);
        }
    });

};

DirectoryResource.prototype.deleteDirectory = function (directory, callback) {

    $.getJSON(this.deleteDirectoryServiceUrl, {
        directoryPath: directory.getPath()
    }, function (data) {
        if (data.status) {
            //All ok
            if (callback != undefined)
                callback(true);
        }
    });

};