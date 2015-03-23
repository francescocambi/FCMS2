/**
 * Created by Francesco on 11/03/15.
 */

function FileResource(params) {

    this.listContentServiceUrl = params.listContentServiceUrl;
    this.deleteFileServiceUrl = params.deleteFileServiceUrl;
    this.moveFileServiceUrl = params.moveFileServiceUrl;
    this.downloadFileServiceUrl = params.downloadFileServiceUrl;

}

FileResource.prototype.loadAll = function (directory, callback) {
    $.getJSON(this.listContentServiceUrl,
        {
            folderPath: directory.getPath()
        }, function (data) {
            if (data.status) {
                //All ok
                var loadedFiles = [];
                jQuery.each(data.fileList, function (index, file) {
                    loadedFiles[index] = new File({
                        name: file.name,
                        size: file.size,
                        lastUpdate: file.lastUpdate,
                        parentDirectory: directory
                    });
                });
                if (callback != undefined) callback(loadedFiles);
            }
            });
};

FileResource.prototype.remove = function (file, callback) {
    $.getJSON(this.deleteFileServiceUrl, {
        filePath: file.getPath()
    }, function (data) {
        if (data.status) {
            //All ok
            if (callback != undefined)
                callback(true);
        }
    });
};

FileResource.prototype.move = function (oldPath, newPath, callback) {
    $.getJSON(this.moveFileServiceUrl, {
        sourceFilePath: oldPath,
        destinationFilePath: newPath
    }, function (data) {
        if (data.status) {
            //All ok
            if (callback != undefined)
                callback(newPath);
        }
    });
};

FileResource.prototype.download = function (file) {
    if (file === undefined) return;

    //Download a file from a url
    var url = this.downloadFileServiceUrl+"?filePath="+file.getPath();
    // Get file name from url.
    var filename = url.substring(url.lastIndexOf("/") + 1).split("?")[0];
    var xhr = new XMLHttpRequest();
    xhr.responseType = 'blob';
    xhr.onload = function() {
        var a = document.createElement('a');
        a.href = window.URL.createObjectURL(xhr.response); // xhr.response is a blob
        a.download = filename; // Set the file name.
        a.style.display = 'none';
        document.body.appendChild(a);
        a.click();
        delete a;
    };
    xhr.open('GET', url);
    xhr.send();
}