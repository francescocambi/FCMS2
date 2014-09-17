<?php
$INITIAL['items'] = array(
		0 => new MenuItem('0', 'primo', '#', '0', FALSE, NULL),
		1 => new MenuItem('1', 'secondo','#','1',FALSE, NULL),
		2 => new MenuItem('2', 'terzo', '#', '2', FALSE, NULL)
);
$INITIAL['menu'] = new Menu('0', 'menupri', '', $INITIAL['items']);
$INITIAL['languages'] = array (
		0 => new Language('0', 'italian', 'it', 'resources/images/ita.png', $INITIAL['menu']),
		1 => new Language('1', 'english', 'en', 'resources/images/eng.png', $INITIAL['menu'])
);
$INITIAL['blocks'] = array(
		0 => new ContentBlock('0', 'contenuto', '', '<h1>IT WORKS</h1>', $INITIAL['languages']),
		1 => new ContentBlock('1', 'contenuto', '', '<h1>IT WORKS</h1>', $INITIAL['languages']),
		2 => new ContentBlock('2', 'contenuto_ext', '', '<h1 align="center">IT WORKS EXTENDED TOO</h1>', $INITIAL['languages']),
		3 => new ContentBlock('3', 'contenuto_ext', '', '<h1 align="center">IT WORKS EXTENDED TOO</h1>', $INITIAL['languages'])
);
$INITIAL['blocks'][0]->setBlockStyle(new CenteredBlockStyle());
$INITIAL['blocks'][1]->setBlockStyle(new CenteredBlockStyle());
$INITIAL['blocks'][2]->setBlockStyle(new ExtendedBlockStyle());
$INITIAL['blocks'][3]->setBlockStyle(new ExtendedBlockStyle());

$INITIAL['blocks'][4] = new ContentBlock('4', 'tabellaprova','','
<style>
.CSSTableGenerator {
	margin:0px;padding:0px;
	width:100%;
	box-shadow: 10px 10px 5px #888888;
	border:1px solid #000000;
	
	-moz-border-radius-bottomleft:8px;
	-webkit-border-bottom-left-radius:8px;
	border-bottom-left-radius:8px;
	
	-moz-border-radius-bottomright:8px;
	-webkit-border-bottom-right-radius:8px;
	border-bottom-right-radius:8px;
	
	-moz-border-radius-topright:8px;
	-webkit-border-top-right-radius:8px;
	border-top-right-radius:8px;
	
	-moz-border-radius-topleft:8px;
	-webkit-border-top-left-radius:8px;
	border-top-left-radius:8px;
}.CSSTableGenerator table{
    border-collapse: collapse;
        border-spacing: 0;
	width:100%;
	height:100%;
	margin:0px;padding:0px;
}.CSSTableGenerator tr:last-child td:last-child {
	-moz-border-radius-bottomright:8px;
	-webkit-border-bottom-right-radius:8px;
	border-bottom-right-radius:8px;
}
.CSSTableGenerator table tr:first-child td:first-child {
	-moz-border-radius-topleft:8px;
	-webkit-border-top-left-radius:8px;
	border-top-left-radius:8px;
}
.CSSTableGenerator table tr:first-child td:last-child {
	-moz-border-radius-topright:8px;
	-webkit-border-top-right-radius:8px;
	border-top-right-radius:8px;
}.CSSTableGenerator tr:last-child td:first-child{
	-moz-border-radius-bottomleft:8px;
	-webkit-border-bottom-left-radius:8px;
	border-bottom-left-radius:8px;
}.CSSTableGenerator tr:hover td{
	background-color:#ffffff;
		

}
.CSSTableGenerator td{
	vertical-align:middle;
		background:-o-linear-gradient(bottom, #ffaa56 5%, #ffffff 100%);	background:-webkit-gradient( linear, left top, left bottom, color-stop(0.05, #ffaa56), color-stop(1, #ffffff) ); 
	background:-moz-linear-gradient( center top, #ffaa56 5%, #ffffff 100% );
	filter:progid:DXImageTransform.Microsoft.gradient(startColorstr="#ffaa56", endColorstr="#ffffff");	background: -o-linear-gradient(top,#ffaa56,ffffff);

	background-color:#ffaa56;

	border:1px solid #000000;
	border-width:0px 1px 1px 0px;
	text-align:center;
	padding:9px;
	font-size:10px;
	font-family:Arial;
	font-weight:normal;
	color:#000000;
}.CSSTableGenerator tr:last-child td{
	border-width:0px 1px 0px 0px;
}.CSSTableGenerator tr td:last-child{
	border-width:0px 0px 1px 0px;
}.CSSTableGenerator tr:last-child td:last-child{
	border-width:0px 0px 0px 0px;
}
.CSSTableGenerator tr:first-child td{
		background:-o-linear-gradient(bottom, #ff7f00 5%, #bf5f00 100%);	background:-webkit-gradient( linear, left top, left bottom, color-stop(0.05, #ff7f00), color-stop(1, #bf5f00) );
	background:-moz-linear-gradient( center top, #ff7f00 5%, #bf5f00 100% );
	filter:progid:DXImageTransform.Microsoft.gradient(startColorstr="#ff7f00", endColorstr="#bf5f00");	background: -o-linear-gradient(top,#ff7f00,bf5f00);

	background-color:#ff7f00;
	border:0px solid #000000;
	text-align:center;
	border-width:0px 0px 1px 1px;
	font-size:14px;
	font-family:Arial;
	font-weight:bold;
	color:#ffffff;
}
.CSSTableGenerator tr:first-child:hover td{
	background:-o-linear-gradient(bottom, #ff7f00 5%, #bf5f00 100%);	background:-webkit-gradient( linear, left top, left bottom, color-stop(0.05, #ff7f00), color-stop(1, #bf5f00) );
	background:-moz-linear-gradient( center top, #ff7f00 5%, #bf5f00 100% );
	filter:progid:DXImageTransform.Microsoft.gradient(startColorstr="#ff7f00", endColorstr="#bf5f00");	background: -o-linear-gradient(top,#ff7f00,bf5f00);

	background-color:#ff7f00;
}
.CSSTableGenerator tr:first-child td:first-child{
	border-width:0px 0px 1px 0px;
}
.CSSTableGenerator tr:first-child td:last-child{
	border-width:0px 0px 1px 1px;
}</style>
<div class="CSSTableGenerator" >
                <table >
                    <tr>
                        <td>
                            Title 1
                        </td>
                        <td >
                            Title 2
                        </td>
                        <td>
                            Title 3
                        </td>
                    </tr>
                    <tr>
                        <td >
                            Row 1
                        </td>
                        <td>
                            Row 1
                        </td>
                        <td>
                            Row 1
                        </td>
                    </tr>
                    <tr>
                        <td >
                            Row 2
                        </td>
                        <td>
                            Row 2
                        </td>
                        <td>
                            Row 2
                        </td>
                    </tr>
                    <tr>
                        <td >
                            Row 2
                        </td>
                        <td>
                            Row 2
                        </td>
                        <td>
                            Row 2
                        </td>
                    </tr>
                    <tr>
                        <td >
                            Row 3
                        </td>
                        <td>
                            Row 3
                        </td>
                        <td>
                            Row 3
                        </td>
                    </tr>
                </table>
            </div>
            
', $INITIAL['languages']);

$INITIAL['blocks'][4]->setBlockStyle(new CenteredBlockStyle());
$INITIAL['page'] =  new Page(0, 'HomePage', 'Questa &egrave; la home page del sito', TRUE, TRUE, $INITIAL['languages'][0], $INITIAL['blocks'], NULL);
?>