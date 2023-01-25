<?php

namespace Admin\Editor\Operations;

class HorizontalRuleView {
    /**
     * @return string
     */
    function window( $content, $jsobj ) { return <<<EOF
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
	<title>Вставить горизонтальную линию</title>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<link rel="stylesheet" href="[css://]style.main.css" type="text/css">	
	<script language="JavaScript" type="text/javascript" src="[js://]editor/_dialog.js"></script>
	<script language="JavaScript" type="text/javascript" src="[js://]editor/_dialog_hrule.js"></script>
	<script language="JavaScript" type="text/javascript">
		var oDialog = new Dialog('{$jsobj}');
	</script>	
</head>

<body style="margin:0;padding:0;" onLoad="oDialog.hideLoadMessage()">	
    <div align="center" id="dialogLoadMessage" style="display:block;">
        <table width="100%" height="90%">
            <tr>
                <td align="center" valign="middle"><div id="loadMessage">Подождите...</div></td>
            </tr>
        </table>
    </div>

    <div class="row1" align="center">
        {$content}
    </div>
</body>
</html>
EOF;
    }

    /**
     * @return string
     */
    function content() { return <<<EOF
<form name="hr_form" id="hr_form" onSubmit="return oDialog.insertRuler()">
    <fieldset>
    <legend>Горизонтальная линия</legend>
    <div class="fieldset" style="padding-bottom:0">
        <table class="tab" cellpadding="0" cellspacing="0" style="border:none; width: 100px">
        <tr> 
            <td class="row1"><b>Выравнивание</b></td>
            <td class="row2"> 
                <select name="align" id="align" class="textinput" style="width:120px">
                    <option selected="selected" value="">По умолчанию</option>
                    <option value="left">Слева</option>
                    <option value="center">По центру</option>
                    <option value="right">Справа</option>
                </select> 
            </td>
        </tr><tr> 
            <td class="row1"><b>Высота</b></td>
            <td class="row2">
                <input type="text" name="size" id="size" style="width:120px" class="textinput">
            </td>
        </tr><tr> 
            <td class="row1"><b>Ширина</b></td>
            <td class="row2">
                <input type="text" name="width" id="width" class="textinput" style="width:67px;"> 
                <select name="percent2" id="percent2" class="textinput" style="width:48px;">
                    <option value="%" selected="selected">%</option>
                    <option value="px">px</option>
                </select> 
            </td>
        </tr><tr> 
            <td class="row1"><b>Цвет</b></td>
            <td class="row2">				
                <button type="button" onClick="oDialog.colordialog(1);" style="background: #F7FBFF; border:0; font-size:11px; padding:1; cursor: pointer;" title="Выбрать цвет"> 
                    <span id="borderchosencolor" style="border: 1px solid #ADB6CE;">&nbsp;&nbsp;&nbsp;&nbsp;</span> ...
                </button>				
                <input type="hidden" name="color" id="color" value=""> 
            </td>
        </tr><tr>
            <td class="row_sub" colspan="2">
                <input type="submit" value="OK">
                &nbsp;&nbsp; 
                <input type="button" onClick="window.close();" value="Отменить">
            </td>
        </table>
    </div>	
    </fieldset>
</form>
EOF;
    }
}