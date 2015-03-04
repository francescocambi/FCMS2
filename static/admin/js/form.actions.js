var editor_idx = 0;
var openBlockPropertiesOwner = null;

tinymce.init({
    theme: "modern",
    image_advtab: true,
    image_class_list: [
        {title: "Null", value: ""},
        {title: "Visualizzatore Img", value: "ingrandimento"}
    ],
    plugins: [
         "advlist autolink link image lists charmap print preview hr anchor pagebreak spellchecker",
         "searchreplace wordcount visualblocks visualchars code fullscreen insertdatetime media nonbreaking",
         "save table contextmenu directionality emoticons template paste textcolor"
   ],
   toolbar: "insertfile undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image | print preview media fullpage | forecolor backcolor emoticons",
   relative_urls: false,
   file_browser_callback: RoxyFileBrowser 
});

function RoxyFileBrowser(field_name, url, type, win) {
  var roxyFileman = 'fileman/index.php';
  if (roxyFileman.indexOf("?") < 0) {     
    roxyFileman += "?type=" + type;   
  }
  else {
    roxyFileman += "&type=" + type;
  }
  roxyFileman += '&input=' + field_name + '&value=' + document.getElementById(field_name).value;
  roxyFileman += '&integration=tinymce4';
  tinyMCE.activeEditor.windowManager.open({
     file: roxyFileman,
     title: 'File Manager',
     width: 850, 
     height: 650,
     resizable: "yes",
     plugins: "media",
     inline: "yes",
     close_previous: "no"  
  }, {     window: win,     input: field_name    });
  return false; 
}

function CustomRoxyFileBrowser(field_name) {
  var roxyFileman = 'fileman/index.php';
  if (roxyFileman.indexOf("?") < 0) {     
    roxyFileman += "?input=" + field_name;   
  }
  else {
    roxyFileman += "&input=" + field_name;
  }
  roxyFileman += '&integration=custom&dialogid=roxyCustomPanel';
  console.log(roxyFileman);
  
  $('#roxyCustomPanel').children().first().attr("src",roxyFileman);
  $('#roxyCustomPanel').dialog({
  	modal:true,
  	width:875,
  	height:600,
  	title: 'File Manager'
  });
  
  return false; 
}



$(document).ready(function() {
	
	$("#newblockmodal").dialog({
		position: { my: 'center top', at: 'center top' },
		width: 600,
		title: 'Inserisci Blocco',
		autoOpen: false
	});
	
	$("#blockpropdialog").dialog({
		position: { my: 'center top', at: 'center top' },
		width: 600,
		title: 'Proprietà Blocco',
		autoOpen: false,
		close: function() {
			openBlockPropertiesOwner=null;
		}
	});

});

//Azione pulsante "Nuovo Url"
$("#newurl").click(function () {
	$("#urlrowtpl").clone(true).insertBefore($("#newurlrow")).attr("id","").show().addClass("urlitem");
});

//Azione pulsante Modifica Url
$(".modurl").click(function (event) {
	var datafield = $(event.target).parent().prev();
	var datainput = $(event.target).parent().next();
	if (datafield.attr("mode") == 1) {
		//In corso di modifica
		datafield.attr("mode", 0);
		var url = datafield.children().get(0).value;
		datafield.children().remove();
		datafield.text(url);
		datainput.val(url);
		$(event.target).parent().children().css('color', 'black');
		
	} else {
		//Vuole avviare modifica
		datafield.attr("mode", 1);
		var url = datafield.text();
		datafield.text(""); //toglie l'url
		$("<input type=\"text\" value=\""+url+"\">").appendTo(datafield); //mette casella testo con url
		$(event.target).parent().children().css('color', '#0078e7'); //button diventa primary
	}
	
});

//Azione pulsante elimina url
$(".delurl").click(function (event) {
	$(event.target).parent().parent().remove();
});

//Azione pulsante rimuovi blocco
$(".delblock").click(function delblock(event) {
	var id  = $(event.target).parent().siblings().filter('textarea').attr('id');
	tinymce.execCommand('mceRemoveEditor', false, id);
	$(event.target).parent().parent().remove();
});

//Azione pulsante nuovo blocco
$("#newblock").click(function () {
	//Apertura dialog apposita
	$("#newblockmodal").dialog("open");
    var nbmname = $("#newblockmodal").find("#nbm-name");
    nbmname.css("border", "solid 1px #ccc");
});

//Funzione aggiunta blocco
//Il paramentro indica se sul nuovo blocco deve essere
//attivato tinymce oppure no
function newblock(editor_ready) {
	var cloned = $('#editormodel').clone(true).appendTo("#blocks").attr("id","");
	cloned.show();
	var textarea = cloned.children().filter(".blockcontent");
	textarea.attr('id','tmceeditor'+editor_idx++);
	if (editor_ready) {
		textarea.show();
		tinymce.execCommand('mceAddEditor', false, textarea.attr("id"));
	} else {
		var contentdiv = $('<div class="blockcontentdiv"></div>');
		cloned.children().first().after(contentdiv);
	} 
	
	return cloned;
}

//Azione pulsante sposta blocco su
$(".upbutton").click(function blockup() {
	
	var toMove = $(event.target).parent().parent();
	var previous = toMove.prev();
	
	//If previous is null do nothing
	if (previous.size() != 1) return;
	
	var contentdiv = toMove.children().filter(".blockcontentdiv");
	if (contentdiv.size() == 0) {
		var toMoveTxtId = toMove.children().filter("textarea").attr('id');
		tinyMCE.execCommand('mceRemoveEditor', false, toMoveTxtId);
	}
		
	var parent = toMove.parent().get(0);
	parent.insertBefore(toMove.get(0), previous.get(0));
	
	if (contentdiv.size() == 0) {	
		tinyMCE.execCommand('mceAddEditor', false, toMoveTxtId);
	}

});

//Azione pulsante sposta blocco giu
$(".downbutton").click(function blockdown() {
	var toMove = $(event.target).parent().parent();
	var next = toMove.next();
	
	//If previous is null do nothing
	if (next.size() != 1) return;
	
	var contentdiv = next.children().filter(".contentdiv");
	
	if (contentdiv.size() == 0) {
		var nextTxtId = next.children().filter('textarea').attr('id');
		tinyMCE.execCommand('mceRemoveEditor', false, nextTxtId);
	}
	
	var parent = toMove.parent().get(0);
	parent.insertBefore(next.get(0), toMove.get(0));
	
	if (contentdiv.size() == 0) {
		tinyMCE.execCommand('mceAddEditor', false, nextTxtId);
	}

});

//Azioni pulsanti nella dialog di aggiunta blocco per cambiare schermata (nuovo-esistente)
function nbmtabcontrol(target) {
	var name = $(target).attr("id");
	if (name == "nbm-new") {
		$("#nbm-insertblockscreen").hide();
		$("#nbm-newblockscreen").show();
		$("#nbm-exist").removeClass("pure-button-primary");
		$("#nbm-new").addClass("pure-button-primary");
	} else if (name == "nbm-exist") {
		$("#nbm-newblockscreen").hide();
		$("#nbm-insertblockscreen").show();
		$("#nbm-new").removeClass("pure-button-primary");
		$("#nbm-exist").addClass("pure-button-primary");
	}
}

//Azione pulsante aggiunta nuovo blocco
$("#nbm-addnew").click(function() {
    //Check if blockname is valid
    var namefield = $("#nbm-name");
    checkBlockNameUnique(0, namefield[0], function (nameisvalid) {

        if (!nameisvalid) {
            namefield[0].focus();
            return;
        }

        var cloned = newblock(true);
        cloned.find(".modblock").hide();
        cloned.children().first().children().filter("input").each(function(index, element) {
            if ($(element).attr("name") == "block[name][]") {
                $(element).val($("#nbm-name").val());
            } else if ($(element).attr("name") == "block[description][]") {
                $(element).val($("#nbm-description").val());
            } else if ($(element).attr("name") == "block[style][]") {
                $(element).val($("#nbm-style").val());
            }
        });
        $("#nbm-newblockform")[0].reset();
        $("#newblockmodal").dialog("close");
    });
});


//Azione pulsante aggiunta blocco esistente
$("#nbm-addexist").click(function() {
    $(".nbm-blockcheck:checked").each(function (index, element) {
        var cloned = newblock(false);
        cloned.find(".applyblock").hide();
        var blockid = $(element).val();
        cloned.children().first().children().filter('[name="block[id][]"]').val(blockid);
        $.getJSON('/admin/blocks/'+blockid, function(data) {
            cloned.find('[name="block[id][]"]').val(data.ID);
            cloned.find('[name="block[name][]"]').val(data.NAME);
            cloned.find('[name="block[description][]"]').val(data.DESCRIPTION);
            cloned.find(".blockcontentdiv").html(data.CONTENT);
            cloned.find('[name="block[style][]"]').val(data.BLOCK_STYLE_ID);
            cloned.find('[name="block[bckurl][]"]').val(data.BG_URL);
            cloned.find('[name="block[bckred][]"]').val(data.BG_RED);
            cloned.find('[name="block[bckgreen][]"]').val(data.BG_GREEN);
            cloned.find('[name="block[bckblue][]"]').val(data.BG_BLUE);
            cloned.find('[name="block[bckopacity][]"]').val(data.BG_OPACITY);
            cloned.find('[name="block[bckrepeatx][]"]').val(data.BG_REPEATX);
            cloned.find('[name="block[bckrepeaty][]"]').val(data.BG_REPEATY);
            cloned.find('[name="block[bcksize][]"]').val(data.BG_SIZE);
        });

    });
    $("#nbm-insertblockform")[0].reset();
    $("#newblockmodal").dialog("close");
});
//Handler errori ajax
$(document).ajaxError(function (event, jqxhr, settings, thrownError) {
	//var cloned = $("#error-dialog").clone(true);
	var cloned = $("#error-dialog");
	cloned.find("p").remove();
	cloned.find("textarea").remove();
	cloned.children().first().before($("<p>Errore: "+thrownError+"</p>"));
	$(cloned).dialog("open");
});
//Azione pulsante modifica blocco
$(".modblock").click(function(event) {
	var block = $(event.target).parent().parent();
	var contentdiv = $(event.target).parent().siblings().filter(".blockcontentdiv");
	var textarea = block.children().filter(".blockcontent");
	textarea.val(contentdiv.html());
	contentdiv.remove();
	textarea.show();
	tinyMCE.execCommand('mceAddEditor', false, textarea.attr("id"));
	//Nasconde pulsanti
	block.children().filter(".blockbuttons").children().filter(".modblock").hide();
	block.children().filter(".blockbuttons").children().filter(".applyblock").show();
});

//Azione pulsante salva blocco
$(".applyblock").click(function(event) {
	var block = $(event.target).parent().parent();
	var contentinput = $(event.target).parent().siblings().filter(".blockcontent");
	tinyMCE.execCommand('mceRemoveEditor', false, contentinput.attr("id"));
	var contentdiv = $('<div class="blockcontentdiv"></div>');
	contentdiv.html(contentinput.val());
	contentinput.hide().after(contentdiv);
	//Nasconde pulsanti
	block.children().filter(".blockbuttons").children().filter(".modblock").show();
	block.children().filter(".blockbuttons").children().filter(".applyblock").hide();
});

//Azione pulsante apertura file manager sul server
$('.openfileman').click(function(event) {
	CustomRoxyFileBrowser($(event.target).prev().attr('id'));
});

//Azione di apertura dialog proprietà blocco
$(".blockproperties").click(function(event) {
	if (openBlockPropertiesOwner != null) return;
	//Valorizza variabile di stato openBlockPropertiesOwner
	//con un "puntatore" jquery al blocco del quale si vuole
	//aprire la dialog delle proprietà
	openBlockPropertiesOwner = $(event.target).parent().parent();
	//Precompila form con dati blocco
	$("#bpd-form")[0].reset();
    $("#bpd-blockname").css("border", "solid 1px #ccc");
	var assoc = [
		["#bpd-blockname", "block[name][]"],
		["#bpd-blockdescription", "block[description][]"],
		["#bpd-blockstyle", "block[style][]"],
		["#bpd-txtbckurl", "block[bckurl][]"],
		["#bpd-txtbckred", "block[bckred][]"],
		["#bpd-txtbckgreen", "block[bckgreen][]"],
		["#bpd-txtbckblue", "block[bckblue][]"],
		["#bpd-txtbckopacity", "block[bckopacity][]"] ];
	for (var i=0; i<assoc.length; i++) {
		$(assoc[i][0]).val(openBlockPropertiesOwner.find('[name="'+assoc[i][1]+'"]').val());
	}
    //Imposta validità block name
    $("#bpm-blockname").attr("valid", "true");
	//Imposta checkbox repeatx repeaty
	if (openBlockPropertiesOwner.find('[name="block[bckrepeatx][]"]').val() == 1)
		$("#bpd-chkbckrepeatx").prop("checked", "true");
	if (openBlockPropertiesOwner.find('[name="block[bckrepeaty][]"]').val() == 1)
		$("#bpd-chkbckrepeaty").prop("checked", "true");
	//Imposta radio button bcksize
	var radiovalue = openBlockPropertiesOwner.find('[name="block[bcksize][]"]').val();
	if (radiovalue != "" && radiovalue == "cover") {
		$("#bpd-bcksizecover").attr('checked', 'true');
	} else if (radiovalue != "" && radiovalue == "contain") {
		$("#bpd-bcksizecontain").attr('checked', 'true');
	}
	//Apertura dialog
	$('#blockpropdialog').dialog('open');
});

//Azione pulsante salvataggio proprietà blocco
$("#bpd-btnsave").click(function(event) {
	if (openBlockPropertiesOwner == null) return;

    //Check if blockname is valid
    var blockid = openBlockPropertiesOwner.find('input[name="block[id][]"]').val();
    var validblockname = checkBlockNameUnique(blockid, $("#bpd-blockname")[0], function (validblockname) {

        if (!validblockname) {
            $("#bpd-blockname")[0].focus();
            return;
        }

        //Scarica dati del form nella dialog nei campi hidden del relativo blocco
        var assoc = [
            ["#bpd-blockname", "block[name][]"],
            ["#bpd-blockdescription", "block[description][]"],
            ["#bpd-blockstyle", "block[style][]"],
            ["#bpd-txtbckurl", "block[bckurl][]"],
            ["#bpd-txtbckred", "block[bckred][]"],
            ["#bpd-txtbckgreen", "block[bckgreen][]"],
            ["#bpd-txtbckblue", "block[bckblue][]"],
            ["#bpd-txtbckopacity", "block[bckopacity][]"] ];
        for (var i=0; i<assoc.length; i++) {
            openBlockPropertiesOwner.find('[name="'+assoc[i][1]+'"]')[0].value = $(assoc[i][0]).val();
        }
        //Scarica dati checkbox repeatx repeaty
        var repeatx = $("#bpd-chkbckrepeatx:checked").size();
        var repeaty = $("#bpd-chkbckrepeaty:checked").size();
        openBlockPropertiesOwner.find('[name="block[bckrepeatx][]"]')[0].value = repeatx;
        openBlockPropertiesOwner.find('[name="block[bckrepeaty][]"]')[0].value = repeaty;
        //Scarica dati radio button background size
        var bcksize = $(event.target).parent().find('input[name="bpd-bcksize"]:checked').val();
        openBlockPropertiesOwner.find('[name="block[bcksize][]"]')[0].value = bcksize;
        //Resetta form e variabile di stato openBlockPropertiesOwner
        $("#bpd-form")[0].reset();
        openBlockPropertiesOwner = null;
        //Chiude la dialog
        $("#blockpropdialog").dialog("close");
    });
});

/* Checks unique constraint on block name */
function checkBlockNameUnique(id, target, callback) {
    target.value = target.value.trim();
    var txtvalue = target.value;
    if (txtvalue == "") {
        $(target).css("border", "solid 2px rgb(202, 60, 60)");
        return false;
    }
    $.getJSON("/admin/blocks/checkBlockName?name="+txtvalue+"&blockid="+id, function(data) {
        if (data.status == "error") {
            //Show error dialog
            $("#erd-errdata").val(data.errormessage);
            $("#error-dialog").dialog("open");
            return false;
        }
        if (data.status == "ok") {
            if (data.result == "true") {
                $(target).css("border", "solid 2px rgb(28, 184, 65)");
                callback(true);
            } else {
                $(target).css("border", "solid 2px rgb(202, 60, 60)");
                displayErrorDialog("Errore", "Impossibile usare lo stesso nome per pi&ugrave; blocchi. Usarne un altro.");
                callback(false);
            }
        }
    });

}

/* Controllo vincolo unicità su nome pagina */
function checkPageNameUnique(id, target, callback) {
    target.value = target.value.trim();
    var txtvalue = target.value;
    if (txtvalue == "") {
        $(target).css("border", "solid 2px rgb(202, 60, 60)");
        return false;
    }
    $.getJSON("/admin/pages/checkPageName?name="+txtvalue+"&pageid="+id, function(data) {
        if (data.status == "error") {
            //Show error dialog
            $("#erd-errdata").val(data.errormessage);
            $("#error-dialog").dialog("open");
            return false;
        }
        if (data.status == "ok") {
            if (data.result) {
                $(target).css("border", "solid 2px rgb(28, 184, 65)");
                callback(true);
            } else {
                $(target).css("border", "solid 2px rgb(202, 60, 60)");
                displayErrorDialog("Errore", "Impossibile usare lo stesso nome per pi&ugrave; pagine. Usarne un altro.");
                callback(false);
            }
        }
    });

}

/**
 * Generates and open a new jquery ui dialog with title and message
 * passed as arguments to display errors for the user
 * @param title
 * @param message
 */
function displayErrorDialog(title, message) {
    var html = '<div class="message-error-dialog"><p>'+message+'</p>' +
        '<button type="button" class="pure-button pure-button-primary red-button" style="float: right;">Ok</button>' +
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

/* Submit form action */
$('#save-all').click(function() {
    var namefield = $('input[name="name"]');
    checkPageNameUnique($("#id").val(), namefield[0], function (result) {
        if (!result) {
            namefield[0].focus();
            return;
        }

        //Submit form
        $("#page-editor-form").submit();
    });
});