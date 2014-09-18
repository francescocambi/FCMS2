<?php
//require_once('php/InitialLoader.php');
error_reporting(E_ERROR);
require_once("vendor/autoload.php");
$pagedao = new dao\PageDao();
// Rileva la pagina da visualizzare analizzando la querystring nell'url
if (isset($_GET['id'])) {
	$page = $pagedao->getById($_GET['id']);
// } else if (isset($_GET['name'])) {
	// $page = $pagedao->getByName($_GET['name']);
} else if (isset($_GET['url']) && $_GET['url'] != "") {
	$page = $pagedao->getByURL($_GET['url']);
}
if ($page == null) {
	$page = $pagedao->getByName("home");
}
// Se la pagina non è pubblica deve rilevare l'utente connesso
// e controllare se l'utente ha i permessi per visualizzare la pagina
if (!$page->isPublic()) {
	session_start();
	// $user = usa $_SESSION['userid'] per tirare fuori un oggetto User 
	if ($_SESSION['userid'] == 0 || $page->canBeViewedBy($user))
		$page = $pagedao->getByName("home");
}
$languagedao = new dao\LanguageDao();
if (isset($_GET['lang']))
	$language = $languagedao->getByCode($_GET['lang']);
else
	$language=$languagedao->getByCode('it');
?>
<!DOCTYPE HTML>
<html>
	
	<head>
		<title><?php echo $page->getTitle(); ?></title>
		<link rel="stylesheet" type="text/css" href="http://yui.yahooapis.com/pure/0.5.0/pure-min.css">
		<link rel="stylesheet" type="text/css" href="http://yui.yahooapis.com/pure/0.5.0/grids-responsive-min.css">
		<link rel="stylesheet" type="text/css" href="resources/css/stile.css">
	</head>

		<?php
		//Provvisorio FIXME
		$sql = "SELECT SET_VALUE FROM SETTINGS WHERE SET_KEY='BODY_BG'";
		$stat = Connection::getPDO()->prepare($sql);
		$stat->execute();
		$row = $stat->fetch();
		$cssstring = "";
		if ($row['SET_VALUE'] != "")
			$cssstring = "background: url('".$row['SET_VALUE']."');"; 
		?>
		
	<body style="<?php echo $cssstring; ?>">
		
		<div id="head_wrapper"><div id="head">
			<div id="lang">
				<?php
					$languageBar = new FlagLanguageBar();
					echo $languageBar->getHTML($languagedao->getAll());
				?>
			</div>
		</div></div>
		
		<div id="menu">
			<?php 
			$menuBuilder = new ListMenuBuilder();
			$menuBuilder->generateFor($language->getMenu());
			echo $menuBuilder->getHTML();
			?>
		</div>
		
		<?php
		foreach($page->getBlocks() as $block)
			echo $block->getHTML($language);
		?>
		
	</body>

</html>
