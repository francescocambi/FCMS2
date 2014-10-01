<?php
//require_once('php/InitialLoader.php');
error_reporting(E_ERROR);
require_once("bootstrap.php");
$em = initializeEntityManager("./");

$requestmanager = new GETRequestManager($em);
$request = $requestmanager->getRequest($_GET, null);

$page = $request->getPage();
$language = $request->getLanguage();
if (is_null($page) || is_null($language)) exit();
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
        $bodyBg = $em->getRepository('\Model\Setting')->findOneBy(array( "settingKey" => "BODY_BG"))->getSettingValue();
		$cssstring = "";
		if ($bodyBg != "")
			$cssstring = "background: url('".$bodyBg."');";
		?>
		
	<body style="<?php echo $cssstring; ?>">
		
		<div id="head_wrapper"><div id="head">
			<div id="lang">
				<?php
                    $languages = $em->getRepository('\Model\Language')->findAll();
					$languageBar = new FlagLanguageBar($languages, $_SERVER['REQUEST_URI']);
					echo $languageBar->getHTML();
				?>
			</div>
		</div></div>
		
		<div id="menu">
			<?php 
                $menuBuilder = new ListMenuBuilder();
                $menuBuilder->generateFor($request->getLanguage()->getMenu());
                echo $menuBuilder->getHTML();
			?>
		</div>
		
		<?php
            foreach ($page->getPageBlocks()->toArray() as $pageBlock) {
                echo $pageBlock->getBlock()->getHTML(null);
            }
		?>
		
	</body>

</html>
