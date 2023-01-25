<?php

namespace Admin\Menu\Desktop;

class DesktopView {
    /**
     * @param string $rows
     * @return string
     */
    function table( $rows ) { return <<<EOF
<table width="100%" border="0" cellspacing="20" align="left">
{$rows}
</table>
EOF;
    }

    /**
     * @param string $cells
     * @return string
     */
    function row( $cells ) { return <<<EOF
<tr>{$cells}</tr>
EOF;
    }

    /**
     * @param int $n
     * @return string
     */
    function space( $n ) { return <<<EOF
<tr><td colspan="{$n}">&nbsp;</td></tr>
EOF;
    }

    /**
     * @param string $name
     * @param string $link
     * @return string
     */
    function cell( $name, $id ) { return <<<EOF
<td width="33%" nowrap>
    <a href="[env://]&s=content&a=page&x={$id}">
        <img src="[img://]shortcuts/05.png" width="64" height="64" align="left" border="0" />		
        <br /><span style="font-size:11px"><i>{$name}</i></span>		
    </a>	
</td>
EOF;
    }
}