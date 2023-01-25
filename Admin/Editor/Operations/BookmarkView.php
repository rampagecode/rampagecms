<?php

namespace Admin\Editor\Operations;

class BookmarkView {
    /**
     * @param $content
     * @param $jsobj
     * @return string
     */
    function window( $content, $jsobj ) { return <<<EOF
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<title>Вставить закладку</title>
	<link rel="stylesheet" href="[css://]style.main.css" type="text/css">	
	<script language="JavaScript" type="text/javascript" src="[js://]editor/_dialog.js"></script>
	<script type="text/javascript">
		oDialog = new Dialog('{$jsobj}');
		
		Dialog.prototype.init = function() {
			document.getElementById('name').focus();
			this.hideLoadMessage();
		}
		
		Dialog.prototype.create_bookmark = function() {
			this.Editor.create_bookmark( document.getElementById('name').value.replace( /[^A-Za-z0-9\-:._]+/g, '' ));
			this.Editor.addUndoLevel();
			window.close();
			return false;
		}
	</script>
</head>
<body bgcolor="threedface" style="margin:0;padding:0;" onLoad="oDialog.init()">
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
</body>
</html>
EOF;
    }

    /**
     * @param $name
     * @return string
     */
    function content( $name ) { return <<<EOF
<form name="hyperlink_form" id="hyperlink_form" onSubmit="return oDialog.create_bookmark()">			
    <table class="tab" cellspacing="0" cellpadding="0" style="border:none">
    <tr> 
        <td class="row1">
            <br />
            <fieldset>
                <legend>Название закладки:</legend>
                <div class="fieldset">
                    <input name="name" id="name" type="text" class="textinput" style="width: 100%" value="{$name}"> 
                </div>
            </fieldset>
            <br />
        </td>
    </tr><tr>
        <td class="row_sub"> 
            <input type="submit" value="OK">
            &nbsp;&nbsp; 
            <input type="button" onClick="oDialog.closeWindow();" value="Отменить">
        </td>			
    </tr>
    </table>		
</form>
EOF;
    }
}