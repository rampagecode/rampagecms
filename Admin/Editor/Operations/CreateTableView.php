<?php

namespace Admin\Editor\Operations;

class CreateTableView {
    /**
     * @return string
     */
    function window( $content, $jsobj ) { return <<<EOF
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
	<title>Вставить таблицу</title>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<link rel="stylesheet" href="[css://]style.main.css" type="text/css">	
	<script language="JavaScript" type="text/javascript" src="[js://]editor/_dialog.js"></script>
	<script language="JavaScript" type="text/javascript" src="[js://]editor/_dialog_table.js"></script>
	<script type="text/javascript">	
		var oDialog = new Dialog('{$jsobj}');
	</script>
	
	<base target="_self" />
</head>

<body style="padding:0;margin:0;" onLoad="oDialog.init()">
    <div align="center" id="dialogLoadMessage" style="display:block;">
        <table width="100%" height="90%">
        <tr>
            <td align="center" valign="middle"><div id="loadMessage">Подождите...</div></td>
        </tr>
        </table>
    </div>
    {$content}
</body>
</html>
EOF;
    }

    /**
     * @return string
     */
    function content() { return <<<EOF
<form name="table_form" id="table_form" onSubmit="return oDialog.conjunction()">
    <table class="tab" cellpadding="0" cellspacing="0" style="border:1px;">
    <tr>
        <td class="row1" valign="top"> 
            <fieldset>
            <legend>Размер таблицы:</legend>
            <div class="fieldset">		
                <table class="tab" cellpadding="0" cellspacing="0" style="border:none; width: 405px;">
                <tr> 
                    <td width="20%" class="row1"><b>Строки</b></td>
                    <td width="30%" class="row2">
                        <input type="text" name="rows" id="rows" size="4" value="3" class="textinput" style="width: 50px;" onChange="oDialog.updateStyle()" />
                    </td>
                    <td width="20%" class="row1"><b>Ширина</b></td>
                    <td width="30%" class="row2">
                        <input type="text" name="width" id="width" size="4" value="100" class="textinput" style="width: 50px;" /> 
                        
                        <select name="percent1" id="percent1" class="textinput" style="width:50px; padding:0; height:16px;">
                            <option value="%" selected="selected">%</option>
                            <option value="">px</option>
                        </select>
                    </td>
                </tr><tr> 
                    <td class="row1"><b>Колонки</b></td>
                    <td class="row2">
                        <input type="text" name="cols" id="cols" size="4" value="3" class="textinput" style="width: 50px;" onChange="oDialog.updateStyle()" />
                    </td>
                    <td class="row1"><b>Высота</b></td>
                    <td class="row2">
                        <input type="text" name="height" id="height" size="4" value="" class="textinput" style="width: 50px;" /> 
                        
                        <select name="percent2" class="textinput" style="width:50px; padding:0; height:16px;" />
                            <option value="%">%</option>
                            <option value="" selected="selected">px</option>
                        </select>
                    </td></tr>
                </table>
            </div>		
            </fieldset>
        </td>
    </tr><tr>
        <td class="row1">
            <fieldset>
            <legend>Отступы</legend>
            <div class="fieldset">
                <table class="tab" cellpadding="0" cellspacing="0" style="border:none; width: 405px;">
                <tr> 
                    <td width="30%" class="row1"><b>Интервалы</b></td>
                    <td width="20%" class="row2">
                        <input type="text" name="cellspacing" id="cellspacing" size="4" value="0" onChange="oDialog.updateStyle()" class="textinput" style="width: 50px;" /> 
                    </td>
                    <td width="30%" class="row1"><b>Отступы в ячейке</b></td>
                    <td width="20%" class="row2"> 
                        <input type="text" name="cellpadding" id="cellpadding" size="4" value="3" onChange="oDialog.updateStyle()" class="textinput" style="width: 50px;" /> 
                    </td>
                </tr>
                </table>
            </div>		
            </fieldset>
        </td>
    </tr><tr>
        <td class="row1">
            <fieldset>
            <legend>Границы</legend>
            <div class="fieldset">		
                <table class="tab" cellpadding="0" cellspacing="0" style="border:none; width: 405px;">
                <tr> 
                    <td width="30%" class="row1"><b>Размеры рамок</b></td>
                    <td width="20%" class="row2">
                        <input type="text" name="border" id="border" size="4" value="1" onChange="oDialog.updateStyle()" class="textinput" style="width: 50px;" />
                    </td>
                    <td width="30%" class="row1"><b>Сжатые границы</b></td>
                    <td width="20%" class="row2">
                        <!--
                        <input type="checkbox" name="collapse" id="collapse" value="ON" onClick="oDialog.updateStyle()" checked="checked" disabled="disabled" />
                        -->
                        <input type="checkbox" name="collapse" id="collapse" value="ON" onClick="oDialog.updateStyle()" />
                    </td>
                </tr><tr> 
                    <td class="row1"><b>Цвет фона</b></td>
                    <td class="row2">
                        <button type="button" onClick="oDialog.colordialog(2,'{$jsobj}');" style="background: #F7FBFF; border:0; font-size:11px; padding:1; cursor: pointer;" title="Выбрать цвет"> 
                            <span id="bgchosencolor" style="border: 1px solid #ADB6CE;">&nbsp;&nbsp;&nbsp;&nbsp;</span> ...
                        </button>
                        <input type="hidden" name="bgcolor" id="bgcolor" value="" onChange="oDialog.updateStyle()" /> 
                    </td>
                    <td class="row1"><b>Цвет рамок</b></td>
                    <td class="row2">
                        <button type="button" onClick="oDialog.colordialog(1,'{$jsobj}');" style="background: #F7FBFF; border:0; font-size:11px; padding:1; cursor: pointer;" title="Выбрать цвет"> 
                            <span id="borderchosencolor" style="border: 1px solid #ADB6CE;">&nbsp;&nbsp;&nbsp;&nbsp;</span> ...
                        </button>
                        <input type="hidden" name="bordercolor" id="bordercolor" value="#000000" onChange="oDialog.updateStyle()" /> 
                    </td></tr>
                </table>
            </div>	
            </fieldset>
        </td>
    </tr><tr>
        <td class="row1">
            <fieldset>
                <legend>Просмотр стиля</legend>
                
                <div class="fieldset"> 
                    <iframe src="/blank.html" id="pasteFrame" style="background-color: #fff; height: 70px; width: 400px; border: 1px solid #ADB6CE;" frameborder="0"></iframe>			
                </div>
            </fieldset>
        </td>
    </tr><tr>
        <td class="row_sub">	
            <div align="center"> 
                <input type="submit" value="OK">
                &nbsp;&nbsp; 
                <input type="button" onClick="oDialog.closeTopWindow();" value="Отменить">	
            </div>
        </td>
    </tr>
    </table>
</form>
EOF;
    }

}