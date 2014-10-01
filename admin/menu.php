<?php
/*
 * Stampa il menu con la voce $current evidenziata
 */
function print_menu($selected) {
	
	$MENU = array(
		"Home" => "home.php",
		"Gestione Pagine" => "gestpag.php",
		"Impostazioni" => "gestsettings.php"
	);
	
	$html .= "<div class=\"pure-menu pure-menu-open\">\n";
	$html .= "<img src=\"logo.png\" style=\"border: none; width: 200px; height: 58px; margin-bottom: -5px;\">\n";
	$html .= "<ul>\n";
	
	foreach ($MENU as $key => $item) {
		if ($key == $selected)
			$html .= "<li class=\"menu-item-divided pure-menu-selected\">";
		else
			$html .= "<li>";
		$html .= "<a href=\"$item\">$key</a></li>\n";
	}
	
	$html .= "</ul>\n";
	$html .= "</div>";
	
	echo $html;
}

?>
