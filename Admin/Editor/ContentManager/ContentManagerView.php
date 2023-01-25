<?php

namespace Admin\Editor\ContentManager;

class ContentManagerView {
    function window( $title, $content, $jsObj ) { return <<<EOF
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
	<title>{$title}</title>
	<link rel="stylesheet" href="[css://]style.main.css" type="text/css">	
    <script language="JavaScript" type="text/javascript" src="[js://]editor/_dialog.js"></script>
	<script language="JavaScript" type="text/javascript">
		oDialog = new Dialog('{$jsObj}');
	</script>    
    <base target="_self" />
</head>

<body scroll="no" onload="oDialog.hideLoadMessage()">
    <div align="center" id="dialogLoadMessage" style="display:block;">
        <table width="100%" height="90%">
        <tr>
            <td align="center" valign="middle"><div id="loadMessage">Please&nbsp;Wait...</div></td>
        </tr>
        </table>
    </div>

    <div align="center" style="padding-top: 140px;"> 	
        {$content}
    </div>
</body>
</html>
EOF;
    }

    function error( $title, $msg, $folder = null, $sortDir = null ) { return <<<EOF
<div align="center" style="width: 400px">
    <form action="[mod://]" method="post">
        <table class="tab" cellspacing="0">
        <tr>
            <td class="row0_err">{$title}</td>
        </tr><tr>
            <td class="row2">{$msg}</td>
        </tr><tr>
            <td class="row_sub">
                <!-- <input class="button" type="submit" name="OK" value="OK"> -->
                <input class="button" type="button" onclick="window.location = document.referrer" value="Назад" />
            </td>
        </tr>							
        </table>
                                                            
        <input type="hidden" name="folder" value="{$folder}" />
        <input type="hidden" name="sort_dir" value="{$sortDir}" />			       
        <input type="hidden" name="[?act_var]" value="" />						
    </form>
</div>
EOF;
    }

    /**
     * Верхняя строчка списка файлов для перемещения к родительскому каталогу
     * @param int $id
     * @return string
     */
    function moveUpRow( $id ) { return <<<EOF
<tr style="background: #EEF2F7;" onmouseover="this.style.backgroundColor='#F5F9FD'" onmouseout="this.style.backgroundColor='#EEF2F7'">
	<td class="row12" onclick="oDialog.openFolder({$id})" style="cursor:pointer" width="100%" colspan="4">
		<table width="100%" cellspacing="0" cellpadding="0" style="font-size:10px">
		<tr>
			<td width="1%"><img src="[img://]vfs/up.gif" border="0"></td>
			<td width="99%">&nbsp;<b>[..]</b></td>
		</tr>
		</table>		
	</td>		
</tr>
EOF;
    }

    /**
     * Таблицы списка документов
     * @param $content
     * @param $js_array
     * @return string
     */
    function listTable( $content, $js_array ) { return <<<EOF
<script>	  
	var imgs_url    = new Array();
	var imgs_width  = new Array();
	var imgs_height = new Array();
	
	{$js_array}				
</script>

<table class="tab" cellspacing="0" style="border:none;cursor:default;">
{$content}    
</table>
EOF;
    }

    /**
     * @param $file_name
     * @param $item_id
     * @return string
     */
    function itemEditForm( $file_name, $item_id ) { return <<<EOF
<div align="center" style="width:400px">
    <form action="[mod://]" name="itemEditForm" method="post">
        <table class="tab" cellspacing="0">
        <tr>
            <td class="row0" colspan="2">Параметры:</td>
        </tr><tr>
            <td class="row1" width="170px"><b>Имя</b></td>
            <td class="row2" width="230px"><input type="text" name="file_name" value="{$file_name}" class="textinput"></td>
        </tr><tr>
            <td class="row_sub" colspan="2">
                <input class="button" type="submit" name="OK" value="OK" onclick="if( document.itemEditForm.file_name.value == '' ){ alert('Введите имя'); return false }">
                &nbsp;					
                <a href="[mod://]"><u>Cancel</u></a>
            </td></tr>							
        </table>
                                                        
        <input type="hidden" name="[?act_var]" value="edit" />
        <input type="hidden" name="[?idx_var]" value="{$item_id}" />						
    </form>
</div>
EOF;
    }
}