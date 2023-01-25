<?php

namespace Admin\Menu\ModuleSection;

use App\Module\ModuleInfo;

class ModuleView {
    function modulesList( $title, $rows, $cols ) { return <<<EOF
<table class="tab" cellspacing="0">
<tr>
	<td class="row0" colspan="{$cols}">{$title}</td>
</tr>
{$rows}
</table>
EOF;
    }

    function availableRow( $title, $control ) { return <<<EOF
<tr>	
	<td class="row2" width="50%">{$title}</td>
	<td class="row2" nowrap width="50%" align="right">{$control}</td>
</tr>
EOF;
    }

    function modulesListRow( $title, $status, $toggle, $control ) { return <<<EOF
<tr>	
	<td class="row2" width="50%">{$title}</td>
	<td class="row2" nowrap width="10%">{$status}</td>
	<td class="row2" nowrap width="10%">{$toggle}</td>
	<td class="row2" nowrap width="20%" align="right">{$control}</td>
</tr>
EOF;
    }

    function moduleName( ModuleInfo $info ) { return <<<EOF
<b>{$info->title}</b>
<span style="color: grey;">(<i>{$info->name}</i>)</span>
<div style="color: grey;">{$info->description}</div>
EOF;
    }
}