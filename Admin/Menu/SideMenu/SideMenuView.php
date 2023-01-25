<?php

namespace Admin\Menu\SideMenu;

class SideMenuView {
    function header( $title ) { return <<<EOF
<table class="tab" cellspacing="0" style="width: 228px; border-bottom: none;">
	<tr><td class="row0">{$title}</td></tr>
</table>
EOF;
    }

    function body( $content ) { return <<<EOF
<div class="tableborder">
    {$content}
</div>
EOF;
    }

    function row( $url, $name, $icon ) { return <<<EOF
<div class="menulinkwrap">
	<img src="{$icon}" border="0" alt="" valign="absmiddle" />
	<a href="{$url}"><i>{$name}</i></a>
</div>
EOF;
    }

    function spacer() { return <<<EOF
<div style="height:10px;"></div>
EOF;
    }
}