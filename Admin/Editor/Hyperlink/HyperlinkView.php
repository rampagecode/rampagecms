<?php

namespace Admin\Editor\Hyperlink;

class HyperlinkView {
    /**
     * Окно диалога создания/редактирования ссылок в контенте RTE
     */
    function show( $jsobj ) { return <<<EOF
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<title>Cсылка</title>
	<style type="text/css">
	<!--	
	p {
		margin:2px
	}
	.tbuttonUp {
		width: 88px;
		height: 67px;
		padding: 2px;
		border: 1px solid #D6DFEF;
		border-top: 1px solid #fff;
		background-color: #EFF3F7;
		cursor: default;
		text-align:center;
		display: block;
	}
	.tbuttonDown {
		width: 88px;
		height: 67px;
		padding: 2px;
		border-top: 1px solid #fff;
		border-left: 1px solid #fff;
		border-bottom: 1px solid #D6DFEF;
		border-right: 1px solid #D6DFEF;
		background-color: #F7FBFF;
		cursor: default;
		text-align:center;
		display: block;
	}
	.tbuttonOver {
		width: 88px;
		height: 67px;
		padding: 2px;
		border: 1px solid #D6DFEF;
		border-top: 1px solid #fff;
		background-color: #F7FBFF;
		cursor: pointer;
		text-align:center;
		display: block;
	}
	#outlookbar {
		height:100%; 
		border-top:1px solid threedshadow; 
		border-bottom: 1px solid threedhighlight; 
		border-left: 1px solid threedshadow; 
		border-right: 1px solid threedhighlight;
	}
	#siteTree {
		width: 524px; 
		height: 224px; 
		background-color: #FFFFFF; 
		border: 1px solid #D6DFEF;
		overflow: auto; 
		padding: 5px 5px;
	}        	
	#anchors {
		width: 524px; 
		height: 254px; 
		background-color: #FFFFFF; 
		border: 1px solid #D6DFEF;
		padding: 5px 5px	
	}  	
	#email {		
		width: 535px; 
		height: 100%;
		display: none;
		background: #F7FBFF;   		
		padding: 4px;
	}
	#external {		
		width: 535px; 
		height: 100%;
		display: none;
		background: #F7FBFF;   		
		padding: 4px;
	}	
	-->
	</style>
	<link rel="stylesheet" href="[css://]style.main.css" type="text/css">
	<script language="JavaScript" type="text/javascript" src="[js://]editor/_dialog.js"></script>
	<script language="JavaScript" type="text/javascript" src="[js://]editor/_dialog_hyperlink.js"></script>		
	<script type="text/javascript">	
		var oDialog = new Dialog('{$jsobj}');
	</script>	
	
	<base target="_self" />
</head>

<body style="margin:0;padding:0;" scroll="no" onLoad="oDialog.init();">

<div align="center" id="dialogLoadMessage" style="display:block;">
	<table width="100%" height="90%">
		<tr>
			<td align="center" valign="middle"><div id="loadMessage">Подождите...</div></td>
		</tr>
	</table>
</div>

<div align="center" id="siteTreeLoadMessage" style="display:none;">
	<table width="100%" height="90%">
		<tr>
			<td align="center" valign="middle"><div id="siteTreeMessage">Загружается дерево сайта...</div></td>
		</tr>
	</table>
</div>               

<form name="form1" id="form1" onSubmit="return oDialog.linkit();">
	
<table class="tab" cellspacing="0" cellpadding="0" style="border:none">
<tr> 
	<td class="row1" valign="top" width="90" align="center">
		<div style="padding: 8px"><b>Ссылка к:</b></div>		
		<div id="outlookbar"> 
			<div class="tbuttonUp" id="site_button" style="display:none" onClick="oDialog.mTabClick(this)" onMouseOver="oDialog.mTabOver(this)" onMouseOut="oDialog.mTabOut(this)" onMouseDown="oDialog.mTabDown(this)"> 
				<img src="[img://]dialogs/spacer.gif" width="1" height="3" alt=""><br>
				<img src="[img://]dialogs/file_on_this_site.gif" width="22" height="23" alt="">
				<p>Страница сайта</p>
			</div>

			<div class="tbuttonUp" id="page_button" style="display:none" onClick="oDialog.mTabClick(this)" onMouseOver="oDialog.mTabOver(this)" onMouseOut="oDialog.mTabOut(this)" onMouseDown="oDialog.mTabDown(this)"> 
				<img src="[img://]dialogs/spacer.gif" width="1" height="3" alt=""><br>
				<img src="[img://]dialogs/place_on_this_page.gif" width="22" height="23" alt="">
				<p>Ссылка на закладку</p>
			</div>
			
			<div class="tbuttonUp" id="email_button" style="display:block" onClick="oDialog.mTabClick(this)" onMouseOver="oDialog.mTabOver(this)" onMouseOut="oDialog.mTabOut(this)" onMouseDown="oDialog.mTabDown(this)"> 
				<img src="[img://]dialogs/spacer.gif" width="1" height="3" alt=""><br>
				<img src="[img://]dialogs/email_address.gif" width="22" height="23" alt="">
				<p>Адрес E-mail</p>
			</div>
			
			<div class="tbuttonUp" id="web_button" style="display:block" onClick="oDialog.mTabClick(this)" onMouseOver="oDialog.mTabOver(this)" onMouseOut="oDialog.mTabOut(this)" onMouseDown="oDialog.mTabDown(this)"> 
				<img src="[img://]dialogs/spacer.gif" width="1" height="3" alt=""><br>
				<img src="[img://]dialogs/external_link.gif" width="22" height="23" alt="">
				<p>Место Web</p>
			</div>
		</div>
	</td><td>           
		
		<!-- Страница сайта -->
		
		<table id="placeonthissite" class="tab" cellspacing="0" cellpadding="0" style="border:none;display:none">
		<tr>
			<td class="row2" colspan="2">
				<!-- Сюда загрузится дерево -->
				<div id="siteTree" disabled="disabled"></div>					
			</td>
		</tr><tr> 
			<td class="row1" width="20%"><b>Адрес</b></td>
			<td class="row2" width="80%"><input type="text" class="textinput" name="site_address" id="site_address" value=""></td>
		</tr><tr> 
			<td class="row1"><b>Заголовок</b></td>
			<td class="row2"><input type="text" class="textinput" name="site_title" id="site_title" value="" title="Подсказка, типа той, что вы сейчас видите"></td>
		</tr><tr> 
			<td class="row1"><b>Окно</b></td>
			<td class="row2">
				<select class="textinput" name="site_target_list" id="site_target_list" onChange="document.getElementById('site_target').value=this.options[this.selectedIndex].value">
					<option value="" selected="selected">По умолчанию</option>
					<option value="_self">Открыть в этом окне</option>
					<option value="_blank">Открыть в новом окне</option>
					<option value="_parent">Открыть в родительском окне</option>
					<option value="_top">Открыть в верхнем окне</option>
				</select> 
				<input type="text" name="site_target" id="site_target" value="" style="display:none">	
			</td>
		</tr>
		</table>			

		<!-- Ссылка на закладку -->
			
		<table id="placeonthispage" class="tab" cellspacing="0" cellpadding="0" style="border:none;display:none">
		<tr>
			<td class="row2" colspan="2">					
				<div id="anchors"> 
					<!-- a list of bookmarks in this page will be generated below: -->
				</div>
			</td>
		</tr><tr> 
			<td class="row1" width="20%"><b>Адрес</b></td>
			<td class="row2" width="80%"><input type="text" class="textinput" name="page_address" id="page_address" value="" title="Пример: #bookmarkName"></td>
		</tr><tr> 
			<td class="row1"><b>Заголовок</b></td>
			<td class="row2"><input type="text" class="textinput" name="page_title" id="page_title" value="" title="Создает сообщение в pop-up (наподобие того, что вы читаете сейчас)."></td>
		</tr>
		</table>			
			
		<!-- Адрес E-mail -->		
		
		<div id="email">
			<div style="height: 113px;">&nbsp;</div>
			<table class="tab" cellspacing="0" cellpadding="0" style="border:none;">
			<tr>
				<td class="row1" width="20%"><b>Адрес</b></td>
				<td class="row2" width="80%"><input type="text" class="textinput" name="email_address" id="email_address" value="" title="Пример: me@mycompany.com"></td>
			</tr><tr> 
				<td class="row1"><b>Тема</b></td>
				<td class="row2"><input type="text" class="textinput" name="email_subject" id="email_subject" value="" title="Вы можете ввести тему сообщение"></td>
			</tr><tr> 
				<td class="row1"><b>Заголовок</b></td>
				<td class="row2"><input type="text" class="textinput" name="email_title" id="email_title" value="" title="Создает сообщение в pop-up (наподобие того, что вы читаете сейчас)"></td>			
			</tr>
			</table>			
			<div style="height: 113px;">&nbsp;</div>
		</div>
			
		<!-- Место Web -->		
		
		<div id="external">
			<div style="height: 113px;">&nbsp;</div>
			<table class="tab" cellspacing="0" cellpadding="0" style="border:none;">
			<tr>
				<td class="row1" width="20%"><b>Адрес</b></td>
				<td class="row2" width="80%"><input type="text" class="textinput" name="web_address" id="web_address" value="" title="Пример: http://www.website.com/about/index.html"></td>
			</tr><tr> 
				<td class="row1"><b>Заголовок</b></td>
				<td class="row2"><input type="text" class="textinput" name="web_title" id="web_title" value="" title="Создает сообщение в pop-up (наподобие того, что вы читаете сейчас)"></td>
			</tr><tr> 
				<td class="row1"><b>Окно</b></td>
				<td class="row2">
					<select class="textinput" name="web_target_list" id="web_target_list" onChange="document.getElementById('web_target').value=this.options[this.selectedIndex].value">
						<option value="" selected="selected">По умолчанию</option>
						<option value="_self">Открыть в этом окне</option>
						<option value="_blank">Открыть в новом окне</option>
						<option value="_parent">Открыть в родительском окне</option>
						<option value="_top">Открыть в верхнем окне</option>
					</select> 
					<input type="text" name="web_target" id="web_target" value="" style="display:none">						
				</td>			
			</tr>
			</table>			
			<div style="height: 113px;">&nbsp;</div>
		</div>
						
	</td>
</tr>
</table>
		
<table class="tab" cellspacing="0" cellpadding="0" style="border:none">		
<tr>
	<td class="row_sub">
		<input type="submit" value="OK">
		&nbsp;&nbsp; 
		<input type="button" onClick="oDialog.closeTopWindow();" value="Отменить">
	</td>
</tr>
</table>
</form>
</body>
</html>
EOF;
    }
}