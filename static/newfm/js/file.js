/**
 * Created by Francesco on 11/03/15.
 */

function File(params) {

    this.name = params.name;
    this.size = params.size;
    this.lastUpdate = params.lastUpdate;

    this.parentDirectory = params.parentDirectory

}

File.nameSorterFactory = function (order) {
    return function (a, b) {
        var x = (order == 'desc' ? 0 : 2);
        a = a.name.toLowerCase();
        b = b.name.toLowerCase();
        if (a > b)
            return -1 + x;
        else if (a < b)
            return 1 - x;
        else
            return 0;
    };
};

File.sizeSorterFactory = function (order) {
    return function (a, b) {
        var x = (order == 'desc' ? 0 : 2);
        a = parseInt(a.size);
        b = parseInt(b.size);
        if (a > b)
            return -1 + x;
        else if (a < b)
            return 1 - x;
        else
            return 0;
    }
};

File.lastUpdateSorterFactory = function (order) {
    return function (a, b) {
        var x = (order == 'desc' ? 0 : 2);
        a = parseInt(a.lastUpdate);
        b = parseInt(b.lastUpdate);
        if (a > b)
            return -1 + x;
        else if (a < b)
            return 1 - x;
        else
            return 0;
    }
};

File.filterFunctionFactory = function (filterString) {
    return function (file, index) {
        return (file.name.toLowerCase().indexOf(filterString.toLowerCase()) > -1)
    };
};

File.prototype.toString = function () {
    return this.name;
};

File.prototype.getUrl = function (rootUrl) {
    if (rootUrl == undefined) rootUrl = File.rootFolderUrl;

    var filePath = this.getPath();
    if (rootUrl.charAt(rootUrl.length-1) == '/')
        rootUrl = rootUrl.substr(0, rootUrl.length-1);

    return (rootUrl+filePath);
};

File.prototype.getPath = function () {
    var path = this.parentDirectory.getPath();
    if (path == "/")
        path += this.name;
    else
        path += "/"+this.name;
    return path;
};