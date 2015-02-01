<?php
//require_once('php/InitialLoader.php');
error_reporting(E_ERROR);
require_once("bootstrap.php");
$em = initializeEntityManager("./");
require_once("admin/checkSessionRedirect.php");

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
        <link rel="stylesheet" type="text/css" href="resources/lightbox/css/lightbox.css">

        <link rel="stylesheet" type="text/css" href="resources/slick/slick.css"/>

        <script type="text/javascript" src="resources/js/jquery-1.11.2.min.js"></script>

        <script type="text/javascript" src="resources/lightbox/js/lightbox.min.js"></script>

        <script type="text/javascript" src="//code.jquery.com/jquery-migrate-1.2.1.min.js"></script>
        <script type="text/javascript" src="resources/slick/slick.min.js"></script>

        <!-- blueimp gallery -->
        <link rel="stylesheet" href="resources/gallery/css/blueimp-gallery.min.css">


        <!-- end -->

        <style>
            .slick-prev {
                left: 7px;
            }
            .slick-next {
                right: 7px;
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

        <div id="menu-wrap">
		<div id="menu">
			<?php
                $menuBuilder = new ListMenuBuilder();
                $menuBuilder->generateFor($request->getLanguage()->getMenu());
                echo $menuBuilder->getHTML();
			?>
		</div>
        </div>
		
		<?php
            foreach ($page->getPageBlocks()->toArray() as $pageBlock) {
                echo $pageBlock->getBlock()->getHTML(null);
            }
		?>


        <!-- The Gallery as lightbox dialog, should be a child element of the document body -->
        <div id="blueimp-gallery" class="blueimp-gallery blueimp-gallery-controls">
            <div class="slides"></div>
            <h3 class="title"></h3>
            <a class="prev">&lsaquo;</a>
            <a class="next">&rsaquo;</a>
            <a class="close">&Chi;</a>
            <ol class="indicator"></ol>
        </div>



        <script type="text/javascript">
            $(".ingrandimento").each(function (index, item) {
                $(item).wrap('<a href="' + $(item).attr("src") + '" data-lightbox="gallery' + index + '"></a>');
            });

            $(document).ready(function(){
                $('.carousel').slick({
                    autoplay: true,
                    autoplaySpeed: 2000,
                    dots: true,
                    speed: 500
                });
            });
        </script>


        <script src="resources/gallery/js/jquery.blueimp-gallery.min.js"></script>
        <script>
//            document.getElementById('links').onclick = function (event) {
//                event = event || window.event;
//                var target = event.target || event.srcElement,
//                    link = target.src ? target.parentNode : target,
//                    options = {index: link, event: event},
//                    links = this.getElementsByTagName('a');
//                blueimp.Gallery(links, options);
//            };

        </script>

	</body>

</html>
