<?php

namespace Admin\Menu\Content;

class ContentView {
    /**
     * @param $content
     * @param $title
     * @param $id
     * @return string
     */
    function moduleBox( $content, $title, $id ) { return <<<EOF
<div id="{$id}_show" style="text-align: left; border: 1px solid rgb(114, 152, 212); background: #F5F9FD; padding: 4px; cursor: pointer; color: #222;" onclick="toggleViews('{$id}_show', '{$id}_hide', '{$id}_content')">
	{$title} 
	<img src="[img://]buttons/down.png" width="20" height="20" align="absmiddle">
	<span style="color: #666; font-weight: normal;">(скрыть)</span>
</div>

<div id="{$id}_hide" style="text-align: left; font-weight: bold; border: 1px solid rgb(114, 152, 212); background: #F5F9FD; padding: 4px; cursor: pointer; color: #222; display:none;" onclick="toggleViews('{$id}_show', '{$id}_hide', '{$id}_content')">
	{$title} 
	<img src="[img://]buttons/up.png" width="20" height="20" align="absmiddle">
	<span style="color: #666; font-weight: normal;">(показать)</span>
</div>
<div id="{$id}_content">
{$content}
</div>
EOF;
    }

    /**
     * @return string
     */
    function spacer() { return <<<EOF
<div style="height:10px;"></div>
EOF;
    }

    /**
     * @param string $name
     * @param string $link
     * @return string
     */
    function cell($name, $link, $icon ) { return <<<EOF
<td width="33%" nowrap>
    <a href="{$link}">
        <img src="{$icon}" width="64" height="64" align="left" border="0" />		
        <br /><span style="font-size:11px"><i>{$name}</i></span>		
    </a>	
</td>
EOF;
    }
}