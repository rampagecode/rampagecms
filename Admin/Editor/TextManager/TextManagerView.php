<?php

namespace Admin\Editor\TextManager;

class TextManagerView {

    /**
     * Окно диалога для загрузки и управления загруженными файлами
     * @param string $toolBar
     * @param string $sortBar
     * @param string $content
     * @param string $jsObj
     * @return string
     */
    function build( $toolBar, $sortBar, $content, $jsObj ) { return <<<EOF
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
	<meta http-equiv="Pragma" content="no-cache">
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
	<title>Выбор контента</title>
	
	<link rel="stylesheet" href="[css://]style.main.css" type="text/css">

	<script language="JavaScript" type="text/javascript" src="[js://]admin.js"></script>
	<script language="JavaScript" type="text/javascript" src="[js://]editor/_dialog.js"></script>
	<script language="JavaScript" type="text/javascript" src="[js://]editor/_dialog_text.js"></script>

	<script type="text/javascript">	
		var oDialog = new Dialog('{$jsObj}');
		
		Dialog.prototype.init = function() {
			this.hl 		= null;
			this.hl_title 	= '';
			this.mod_url	= '[mod://]';
						 
			this.hideLoadMessage();
		}	
	</script>		

	<base target="_self" />
</head>

<body scroll="no" onLoad="oDialog.init()" style="margin:0;padding:0;">
	
<div align="center" id="dialogLoadMessage" style="display:block;">
	<table width="100%" height="90%">
		<tr><td align="center" valign="middle"><div id="loadMessage">Подождите...</div></td></tr>
	</table>
</div>

<table class="tab" cellpadding="0" cellspacing="0" style="border:none">
<tr>
	<td class="row1" valign="top" style="width: 420px;">		
		<fieldset>
    	<legend>Менеджер контента</legend>
    	<div class="fieldset">        		
			<table class="tab" cellspacing="0" cellpadding="0" style="border:none; width: 465px;">
			    {$toolBar}
			    {$sortBar}
			    {$content}
    		</table>
    	</div>
    	</fieldset>
    </td>       
</tr><tr>
	<td align="center" valign="top" class="row_sub">
		<form name="image_form" id="image_form" style="display:inline" onSubmit="return oDialog.get_content_id()">
			<input type="hidden" id="sel_text_id" value="" />
			<input type="hidden" id="sel_text_title" value="" />
			<input id="ok" type="submit" value="OK" style="color:silver" disabled />&nbsp;
			<input type="button" onClick="oDialog.closeParentWindow()" value="Отменить">&nbsp;
		</form>
	</td>	
</tr>
</table>
</body>
</html>
EOF;
    }

    /**
     * @param string $path
     * @param string $folder
     * @return string
     */
    function toolBar( $path, $folder, $createFolderURL ) { return <<<EOF
<tr>
    <td class="row2" nowrap colspan="2">					
        <img src="[img://]dialogs/newpage.gif" width="21" height="20" title="Создать новый контент" border="0" align="absmiddle" onClick="oDialog.openContentWindow('[base://]&folder={$folder}',0)" style="cursor:pointer" />            			            							
    
        <input class="textinput" style="color: #949FBE; width: 400px;" type="text" name="imagename" value="{$path}" readonly="readonly">
                                                        
        <a href="{$createFolderURL}">
            <img src="[img://]editor/newfolder.gif" width="23" height="22" alt="Создать директорию" title="Создать директорию" border="0" align="absmiddle">
        </a>            			            			
    </td>
</tr>
EOF;
    }

    /**
     * @param string $link
     * @param string $icon
     * @return string
     */
    function sortBar( $link, $icon ) { return <<<EOF
<tr>
    <td class="row1" colspan="2" style="font-size: 11px; font-weight: bold; width: 230px;">
        <a href="{$link}">Имя:&nbsp;<img src="{$icon}" width="8" height="7" border="0"></a>
    </td>
</tr>
EOF;
    }

    /**
     * @param string $content
     * @return string
     */
    function content( $content ) { return <<<EOF
<tr>  
    <td class="row2" colspan="2">      		
        <div class="scroll_list" style="height:340px; width:455px; overflow:auto;">
            {$content}							   					
        </div>   
    </td>
</tr>
EOF;
    }

    /**
     * Строка с текстом в таблице списка файлов
     * @param int $id
     * @param string $title
     * @param string $pages
     * @param string $controls
     * @return string
     */
    function listFileRow( $id, $title, $pages, $controls ) { return <<<EOF
<tr style="background:#EEF2F7" onmouseover="this.style.backgroundColor='#F5F9FD'" onmouseout="this.style.backgroundColor='#EEF2F7'">
	<td colspan="3" class="row12" width="99%" id="txt{$id}" onClick="oDialog.selectText('{$id}', '{$title}')">
		<b>{$title}</b>&nbsp;<span style="color:#000; font-size: 8px;">id:&nbsp;{$id}</span>
		<br />На странице: {$pages}
	</td>
	<td class="row2" width="1%" nowrap>
		<img src="[img://]buttons/edit.gif" width="21" height="20" alt="Редактировать текст" title="Редактировать текст" border="0" align="absmiddle" onClick="oDialog.openContentWindow('[base://]',{$id})" style="cursor:pointer" />
		{$controls}
	</td>
</tr>
EOF;
    }

    function openContentWindow( $content, $title ) {
        return <<<EOF
<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
	<title>{$title}</title>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
	<link rel="stylesheet" type="text/css" media="all" href="[css://]style.main.css" />
	<script language="JavaScript" type="text/javascript" src="[js://]jquery/jquery.js"></script>	
</head>
<body style="margin:0;paggin:0;">
{$content}
</body>
</html>
EOF;
    }

    function closeContentWindow() { return <<<EOF
<html>
<head></head>
<body>
<script language="JavaScript" type="text/javascript">    
    var baseWindow = window;

	while( typeof baseWindow.ModalDialog !== 'object' || baseWindow !== baseWindow.parent ) {
		baseWindow = baseWindow.parent;
	}

	ModalDialog = baseWindow.ModalDialog;
    
	if( ModalDialog ) {
		ModalDialog.closeTopDialog();        
        ModalDialog.topDialogWindow().document.getElementsByTagName('IFRAME')[0].contentWindow.location.reload();
	} else {
        opener.location = opener.location; 
	    window.close();
	}   		
</script>
</body>
</html>
EOF;
    }
}