<?php

class ExtendedBlockStyle implements BlockStyle {
	
	public function stylizeHTML($html, $localcss) {
		return "<div class=\"extended_wrapper\" style=\"".$localcss."\"><div class=\"extended_content\">".$html."</div></div>";
	}
	
}

?>