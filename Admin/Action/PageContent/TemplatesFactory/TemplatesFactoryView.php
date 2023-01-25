<?php

namespace Admin\Action\PageContent\TemplatesFactory;

class TemplatesFactoryView {
    function tableBody( $body, $hiddens ) { return <<<EOF
<script>
	var oDialog;
	
	setTimeout( function() {
				
		Dialog.prototype.init = function()
		{
			this.baseurl = '[base://]';			
		}
		
		oDialog = new Dialog();
		oDialog.baseurl = '[base://]';
		
		//alert('Загружен');
	}, 1000 );
	
	function change_type_show( name, type )
	{
		document.getElementById( name + '_show_' + type ).style.display = '';
		
		document.getElementById( name + '_show_' + ( type == 'content' ? 'module' : 'content' )).style.display = 'none';
	}
</script>

<form action="" method="post">

<input type="hidden" name="i" value="{$hiddens['i']}" />
<input type="hidden" name="x" value="{$hiddens['x']}" />
<input type="hidden" name="pid" value="{$hiddens['pid']}" />
<input type="hidden" name="layout" value="{$hiddens['layout']}" />
<input type="hidden" name="mod_names" value="{$hiddens['mod_names']}" />

<table class="tab" cellspacing="0">
{$body}
</table>
</form>
EOF;
    }

    function editContentImg( $id, $field_name ) { return <<<EOF
&nbsp;<img src="[img://]buttons/edit.gif" width="20" height="20" border="0" align="absmiddle" title="Изменить" onClick="oDialog.openContentManager({$id}, '{$field_name}')" style="cursor:pointer" />
EOF;
    }

    function tableRow( $title, $name, $type_select, $mod_select, $mod_var, $content_field, $mod_func, $noadmin ) { return <<<EOF
<tr>
	<td class="row1" nowrap="nowrap"><b>{$title}</b></td>
	<td class="row2">{$type_select}</td>
	<td class="row1" nowrap width="99%">
		<div id="{$name}_show_module" style="display:none">			
			<table cellpadding="0" cellspacing="2" border="0" width="100%" style="font-size: 10px; color: #304F6B;">
				<tr><td width="10%" valign="middle">Имя модуля&nbsp;&nbsp;</td><td width="90%">{$mod_select}</td></tr>
				<tr><td width="10%" valign="middle">Переменная&nbsp;&nbsp;</td><td width="90%">{$mod_var}</td></tr>
				<tr><td width="10%" valign="middle">Функция&nbsp;&nbsp;</td><td width="90%">{$mod_func}</td></tr>
			</table>
		</div>
		<span id="{$name}_show_content" style="display:none">{$content_field}</span>
		<script>change_type_show( '{$name}', document.getElementsByName( '{$name}_type' )[0].value )</script>
		<div style="font-size: 10px; color: #304F6B; margin-top: 5px;">Запрет на управление: &nbsp; {$noadmin}</div>
	</td>
EOF;
    }

    function onErrorRow( $text ) { return <<<EOF
<tr>
    <td class="row1" colspan="3">{$text}</td>
</tr>
EOF;
    }

    function mod_params_table( $mod, $var, $act ) { return <<<EOF
<table cellpadding="0" cellspacing="2" border="0" width="100%" style="font-size: 10px; color: #304F6B;">
	<tr><td width="10%" valign="middle">Имя модуля&nbsp;&nbsp;</td><td width="90%"><input type="text" value="{$mod}" class="textinput" /></td></tr>
	<tr><td width="10%" valign="middle">Переменная&nbsp;&nbsp;</td><td width="90%"><input type="text" value="{$var}" class="textinput" /></td></tr>
	<tr><td width="10%" valign="middle">Функция&nbsp;&nbsp;</td><td width="90%"><input type="text" value="{$act}" class="textinput" /></td></tr>
</table>
EOF;
    }

    function controlAbilityRow( $canControl ) {
        $value = $canControl ? '<span style="color: green;">НЕТ</a>' : '<span style="color: red;">ДА</span>';
        return <<<EOF
<div style="color: #222; padding-top: 2px; padding-bottom: 2px;">Запрет на управление: &nbsp;&nbsp;{$value}</div>
EOF;
    }

    function submitRow() { return <<<EOF
<tr><td colspan="3" class="row_sub"><input type="submit" value="Сохранить" /></td></tr>
EOF;
    }
}