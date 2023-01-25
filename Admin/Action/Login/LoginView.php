<?php

namespace Admin\Action\Login;

class LoginView {
    function loginPage( $query_string = '', $message = '', $name = '' ) { return <<<EOF
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<title>Вход в панель управления сайтом</title>
	<meta http-equiv="content-type" content="text/html; charset=utf-8" /> 	
 	<meta HTTP-EQUIV="Pragma"  CONTENT="no-cache" />
 	<meta HTTP-EQUIV="Cache-Control" CONTENT="no-cache" />
 	<meta HTTP-EQUIV="Expires" CONTENT="Mon, 06 May 1996 04:57:00 GMT" />

	<link rel="stylesheet" type="text/css" media="all" href="[css://]style.main.css" />
	
	<script type='text/javascript'>
	//<![CDATA[
		if( top.location != self.location ) { top.location = self.location }
	
		function fieldfocus() {
			try {
				if( document.getElementById('namefield').value != '' ) {
					document.getElementById('passfield').focus();
				} else {
					document.getElementById('namefield').focus();
				}
			}
			catch(e) {}
		}
	//]]>	 
	</script>

	<style>
	    html, body {
	        background: rgb(248, 247, 250);
	        margin: 0;
	        padding: 0;
	    }
	    
		#login_head {
			width: auto; 
			height: 13px; 
			font-size: 13px; 
			text-align: center; 
			font-weight: normal; 
			padding: 11px; 
			border-top: none;
			border-left: none;
			border-right: none;
			border-bottom: 1px solid #7e7f80;
			text-shadow: 1px 1px rgba(110, 127, 156, 0.4);			
		}
		
		#login_form {
			padding: 10px;	
			padding-left: 0;		
			font-size: 10px; 
			width: 300px; 
			background: #fff; 
			border-top: 1px solid #D4D9E1;			
			border-left: none;
			border-right: none;
			border-bottom: none;			 						
		}
		
		#login_box {
		    width: 300px; 
		    margin-left: auto; 
		    margin-right: auto; 
		    border: 1px solid rgba(92, 127, 181, 0.37);
		    box-shadow: 6px 6px rgba(130, 138, 142, 0.31);
		}
	</style>	
</head>
<body onload="fieldfocus();">
<p>&nbsp;</p>
<p>&nbsp;</p>
<p>&nbsp;</p>
<p>&nbsp;</p>
<p>&nbsp;</p>
<p>&nbsp;</p>
<p>&nbsp;</p>
<form id="loginform" action="" method="post">
    <input type="hidden" name="qstring" value="{$query_string}" />
    <input type="hidden" name="u" value="a" />
    <input type="hidden" name="a" value="login" />
    <input type="hidden" name="i" value="do_log_in" />
    
    <div id="login_box">
        <div id="login_head" class="login_header">Вход в панель управления</div>
        <table id="login_form" cellpadding="5" cellspacing="0">
        <tr>
            <td colspan="3" style="font-weight: bold; color: red">{$message}</td>
        </tr><tr>
            <td width="70px" align="right"><strong>Логин</strong></td>
            <td width="160px" align="left">
                <input style="border: 1px solid #AAA; width: 140px; font-size: 11px; font-family: Verdana; padding: 3px;" type="text" name="username" id="namefield" value="{$name}" />
            </td>
        </tr><tr>
            <td align="right"><strong>Пароль</strong></td>
            <td align="left">
                <input style="border: 1px solid #AAA; width: 140px; font-size: 11px; font-family: Verdana; padding: 3px;" type="password" name="password" id="passfield" value="" />
            </td>
        </tr><tr>
            <td colspan="3" align="center" style="height: 10px;">
            <input type="submit" value="Войти" />
            </td>
        </table>
    </div>
</form>
<p>&nbsp;</p>
<p>&nbsp;</p>
<p>&nbsp;</p>
<p>&nbsp;</p>
</body>
</html>
EOF;
    }
}