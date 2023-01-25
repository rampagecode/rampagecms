<?php

namespace Module\Menu\Frontend;

use App\Page\Page;

class MenuView {
    function rowsWrapper( $rowsHTML ) { return <<<EOF
<span class="headerMenu">
    {$rowsHTML}
</span>
EOF;
    }

    function activeRow( $title, $url ) { return <<<EOF
<a href="{$url}" class="selected">{$title}</a>
EOF;
    }

    function inactiveRow( $title, $url ) { return <<<EOF
<a href="{$url}" >{$title}</a>
EOF;
    }

    function breadcrumbLink( Page $page ) { return <<<EOF
<a href="[~{$page->getId()}]">{$page->getTitle()}</a>
EOF;
    }
}