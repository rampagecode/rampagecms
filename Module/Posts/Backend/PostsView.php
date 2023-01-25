<?php

namespace Module\Posts\Backend;

class PostsView {
    function postsTable( $rows, $pagelinks, $topButton = '' ) { return <<<EOF
<table class="tab" cellspacing="0">
<tr>
	<td class="row0" colspan="2">Список постов</td>
	<td class="row0" align="right" style="padding-bottom: 2px;">{$topButton} &nbsp;</td>
</tr>
{$rows}
<tr>
    <td colspan="3" style="background-color: #D1DCEB; color: #3A4F6C; padding: 5px; margin-top: 1px; font-size: 10px;" align="right">{$pagelinks}</td>    
</tr>
</table>
EOF;
    }

    function postsTableRow( $actions, $date, $title ) { return <<<EOF
<tr>
	<td class="row1" nowrap>{$actions}</td>
	<td class="row2" style="font-size: 9px;" nowrap>{$date}</td>
	<td class="row2" width="99%">{$title}</td>
</tr>
EOF;
    }
}