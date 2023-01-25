<?php

namespace Admin;

class TabMenuView {
    function main( $tabs, $menu ) { return <<<EOF
{$tabs}
{$menu}
EOF;
    }

    function space() { return <<<EOF
<div class="tabnone">&nbsp;</div>
EOF;
    }

    function tabs( $tabs ) { return <<<EOF
<div style="position: absolute; left: 11px; top: 54px;">	
	{$tabs}
</div>
EOF;
    }

    function tab( $class, $icon, $link, $title ) { return <<<EOF
<div class="{$class}">
    <img src="{$icon}" style="vertical-align: middle" />&nbsp;<a href="{$link}">{$title}</a>
</div>
EOF;
    }

    function menu() { return <<<EOF
<div style="position: absolute; left: 10px; top: 60px;" id="ext_main_menu">
	<div id="mm_dd">
		Навигационное меню
	</div>
</div>

<div id="ext_dd_tree"></div>
EOF;
    }

}