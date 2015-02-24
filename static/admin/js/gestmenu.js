/**
 * Created by Francesco on 08/10/14.
 */


/* Delete item button pressed */
$(".delete-li").click(function(event) {
    $(event.target).parent().parent().remove();
});

/* New item button pressed */
$(".new-li-a").click(function(event) {
    //Clone template
    var cloned = $("#li-template").clone(true).show();
    cloned.attr("id", "");

    //Set item parameters
    var ul = $(event.target).parent().parent();
    var level = ul.attr("level");
    cloned.attr("level", level);
    cloned.find('input[name="level[]"]').val(level);
    cloned.find(".new-li").attr("level", parseInt(level)+1);

    //Insert it in dom
    cloned.insertBefore(ul.parent().find(".new-li[level="+level+"]"));

});

/* Edit Item button pressed */
$(".edit-li").click(function(event) {
    var li = $(event.target).parent().parent();
    if (li.attr("editmode") != 1) {
        li.attr("editmode", 1);
        //Hide span
        li.children().filter(".label-caption").hide();

        //Update input values and show them
        li.children().filter('input[name="label[]"]').val(li.children().filter(".label-caption").text()).show();
        li.children().filter('input[name="url[]"]').show();
    } else {
        li.attr("editmode", 0);

        //Hide inputs
        li.children().filter('input[name="label[]"]').hide();
        li.children().filter('input[name="url[]"]').hide();

        //Update label and show it
        li.children().filter(".label-caption").text(li.children().filter('input[name="label[]"]').val()).show();
    }
});

/* Li up button pressed */
$(".up-li").click(function(event) {
    var li = $(event.target).parent().parent();
    if (li.prev().size() == 1) {
        var prev = li.prev();
        li.insertBefore(prev);
    }
});

/* Li down button pressed */
$(".down-li").click(function(event) {
    var li = $(event.target).parent().parent();
    var next = li.next();
    //WARN check that last element isn't new-li
    if (next.size() == 1 && next.attr("class") != "new-li") {
        next.insertBefore(li);
    }
});