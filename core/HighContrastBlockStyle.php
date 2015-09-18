<?php
/**
 * User: Francesco
 * Date: 18/09/15
 * Time: 17:12
 */

namespace Core;

class HighContrastBlockStyle implements BlockStyle {

    public function stylizeHTML($html, $localcss) {
        return "<div class=\"high_contrast\" style=\"".$localcss."\">".$html."</div>";
    }

} 