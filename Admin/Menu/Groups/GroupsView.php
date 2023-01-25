<?php

namespace Admin\Menu\Groups;

class GroupsView {
    /**
     * @param string $content
     * @param string $baseGroup
     * @return string
     */
    function userGroupsList( $content, $baseGroup ) { return <<<EOF
<table class="tab" cellspacing="0">
<tr>
	<td class="row0" colspan="4">Группы пользователей</td>
</tr><tr>
	<td width="1%" class="row1">&nbsp;</td>
	<td width="97%" class="row1" style="color: #3A4F6C; font-weight: bold;">Название группы</td>
	<td width="1%" class="row1" style="color: #3A4F6C; font-weight: bold;">ID&nbsp;группы</td>
	<td width="1%" class="row1" style="color: #3A4F6C; font-weight: bold;">Пользователей</td>
</tr>
{$content}
</table>
<!--
<br />                               
<form action="[mod://]" method="post">
<table class="tab" cellspacing="0">
<tr>
	<td class="row0" colspan="2">Создать новую группу</td>
</tr><tr>
	<td class="row1">На основе группы...</td>
	<td class="row2">{$baseGroup}</td>
</tr><tr>
	<td colspan="2" class="row_sub"><input type="submit" value="Создать.."></td>
</td></tr>
</table>

<input type="hidden" name="i" value="add">
</form>
-->
EOF;
    }

    function groupListRow( $editButton, $deleteButton, $title, $id, $membersCount ) { return <<<EOF
<tr>
    <td class="row1" nowrap>{$editButton}&nbsp;{$deleteButton}</td>
    <td class="row2">{$title}</td>
    <td class="row1" align="center">{$id}</td>
    <td class="row2" align="right">{$membersCount}</td>
</tr>
EOF;
    }
}