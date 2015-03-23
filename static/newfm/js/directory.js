/**
 * Created by Francesco on 11/03/15.
 */

function Directory(params) {

    this.name = params.name;
    this.parent = params.parent;

    this.subdirectories = [];

}

Directory.directoryFactory = function (object, parentDirectory) {

    //Checks if object has properties to become a Directory
    if (!(object.name != undefined && object.subdirectories != undefined)) {
        //object argument not valid
        return undefined;
    }

    // object is valid so
    //Creates Directory object
    var directory = new Directory({
        name: object.name,
        parent: parentDirectory
    });

    //Adds subdirectories to Directory object just created
    jQuery.each(object.subdirectories, function (index, object) {
        //Make each object a Directory object
        //then pass it as a subdirectory of new directory
        directory.addSubdirectory(
            Directory.directoryFactory(object, directory)
        );
    });

    return directory;

};

Directory.prototype.addSubdirectory = function (directory) {
    if (directory instanceof Directory)
        this.subdirectories.push(directory);
};

Directory.prototype.removeSubdirectory = function (directory) {
    this.subdirectories.splice(this.subdirectories.indexOf(directory), 1);
};

Directory.prototype.isRoot = function () {
    return (this.parent === undefined);
};

Directory.prototype.getPath = function () {

    //If is root return /
    if (this.isRoot()) return '/';

    var currentDirectory = this;
    var pathString = "";

    //Moves up on the tree until it finds root node
    while (!currentDirectory.isRoot()) {
        //Prepends directory name to path
        pathString = "/"+currentDirectory.name+pathString;
        //Moves up currentDirectory of one level
        currentDirectory = currentDirectory.parent;
    }

    return pathString;
};

Directory.prototype.setParentDirectory = function (newParent) {
    if (this.parent != undefined) {
        this.parent.removeSubdirectory(this);
    }

    this.parent = newParent;
};