<?php

namespace Admin\Editor\Operations;

class FindView {
    function window( $content, $title, $jsObj ) { return <<<EOF
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<title>{$title}</title>
	<link rel="stylesheet" href="[css://]style.main.css" type="text/css">
	<script language="JavaScript" type="text/javascript" src="[js://]editor/_dialog.js"></script>
	<script language="JavaScript" type="text/javascript" src="[js://]editor/_dialog_find.js"></script>		
	<script language="JavaScript" type="text/javascript">
	    window.alert = function( text ) {
            var close = document.createElement('button');
            close.textContent = 'x';
            close.onclick = function( event ) {
                event.target.parentElement.remove();
            }
            
            var box = document.createElement("div");
            box.className = 'nge-alert';
            box.textContent = text;
            box.append( close );
            
            window.document.body.prepend( box );
	    }
        
        window.confirmation = function( text, callback ) {
            var confirm = document.createElement('button');
            confirm.textContent = 'v';
            confirm.className = 'nge-confirm-confirm';
            confirm.onclick = function( event ) {
                event.target.parentElement.remove();
                callback( true );
            }
            
            var cancel = document.createElement('button');
            cancel.textContent = 'x';
            cancel.className = 'nge-confirm-cancel';
            cancel.onclick = function( event ) {
                event.target.parentElement.remove();
                callback( false );
            }
            
            var box = document.createElement("div");
            box.className = 'nge-confirm';
            box.textContent = text;
            box.append( confirm );
            box.append( cancel );
            
            window.document.body.prepend( box );
        }
        
		var oDialog = new Dialog('{$jsObj}');					
	</script>
	<style type="text/css">
	    .nge-alert {
	        position: absolute;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.74);
            padding-top: 40px;
            box-sizing: border-box;
            color: rgb(231, 231, 231);
            text-shadow: black 1px 1px;
	    }
	    
	    .nge-alert > button {
            background: lightgray;
            font: 10px bold;
            width: 20px;
            height: 20px;
            text-shadow: white -1px -1px;
            color: black;
            text-indent: -1px;
            line-height: 100%;
            font-family: Comic Sans MS, serif;
            position: absolute;
            right: 10px;
            top: 10px;
	    }
	    
	    .nge-confirm {
	        position: absolute;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.74);
            padding-top: 10px;
            box-sizing: border-box;
            color: rgb(231, 231, 231);
            text-shadow: black 1px 1px;
	    }
	    
	    .nge-confirm > button {
            background: lightgray;
            font: 10px bold;
            width: 50px;
            height: 20px;
            text-shadow: white -1px -1px;
            color: black;
            text-indent: -1px;
            line-height: 100%;
            font-family: Comic Sans MS, serif;
            position: absolute;
	    }
	    
	    .nge-confirm-confirm {
	        background: green !important;
	        color: white !important;
	        left: 51% !important;
	        bottom: 10px !important;
	    }
	    
	    .nge-confirm-cancel {
	        background: red !important;
	        color: white !important;
	        right: 51% !important;
	        bottom: 10px !important;
	    }
    </style>
</head>

<body style="margin:0;padding:0;" onLoad="oDialog.init()">
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

    function findForm() { return <<<EOF
<form action="" method="post" name="frmSearch" id="frmSearch">
    <table class="tab" cellspacing="0" cellpadding="0" style="border:none">	
    <tr>
        <td class="row1">
            <fieldset>
            <legend>Поиск и замена</legend>
            <div class="fieldset">
                <table class="tab" id="background" cellspacing="0" cellpadding="0" style="border:none; width:304px;">
                <tr> 
                    <td class="row1" width="50%"><b>Найти</b></td>
                    <td class="row2" width="50%"><input type="text" class="textinput" name="strSearch" id="strSearch"></td>
                </tr><tr>
                    <td class="row1"><b>Заменить на</b></td>
                    <td class="row2"><input type="text" class="textinput" name="strReplace" id="strReplace"></td>						
                </tr><tr> 
                    <td class="row1"><b>Учитывать регистр</b></td>
                    <td class="row2"><input type="checkbox" name="blnMatchCase" id="blnMatchCase"></td>
                </tr><tr id="rowMatchWord">
                    <td class="row1"><b>Слово целиком</b></td>
                    <td class="row2"><input type="checkbox" name="blnMatchWord" id="blnMatchWord"></td>						
                </tr>						
                </table>
            </div>
            </fieldset>		
            <script language="JavaScript" type="text/javascript">
            if( ! document.all ) {
                document.getElementById('rowMatchWord').style.display = 'none';
            }
            </script>
        </td>
    </tr><tr>
        <td class="row_sub" colspan="2">		
            <input id="findNext" type="button" onClick="oDialog.findtext(1)" value="Найти далее">
            |
            <input id="replace" type="button" onClick="oDialog.replacetext(1)" value="Заменить">
            |
            <input id="replaceAll" type="button" onClick="oDialog.replaceall(1)" value="Заменить все">
        </td>
    </tr>
    </table>           
</form>
EOF;
    }
}