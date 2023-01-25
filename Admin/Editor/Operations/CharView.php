<?php

namespace Admin\Editor\Operations;

class CharView {
    function window( $content, $jsobj ) { return <<<EOF
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
	<title>Специальные символы</title>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
	<link rel="stylesheet" href="[css://]style.main.css" type="text/css">
	<style type="text/css">	
	td {
		cursor: pointer;
	}
	
	#characterspan {
		font-weight: bold;
		font-size: 15px;
	}
	
	#CharacterTable td {
		background-color: #EFF3F7;
		text-align: center;
		color: #314D6B;
		
	 	border-right: 1px solid #D1DCEB; 
	    border-bottom: 1px solid #D1DCEB; 
	    border-top: 1px solid #ffffff; 
	    border-left: 1px solid #ffffff;  		
	}
	</style>	
	<script language="JavaScript" type="text/javascript" src="[js://]editor/_dialog.js"></script>
	<script language="JavaScript" type="text/javascript" src="[js://]editor/_dialog_char.js"></script>
	<script language="JavaScript" type="text/javascript">
		var oDialog = new Dialog('{$jsobj}');
		var curHL = null;
	</script>
</head>

<body style="margin:0;padding:0;background:#EFF3F7;" onLoad="init()">
    <form name="foo" onSubmit="return finish()">
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
<table class="tab" width="100%" cellpadding="0" cellspacing="0" style="border:none;">
<tr> 
    <td class="row1" align="center" width="100%">
        <fieldset>
        <legend>Специальные символы:</legend>
            <table class="tab" cellpadding="0" cellspacing="0" style="border:none; width: 477px; margin-bottom:5px;">
            <tr height="40px">
                <td class="row1" width="45%" align="right"><b>Вставить:</b></td>
                <td class="row2" width="45%" align="left">						
                    <input id="insert" name="insert" type="text" class="textinput" style="width:60px" onChange="man_character()">						
                </td>
                <td class="row1" width="10%" align="center"><span id="characterspan">&nbsp;</span></td>					
            </tr><tr>
                <td class="row1" colspan="3">
                    <table id="CharacterTable" border="0" cellspacing="0"  cellpadding="3"  align="center">
                    <tr> 
                        <td width="25" title="&amp;iexcl;">&iexcl;</td>
                        <td width="25" title="&amp;cent;">&cent;</td>
                        <td width="25" title="&amp;pound;">&pound;</td>
                        <td width="25" title="&amp;#8364;">&#8364;</td>
                        <td width="25" title="&amp;yen;">&yen;</td>
                        <td width="25" title="&amp;sect;">&sect;</td>
                        <td width="25" title="&amp;uml;">&uml;</td>
                        <td width="25" title="&amp;copy;">&copy;</td>
                        <td width="25" title="&amp;laquo;">&laquo;</td>
                        <td width="25" title="&amp;not;">&not;</td>
                        <td width="25" title="&amp;reg;">&reg;</td>
                        <td width="25" title="&amp;deg;">&deg;</td>
                        <td width="25" title="&amp;plusmn;">&plusmn;</td>
                        <td width="25" title="&amp;acute;">&acute;</td>
                        <td width="25" title="&amp;micro;">&micro;</td>
                        <td width="25" title="&amp;para;">&para;</td>
                        <td width="25" title="&amp;middot;">&middot;</td>
                    </tr><tr> 
                        <td width="25" title="&amp;cedil;">&cedil;</td>
                        <td width="25" title="&amp;raquo;">&raquo;</td>
                        <td width="25" title="&amp;iquest;">&iquest;</td>
                        <td width="25" title="&amp;Agrave;">&Agrave;</td>
                        <td width="25" title="&amp;Aacute;">&Aacute;</td>
                        <td width="25" title="&amp;Acirc;">&Acirc;</td>
                        <td width="25" title="&amp;Atilde;">&Atilde;</td>
                        <td width="25" title="&amp;Auml;">&Auml;</td>
                        <td width="25" title="&amp;Aring;">&Aring;</td>
                        <td width="25" title="&amp;AElig;">&AElig;</td>
                        <td width="25" title="&amp;Ccedil;">&Ccedil;</td>
                        <td width="25" title="&amp;Egrave;">&Egrave;</td>
                        <td width="25" title="&amp;Eacute;">&Eacute;</td>
                        <td width="25" title="&amp;Ecirc;">&Ecirc;</td>
                        <td width="25" title="&amp;Euml;">&Euml;</td>
                        <td width="25" title="&amp;Igrave;">&Igrave;</td>
                        <td width="25" title="&amp;Iacute;">&Iacute;</td>
                    </tr><tr> 
                        <td width="25" title="&amp;Icirc;">&Icirc;</td>
                        <td width="25" title="&amp;Iuml;">&Iuml;</td>
                        <td width="25" title="&amp;Ntilde;">&Ntilde;</td>
                        <td width="25" title="&amp;Ograve;">&Ograve;</td>
                        <td width="25" title="&amp;Oacute;">&Oacute;</td>
                        <td width="25" title="&amp;Ocirc;">&Ocirc;</td>
                        <td width="25" title="&amp;Otilde;">&Otilde;</td>
                        <td width="25" title="&amp;Ouml;">&Ouml;</td>
                        <td width="25" title="&amp;Oslash;">&Oslash;</td>
                        <td width="25" title="&amp;Ugrave;">&Ugrave;</td>
                        <td width="25" title="&amp;Uacute;">&Uacute;</td>
                        <td width="25" title="&amp;Ucirc;">&Ucirc;</td>
                        <td width="25" title="&amp;Uuml;">&Uuml;</td>
                        <td width="25" title="&amp;szlig;">&szlig;</td>
                        <td width="25" title="&amp;agrave;">&agrave;</td>
                        <td width="25" title="&amp;aacute;">&aacute;</td>
                        <td width="25" title="&amp;acirc;">&acirc;</td>
                    </tr><tr> 
                        <td width="25" title="&amp;atilde;">&atilde;</td>
                        <td width="25" title="&amp;auml;">&auml;</td>
                        <td width="25" title="&amp;aring;">&aring;</td>
                        <td width="25" title="&amp;aelig;">&aelig;</td>
                        <td width="25" title="&amp;ccedil;">&ccedil;</td>
                        <td width="25" title="&amp;egrave;">&egrave;</td>
                        <td width="25" title="&amp;eacute;">&eacute;</td>
                        <td width="25" title="&amp;ecirc;">&ecirc;</td>
                        <td width="25" title="&amp;euml;">&euml;</td>
                        <td width="25" title="&amp;igrave;">&igrave;</td>
                        <td width="25" title="&amp;iacute;">&iacute;</td>
                        <td width="25" title="&amp;icirc;">&icirc;</td>
                        <td width="25" title="&amp;iuml;">&iuml;</td>
                        <td width="25" title="&amp;ntilde;">&ntilde;</td>
                        <td width="25" title="&amp;ograve;">&ograve;</td>
                        <td width="25" title="&amp;oacute;">&oacute;</td>
                        <td width="25" title="&amp;ocirc;">&ocirc;</td>
                    </tr><tr> 
                        <td width="25" title="&amp;otilde;">&otilde;</td>
                        <td width="25" title="&amp;ouml;">&ouml;</td>
                        <td width="25" title="&amp;divide;">&divide;</td>
                        <td width="25" title="&amp;oslash;">&oslash;</td>
                        <td width="25" title="&amp;ugrave;">&ugrave;</td>
                        <td width="25" title="&amp;uacute;">&uacute;</td>
                        <td width="25" title="&amp;ucirc;">&ucirc;</td>
                        <td width="25" title="&amp;uuml;">&uuml;</td>
                        <td width="25" title="&amp;yuml;">&yuml;</td>
                        <td width="25" title="&amp;#8218;">&#8218;</td>
                        <td width="25" title="&amp;#402;">&#402;</td>
                        <td width="25" title="&amp;#8222;">&#8222;</td>
                        <td width="25" title="&amp;#8230;">&#8230;</td>
                        <td width="25" title="&amp;#8224;">&#8224;</td>
                        <td width="25" title="&amp;#8225;">&#8225;</td>
                        <td width="25" title="&amp;#710;">&#710;</td>
                        <td style="font-size:9px" width="25" title="&amp;#8240;">&#8240;</td>
                    </tr><tr> 
                        <td width="25" title="&amp;#8249;">&#8249;</td>
                        <td width="25" title="&amp;#338;">&#338;</td>
                        <td width="25" title="&amp;#8216;">&#8216;</td>
                        <td width="25" title="&amp;#8217;">&#8217;</td>
                        <td width="25" title="&amp;#8220;">&#8220;</td>
                        <td width="25" title="&amp;#8221;">&#8221;</td>
                        <td width="25" title="&amp;#8226;">&#8226;</td>
                        <td width="25" title="&amp;#8211;">&#8211;</td>
                        <td width="25" title="&amp;#8212;">&#8212;</td>
                        <td width="25" title="&amp;#732;">&#732;</td>
                        <td width="25" title="&amp;#8482;">&#8482;</td>
                        <td width="25" title="&amp;#8250;">&#8250;</td>
                        <td width="25" title="&amp;#339;">&#339;</td>
                        <td width="25" title="&amp;#376;">&#376;</td>
                        <td width="25" title="&amp;frac14;">&frac14;</td>
                        <td width="25" title="&amp;frac12;">&frac12;</td>
                        <td width="25" title="&amp;frac34;">&frac34;</td>
                    </tr>
                    </table>
                </td>
            </tr>
            </table>
            
        </fieldset>						
    </td>
</tr><tr>
    <td class="row_sub">
        <input type="submit" id="ok" value="OK">
        &nbsp; 
        <input type="button" onClick="oDialog.closeWindow();" value="Отменить">
    </td>
</tr>	
</table>
EOF;
    }
}