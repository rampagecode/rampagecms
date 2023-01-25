<?php

namespace Admin\Menu\Members;

class MembersView {

    /**
     * Форма поиска
     * @param string[] $v
     * @return string
     */
    function searchForm( $v ) { return <<<EOF
<table class="tab" cellspacing="0">
<tr><td class="row0" colspan="2">Поиск пользователей</td></tr>
<tr>
    <td width="50%" class="row1"><b>Логин</b><div style="color:gray">Системное имя пользователя для авторизации</div></td>
    <td width="50%" class="row2">
        <select name='loginwhere'  class='dropdown' style="vertical-align:middle">
            <option value='begin'>Начинается на</option>
            <option value='is'>=</option>
            <option value='contains'>Содержит</option>
            <option value='ends'>Кончается на</option>
        </select>
        <input type="text" name="login" value="{$v['login']}" class="textinput" style="width:auto">
    </td>
</tr><tr>
    <td class="row1"><b>Никнейм</b><div style="color:gray">Имя пользователя, которое отображается на экране</div></td>
    <td class="row2">
        <select name='dnamewhere'  class='dropdown' style="vertical-align:middle">
            <option value='begin'>Начинается на</option>
            <option value='is'>=</option>
            <option value='contains'>Содержит</option>
            <option value='ends'>Кончается</option>
        </select>
        <input type="text" name="dname" value="{$v['dname']}" maxlength="25" class="textinput" style="width:auto">
    </td>	
</tr><tr>
    <td width="50%" class="row1"><b>E-mail содержит..</b><div style="color:gray">Можно ввести какую-либо часть, например "<b>@hotmail</b>"</div></td>
    <td width="50%" class="row2"><input type="text" name="email" value="{$v['email']}" class="textinput"></td>
</tr><tr>
    <td class="row1">
        <b>Зарегистрирован между.. (мм-дд-гггг)</b>
        <div style="color:gray">Оставьте первый бокс пустым, чтобы диапазон отсчитывался с начала. Оставьте последний бокс пустым, чтобы диапазон заканчивался текущей датой</div>
    </td>
    <td class="row2">
        <input type="text" name="registered_first" value="{$v['registered_first']}" class="textinput" style="width:80px">
        до
        <input type="text" name="registered_last" value="{$v['registered_last']}" class="textinput" style="width:80px">
    </td>
</tr><tr>
    <td class="row1">
        <b>Последняя активность между.. (мм-дд-гггг)</b>
        <div style="color:gray">Оставьте первый бокс пустым, чтобы диапазон отсчитывался с начала. Оставьте последний бокс пустым, чтобы диапазон заканчивался текущей датой</div>
    </td>
    <td class="row2">
        <input type="text" name="last_activity_first" value="{$v['last_activity_first']}" class="textinput" style="width:80px">
        до
        <input type="text" name="last_activity_last" value="{$v['last_activity_last']}" class="textinput" style="width:80px">
    </td>	
</tr><tr>
    <td class="row1"><b>Принадлежит группе..</b></td>
    <td class="row2">{$v['_groups_list']}</td>
</tr><tr>
    <td class="row1"><b>ID пользователя</b></td>
    <td class="row2"><input type="text" name="memberid" value="{$v['email']}" class="textinput" style="width:auto"></td>    
</tr><tr>
    <td colspan="2" class="row_sub"><input type="submit" value="Искать"></td>
</tr>
</table>
EOF;
    }

    function searchResultsTable( $count, $paginator, $colsPerRow, $rowsContent, $deleteAllURL ) { return <<<EOF
<table class="tab" cellspacing="0" cellpadding="4" style="background: #EEF2F7;">
<tr>
    <td class="row0" colspan="{$colsPerRow}">
        Результат поиска: найдено {$count} пользователей
        <div style="float: right;">
            <a href="{$deleteAllURL}" style="color: red; font-weight: normal;">Удалить всех</a>
            &nbsp;
        </div>
    </td>
</tr>
{$rowsContent}
<tr>
    <td colspan="{$colsPerRow}" style="background-color: #D1DCEB; color: #3A4F6C; padding: 5px; margin-top: 1px; font-size: 10px;" align="right">{$paginator}</td>
</tr>
</table>
EOF;
    }

    function searchResultsRow( $cells ) { return <<<EOF
<tr align='center'>
    {$cells}
</tr>
EOF;
    }

    function searchResultsEmptyCell() { return <<<EOF
<td class='tdrow2'>&nbsp;</td>
EOF;
}

    function actionsMenu( $items ) { return <<<EOF
<img src="[img://]buttons/drop_down.gif" role="button" border="0" data-bs-toggle="dropdown" aria-expanded="false"  />
<ul class="dropdown-menu">
    {$items}
</ul>
EOF;
}

    function actionItem( $url, $title ) { return <<<EOF
<li><a class="dropdown-item" href="{$url}">{$title}</a></li>
EOF;
    }

    function searchResultsCell( $v, $cellWidth ) { return <<<EOF
<td width="{$cellWidth}" align="left" style="font-size: 10px; background: #EEF2F7;">
	<fieldset style="background: #DFE6EF; margin: 4px; padding: 2px;">
		<legend><strong>{$v['login']}</strong></legend>
		<div style="border: 1px solid #BBB; background: #EEE; margin: 2px; padding: 2px;">
			<table cellpadding="4" cellspacing="0" border="0" width="100%">
			<tr>
				<td width="1%" align="center" style="padding: 4px;">{$v['_avatar']}</td>
				<td width="99%">
					<b>{$v['dname']}</b>
					&nbsp;<span style="font-size: 9px" style="color:gray">{$v['ip_address']}</span>
				</td>
				<td width="1%" align="center">                    
                    {$v['_dropdown']}
				</td>
			</tr>
			</table>
		</div>                                                                          
		
		<div style="border: 1px solid #BBB; background: #FFF; margin: 2px; padding: 1px">
			<table cellpadding="2" cellspacing="0" border="0" width="100%" style="font-size: 10px;">
			<tr>
				<td width="1%" align="center" style="padding: 2px;">
					<img src="[img://]memsearch_email.gif" border="0" />
				<td>
				<td width="99%"><strong>{$v['email']}</strong></td>
			</tr><tr>
				<td width="1%" align="center">
					<img src="[img://]memsearch_group.gif" border="0" />
				<td>
				<td width="99%">
					<strong>{$v['group_name']}</strong> 					
				</td>
			</tr><tr>
				<td width="1%" align="center">
					<img src="[img://]memsearch_posts.gif" border="0" />
				<td>
				<td width="99%"><strong>Регистрация: {$v['_reg_time']}</strong></td>
			</tr>
			</table>
		</div>
	</fieldset>
</td>
EOF;
    }
}