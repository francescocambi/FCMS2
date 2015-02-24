<?php

function deleteConfirmDialog() {
	?>
	<div id="delete-confirm-dialog" style="display: none;">
		<p>Confermi l'eliminazione di <b id="dcd-objname"></b>?</p><br>
		<button type="button" class="pure-button pure-button-primary red-button" id="dcd-ok" style="float: left;">Conferma</button>
		<button type="button" class="pure-button pure-button-primary" id="dcd-cancel" style="float: right;">Annulla</button>
		<input type="hidden" id="dcd-objid">
	</div>
	<script>
	$("#delete-confirm-dialog").dialog({
		autoOpen: false,
		title: "Conferma Cancellazione",
		modal: true,
		close: function() {
			$("#dcd-objname").text("");
			$("#dcd-objid").val("");
		}
	});
	$("#dcd-cancel").click(function() {
		$("#delete-confirm-dialog").dialog("close");
	});
	</script>
	<?php
}

function actionConfirmationDialog() {
    ?>
    <div id="action-confirmation-dialog" style="display: none;">
        <p id="acd-message"></p>
        <button type="button" class="pure-button pure-button-primary green-button" id="acd-ok" style="float: right;">Conferma</button>
        <button type="button" class="pure-button pure-button-primary" id="acd-cancel" style="float: left;">Annulla</button>
        <input type="hidden" id="acd-objid">
    </div>
    <script>
        $("#action-confirmation-dialog").dialog({
            autoOpen: false,
            title: "Richiesta Conferma",
            modal: true,
            close: function() {
                $("#acd-message").text("");
                $("#acd-objid").val("");
            }
        });
        $("#acd-cancel").click(function() {
            $("#action-confirmation-dialog").dialog("close");
        });
    </script>
    <?php
}

function showSuccessfulOperationDialog() {
	?>
	<div id="successful-op-dialog" style="display: none;">
		<p>Operazione Completata!</p><br>
		<button type="button" class="pure-button pure-button-primary green-button" id="sod-ok" style="float: right;">Ok</button>
	</div>
	<script>
		$("#successful-op-dialog").dialog({
			title: "Operazione Completata",
			modal: true
		});
		$("#sod-ok").click(function() {
			$("#successful-op-dialog").dialog("close");
		});
	</script>
	<?php
}

function successfulOperationDialog() {
	?>
	<div id="successful-op-dialog" style="display: none;">
		<p>Operazione Completata!</p><br>
		<button type="button" class="pure-button pure-button-primary green-button" id="sod-ok" style="float: right;">Ok</button>
	</div>
	<script>
		$("#successful-op-dialog").dialog({
			title: "Operazione Completata",
			modal: true,
			autoOpen: false
		});
		$("#sod-ok").click(function() {
			$("#successful-op-dialog").dialog("close");
		});
	</script>
	<?php
}

function errorDialog() {
	?>
	<div id="error-dialog" style="display: none;">
		<p>Si &egrave; verificato un errore, le seguenti informazioni possono essere utili all'amministratore del sistema.</p>
		<p>Per riprendere il lavoro si consiglia di chiudere la pagina e tornare nell'area di amministrazione.</p>
		<textarea id ="erd-errdata" disabled>
		</textarea>
		<button type="button" class="pure-button pure-button-primary red-button" id="erd-ok" style="float: right;">Ok</button>
	</div>
	<script>
		$("#error-dialog").dialog({
			title: "Errore",
			modal: true,
			autoOpen: false
		});
		$("#erd-ok").click(function() {
			$("#error-dialog").dialog("close");
		});
	</script>
	<?php
}

function showErrorDialog($message, $stacktrace) {
	?>
	<div id="error-dialog" style="display: none;">
		<p>Si &egrave; verificato un errore, le seguenti informazioni possono essere utili all'amministratore del sistema.</p>
		<p>Per riprendere il lavoro si consiglia di chiudere la pagina e tornare nell'area di amministrazione.</p>
		<textarea disabled>
			ERROR => <?php echo $message; ?>
			TRACE => <?php echo $stacktrace; ?>
		</textarea>
		<button type="button" class="pure-button pure-button-primary red-button" id="erd-ok" style="float: right;">Ok</button>
	</div>
	<script>
		$("#error-dialog").dialog({
			title: "Errore",
			modal: true
		});
		$("#erd-ok").click(function() {
			$("#error-dialog").dialog("close");
		});
	</script>
	<?php
}
?>