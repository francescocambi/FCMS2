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
		<title><?php
            $title = $page->getTitle()." - ";
            $title .= $em->getRepository('\Model\Setting')->findOneBy(array( "settingKey" => "TITLE_DESC"))->getSettingValue();
            echo $title;
            ?></title>
        <meta name="description" content="<?php echo $page->getDescription(); ?>">
		<link rel="stylesheet" type="text/css" href="resources/css/stile.css">
        <link rel="stylesheet" type="text/css" href="admin/css/tables-min.css">
        <script type="text/javascript" src="resources/js/jquery-1.4.3.min.js"></script>
        <script type="text/javascript" src="resources/fancybox/jquery.fancybox-1.3.4.pack.js"></script>
        <script type="text/javascript">
            $(function($){
                var addToAll = false;
                var gallery = true;
                var titlePosition = 'inside';
                $(addToAll ? 'img' : 'img.ingrandimento').each(function(){
                    var $this = $(this);
                    var title = $this.attr('title');
                    var src = $this.attr('data-big') || $this.attr('src');
                    var a = $('<a href="#" class="fancybox"></a>').attr('href', src).attr('title', title);
                    $this.wrap(a);
                });
                if (gallery)
                    $('a.fancybox').attr('rel', 'fancyboxgallery');
                $('a.fancybox').fancybox({
                    titlePosition: titlePosition
                });
            });
            $.noConflict();
        </script>
        <link rel="stylesheet" type="text/css" media="screen" href="resources/fancybox/jquery.fancybox-1.3.4.css">
        <style type="text/css">
            a.fancybox img {
                border: none;
                box-shadow: 0 1px 7px rgba(0,0,0,0.6);
                -o-transform: scale(1,1); -ms-transform: scale(1,1); -moz-transform: scale(1,1); -webkit-transform: scale(1,1); transform: scale(1,1); -o-transition: all 0.2s ease-in-out; -ms-transition: all 0.2s ease-in-out; -moz-transition: all 0.2s ease-in-out; -webkit-transition: all 0.2s ease-in-out; transition: all 0.2s ease-in-out;
            }
            a.fancybox:hover img {
                position: relative; z-index: 999; -o-transform: scale(1.03,1.03); -ms-transform: scale(1.03,1.03); -moz-transform: scale(1.03,1.03); -webkit-transform: scale(1.03,1.03); transform: scale(1.03,1.03);
            }
        </style>
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
