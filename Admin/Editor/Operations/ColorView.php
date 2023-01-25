<?php

namespace Admin\Editor\Operations;

class ColorView {
    function window( $content, $jsobj, $action ) { return <<<EOF
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
	<title>Выберите цвет</title>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<link rel="stylesheet" href="[css://]style.main.css" type="text/css">
	<style type="text/css">	
	
	#tab_table {
		width: 286px;
		margin-top: 3px;
	}
	
	.colortable  {
		width: 264px;
		font-size: 10px;
		cursor: pointer; cursor: hand;		
	}
	.colorCell {
		border: 1px solid #000000;
	}	
	
	</style>
	<script language="JavaScript" type="text/javascript" src="[js://]editor/_dialog.js"></script>
	<script language="JavaScript" type="text/javascript" src="[js://]editor/_dialog_color.js"></script>
	<script language="JavaScript">
		var _get_action = "{$action}";
		var curHL = null;
		var oDialog = new Dialog('{$jsobj}');		
	</script>
	
</head>

<body style="margin:0;padding:0;" onLoad="init()">	
    <form name="foo" onSubmit="return end()">
        <div align="center" id="dialogLoadMessage" style="display:block;">
            <table width="100%" height="90%">
                <tr>
                    <td align="center" valign="middle"><div id="loadMessage">Подождите...</div></td>
                </tr>
            </table>
        </div>
        
        <div class="dialog_content" align="center">
            {$content}
        </div>
    </form>
</body>
</html>
EOF;
    }

    function content() { return <<<EOF
<table width="205" border="0" cellspacing="0" cellpadding="0" class="tab" style="border:none">
	<tr> 
		<td class="row1" width="33%"> 
			<div class="selcolordisplay" id="colordisplay" style="height: 40px; border: 1px solid #ADB6CE;">&nbsp;</div>
		</td>
		<td class="row2" width="33%" align="center" style="text-transform: uppercase; font-weight: bold; font-size: 11px;">
			<p><span id="rgb">&nbsp;</span></p>
		</td>
		<td class="row1" width="33%" align="right"> 
			<div class="selcolordisplay" style="height:15px; border: 1px solid #ADB6CE; margin-bottom:4px;" id="chosencolor">&nbsp;</div>
			<input class="textinput" type="text" style="width:100%" id="selcolor" name="selcolor" onChange="document.getElementById('chosencolor').style.backgroundColor = this.value"> 
		</td>
	</tr><tr>
		<td colspan="3" class="row1" align="center">
		
			<div id="site_color_container" style="display:block"> 
				<!-- Your custom color swatches will be generated here -->
			</div>
			
			<div id="web_color_container" style="display:block"> 
				
				<table id="web_colortable" width="100%" border="0" cellspacing="0" cellpadding="0" class="colortable">					
					<!-- Мы генерируем палитру сюда -->
					<script language="JavaScript">document.write( palette() )</script>						
					<tr><td class="colorCell" bgcolor="" colspan="19" align="center">По умолчанию</td></tr>
				</table>								
			</div>
		</td>
	</tr><tr>
		<td class="row_sub" colspan="3">
			<input id="ok" type="submit" value="OK">
			&nbsp; 
			<input type="button" onClick="oDialog.closeWindow();" value="Отменить">
		</td>
	</tr>
	</table>
EOF;
    }
}