<?php

namespace Core;

class CenteredBlockStyle implements BlockStyle {
	
	public function stylizeHTML($html, $localcss) {
		return "<div class=\"content\" style=\"".$localcss."\">".$html."</div>";
	}
	
}

?>
