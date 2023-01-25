<?php

namespace Admin\Editor\ImageManager;

class ImageManagerView {

    function window( $jsObj, $folder, $extensions, $maxFileSize, $path, $sortLink, $sortIcon, $content, $createFolderURL ) { return <<<EOF
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
	<meta http-equiv="Pragma" content="no-cache">
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
	<title>Выбор изображения</title>
	
	<link rel="stylesheet" href="[css://]style.main.css" type="text/css">
	
	<script language="JavaScript" type="text/javascript" src="[js://]admin.js"></script>
	<script language="JavaScript" type="text/javascript" src="[js://]editor/multiupload/run.js"></script>
	<script language="JavaScript" type="text/javascript" src="[js://]editor/_dialog.js"></script>
	<script language="JavaScript" type="text/javascript" src="[js://]editor/_dialog_image.js"></script>	

	<style type="text/css">
		p {
			margin:2px
		}
		.filename {
			width:180px;
			height:22px;
			overflow:hidden;
			white-space: nowrap;
		}
		.thumbFilename {
			width:285px;
			display: block;
			overflow:hidden;
			white-space: nowrap;
			background: left no-repeat;
			padding: 2px 0px 2px 100px;
			voice-family: "\"}\"";
			voice-family:inherit;
			width:185px;
		}
		.fileBar a {
			display: block;
			color: #000000;
			text-decoration: none;
		}
		.fileBar a:hover {
			display: block;
			color: #000000;
			text-decoration: none;
		}
		.fileBar a:active {
			display: block;
			color: #000000;
			background-color: transparent;
			text-decoration: none;
		}
		.fileBar a img {
			border-width: 0px;
		}
		#dialogLoadMessage table, #uploadMessage table {
			height: 437px;
		}
	</style>
		
	<script type="text/javascript">	
		var oDialog = new Dialog('{$jsObj}');
		
		Dialog.prototype.init = function() {
			this.hl 		= null;
			this.imgWidth	= 0; 
			this.imgHeight	= 0;
			this.imgURL		= '';
			this.modURL		= '[mod://]';
			this.hideLoadMessage();
		}
		
		function do_redirect() {
			window.location = '[mod://]&folder={$folder}';
		}
		
		function flash_multiupload_complete() {					
			setTimeout( "do_redirect()", 1000 );
		}
		
		function flash_get_uploading_url() {
			return '[mod://]&i=upload_image&folder={$folder}';
		}
		
		function get_file_types( x ) {
			if( x === 'name' ) {
				return "Изображения";
			}
			
			if( x === 'list' ) {
				return "{$extensions}";
			}
		}
		
		function get_max_file_size() {
			return {$maxFileSize};	
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

<div align="center" id="uploadMessage">
	<table width="100%" height="90%">
	<tr>
		<td align="center" valign="middle"><div id="uploadMessageText">Загрузка в процессе. Подождите...<br><br>
  		<img src="[img://]editor/load_bar.gif" height="12" width="251" alt="" class="inset"><br><br>
  		<input class="button" type="button" value="Отменить" onClick="cancelUpload()"></div></td>
    </tr>
	</table>
</div>

<table class="tab" cellpadding="0" cellspacing="0" style="border:none">
<tr>
	<td class="row1" valign="top" style="width: 420px;">		
		<fieldset>
    	<legend>Выберите изображение</legend>
    	<div class="fieldset">        		
			<table class="tab" cellspacing="0" cellpadding="0" style="border:none; width: 465px;">
			<!--
			<tr>
				<td>
					<script type="text/javascript">
						AC_FL_RunContent( 'codebase','http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=7,0,19,0','width','464','height','180','src','[js://]editor/multiupload/upload','quality','high','pluginspage','http://www.macromedia.com/go/getflashplayer','movie','[js://]editor/multiupload/upload', 'id', 'flashMultiUpload' ); 						
					</script>				
				</td>
			</tr>
			-->
			
			<tr>
				<form style="display:inline" enctype="multipart/form-data" action="" method="post" onsubmit="oDialog.showUploadMessage()">
				
				<td class="row2">
					<input name="upload_field" id="upload_field" type="file" class="textinput" style="width: 100%;" title="Нажмите 'Browse...', выберите файл. Потом нажмите 'Загрузить'.">
				</td>			
				<td class="row_sub">
					<input type="submit" class="button" value="Загрузить" onClick="return oDialog.uploadCheck()">
				</td>
				
				<input type="hidden" name="[?act_var]" value="upload_image" />										
				</form>        							
    		</tr>
			
			
			<tr>
    			<td class="row2" nowrap colspan="2">
        			<input class="textinput" style="color: #949FBE; width: 420px;" type="text" name="imagename" value="{$path}" readonly="readonly">
        				        									
					<a href="{$createFolderURL}">
        				<img src="[img://]editor/newfolder.gif" width="23" height="22" alt="Создать директорию" title="Создать директорию" border="0" align="absmiddle">
        			</a>            			            			
	        	</td>
			</tr><tr>
				<td class="row1" colspan="2" style="font-size: 11px; font-weight: bold; width: 230px;">
					<a href="{$sortLink}">Имя:&nbsp;{$sortIcon}</a>					
				</td>
	        </tr><tr>  
	        	<td class="row2" colspan="2">      		
					<div class="scroll_list" style="height:342px; width:455px; overflow:auto;">
	        			{$content}
	        		</div>   
        		</td>
			</tr>
    		</table>
    	</div>
    	</fieldset>
    </td>       
</tr><tr>
	<td align="center" valign="top" class="row_sub">
		<form name="image_form" id="image_form" style="display:inline" onSubmit="return oDialog.insertImage()">
			<input id="ok" type="submit" value="OK">&nbsp;
			<input type="button" onClick="oDialog.closeParentWindow();" value="Отменить">&nbsp;
			<input type="button" disabled id="options" onClick="oDialog.moreOptions()" value="Детали.." style="color:silver">
		</form>
	</td>	
</tr>
</table>
</body>
</html>
EOF;
 }

    function thumbImage( $src, $width, $height ) { return <<<EOF
<img src="{$src}" width="{$width}" height="{$height}" border="0" />
EOF;
    }

    /**
     * Строка с изображением в таблице списка файлов
     * @param string $id
     * @param string $thumb @see thumbImage
     * @param string $name
     * @param string $type
     * @param string $size
     * @param string $weight
     * @param string $controls
     * @return string
     */
    function listImageRow( $id, $thumb, $name, $type, $size, $weight, $controls ) { return <<<EOF
<tr style="background: #EEF2F7" onmouseover="this.style.backgroundColor='#F5F9FD'" onmouseout="this.style.backgroundColor='#EEF2F7'">
	<td class="row" style="cursor: pointer">
		{$thumb}
	</td>
	<td class="row12" width="100%" id="img{$id}" onclick="oDialog.selectImage({$id})" colspan="2">
		<b>{$name}</b><br>{$type}<br>{$size}, {$weight}
	</td>
	<td class="row2" nowrap>{$controls}</td>
</tr>
EOF;
    }

    /**
     * Строка с изображением в таблице списка файлов
     * @param string $id
     * @param string $thumb @see thumbImage
     * @param string $name
     * @param string $type
     * @param string $size
     * @param string $weight
     * @return string
     */
    function listImageRowNoControls( $id, $thumb, $name, $type, $size, $weight ) { return <<<EOF
<tr style="background: #EEF2F7" onmouseover="this.style.backgroundColor='#F5F9FD'" onmouseout="this.style.backgroundColor='#EEF2F7'">
	<td class="row2" style="cursor: pointer" width="1%">
		{$thumb}
	</td>
	<td class="row12" width="100%" id="img{$id}" colspan="2" onclick="oDialog.selectImage({$id})">
		<b>{$name}</b><br>{$type}<br>{$size}, {$weight}
	</td>
</tr>
EOF;
    }

    function imageEditForm( $file_name, $img_id ) { return <<<EOF
<div align="center" style="width:400px">
    <form action="[mod://]" name="imageEditForm" method="post">
        <table class="tab" cellspacing="0">
        <tr>
            <td class="row0" colspan="2">Параметры изображения:</td>
        </tr><tr>
            <td class="row1" width="170px"><b>Имя изображения</b></td>
            <td class="row2" width="230px"><input type="text" name="file_name" value="{$file_name}" class="textinput"></td>
        </tr><tr>
            <td class="row_sub" colspan="2">
                <input class="button" type="submit" name="OK" value="OK" onclick="if( document.imageEditForm.file_name.value == '' ){ alert('Введите имя изображения'); return false }">
                &nbsp;					
                <a href="[mod://]"><u>Cancel</u></a>
            </td></tr>							
        </table>
                                                        
        <input type="hidden" name="[?act_var]" value="edit" />
        <input type="hidden" name="[?idx_var]" value="{$img_id}" />						
    </form>
</div>
EOF;
    }

    function optionsWindow( $v = array(), $jsobj ) { return <<<EOF
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
	<title>Свойства изображения</title>
	<link rel="stylesheet" href="[css://]style.main.css" type="text/css">
	<style type="text/css">
	p {
		margin:2px
	}
	</style>
	<script language="JavaScript" type="text/javascript" src="[js://]editor/_dialog.js"></script>
	<script language="JavaScript" type="text/javascript" src="[js://]editor/_dialog_image_options.js"></script>	
	<script type="text/javascript">		
		var oDialog = new Dialog('{$jsobj}');
		
		Dialog.prototype.init = function() {
			this.imgAlign 		= "{$v['align']}";
			this.imgURL	 		= "{$v['image']}";
			this.tagThumb 		= '{$v['thumb']}';
			this.imgRealWidth	=  {$v['real_width']};
			this.imgRealHeight	=  {$v['real_height']};
						 
			this.loadSettings();
			this.hideLoadMessage();			
		}
	</script>
</head>
<body style="margin:0;padding:0;" scroll="no" bgcolor="threedface" onLoad="oDialog.init()">

<div align="center" id="dialogLoadMessage" style="display:block;">
	<table width="100%" height="90%">
		<tr>
			<td align="center" valign="middle"><div id="loadMessage">Подождите...</div></td>
		</tr>
	</table>
</div>

<form name="image_form" id="image_form" style="display:inline" onsubmit="return oDialog.insertImage()">

<table class="tab" border="0" cellpadding="0" cellspacing="0" style="border:none">
<tr>
	<td class="row1" valign="top">
		<fieldset>
		<legend>Данные об изображении</legend>
		<div class="fieldset">
			<table class="tab" cellspacing="0" cellpadding="0" style="border:none; width:465px;">
			<tr> 
				<td class="row1" width="30%"><b>Адрес изображения</b></td>
				<td class="row2" width="70%" colspan="2">
					<input class="textinput" type="text" style="width:315px" name="imagename" id="imagename" value="{$v['image']}"> 
				</td>
			</tr><tr> 
				<td class="row1"><b>Толщина рамки</b></td>
				<td class="row2" colspan="2">
					<input class="textinput" type="text" style="width:80px" name="border" id="border" value="{$v['border']}">
				</td>
			</tr><tr> 
				<td class="row1"><b>Ширина</b></td>
				<td class="row2"> 
					<input class="textinput" type="text" style="width:80px" name="iwidth" id="iwidth" value="{$v['width']}">
				</td>
				<td class="row2" rowspan="2" width="200px">
					<img src="[img://]dialogs/brackets.gif" width="11" height="39" align="absmiddle" alt="">
					&nbsp;&nbsp;<a id="reset" href="javascript:oDialog.resetDimensions()" onMouseUp="this.blur()"><u>Сбросить</u></a>
				</td>
			</tr><tr> 
				<td class="row1"><b>Высота</b></td>
				<td class="row2">
					<input class="textinput" type="text" style="width:80px" name="iheight" id="iheight" value="{$v['height']}">
				</td>
			</tr><tr> 
				<td class="row1" height="24"><b>Заголовок</b></td>
				<td class="row2" colspan="2"> 
					<input class="textinput" type="text" style="width:315px" name="alt" id="alt" value="{$v['alt']}" title=""> 
				</td>
			</tr>
			</table>
		</div>
		</fieldset>
	</td>
</tr><tr> 
	<td class="row1" valign="top">	
		<fieldset>
		<legend>Размещение и отступы</legend>
		<div class="fieldset">
			<table class="tab" cellspacing="0" cellpadding="0" style="border:none; width: 465px;">
			<tr> 
				<td colspan="2" class="row1"><b>Обтекание текстом</b></td>
				<td colspan="2" class="row2">
					<select name="ialign" id="ialign" onChange="oDialog.updateStyle()" class="textinput">
						<option selected="selected" value="">По умолчанию</option>
						<option value="absmiddle">Absolute Middle</option>
						<option value="middle">Посередине</option>
						<option value="bottom">Понизу</option>
						<option value="top">Поверху</option>
						<option value="left">Слева</option>
						<option value="right">Справа</option>
						<option value="baseline">Baseline</option>
						<option value="texttop">Text Top</option>
						<option value="absbottom">Absolute Bottom</option>
					</select>
				</td>
			</tr><tr> 
				<td class="row1" width="25%"><b>Вверху</b></td>
				<td class="row2" width="25%"><input class="textinput" type="text" style="width:80%" name="mtop" id="mtop" size="4" value="{$v['mtop']}" onChange="oDialog.updateStyle()">&nbsp;px</td> 
				<td class="row1" width="25%"><b>Внизу</b></td>
				<td class="row2" width="25%"><input class="textinput" type="text" style="width:80%" name="mbottom" id="mbottom" size="4" value="{$v['mbottom']}" onChange="oDialog.updateStyle()">&nbsp;px</td>
			</tr><tr> 
				<td class="row1"><b>Слева</b></td>
				<td class="row2"><input class="textinput" type="text" style="width:80%" name="mleft" id="mleft" size="4" value="{$v['mleft']}" onChange="oDialog.updateStyle()">&nbsp;px</td> 
				<td class="row1"><b>Справа</b></td>
				<td class="row2"><input class="textinput" type="text" style="width:80%" name="mright" id="mright" size="4" value="{$v['mright']}" onChange="oDialog.updateStyle()">&nbsp;px</td>
			</tr><tr> 
				<td class="row1" colspan="4" style="padding:10px">
				<div id="stylepreview" style="padding:10px; width:420px; height:275px; overflow:hidden; background-color:#FFFFFF; font-family: Verdana; font-size:9px; border: 1px solid #ADB6CE;" class="previewWindow">
						Червяк и Алиса довольно долго созерцали друг друга в молчании: 
						наконец Червяк вынул изо рта чубук и сонно, медленно произнес: 
						<br>&mdash; Кто - ты - такая?
						<br>Хуже этого вопроса для первого знакомства он ничего бы не мог 
						придумать: Алиса сразу смутилась. 
						<br>&mdash; Видите ли... видите ли, сэр, я... просто не знаю, кто я сейчас 
						такая. Нет, я, конечно, примерно знаю, кто такая я была утром, когда 
						встала, но с тех нор я все время то такая, то сякая &mdash; словом, какая-то 
						не такая. &mdash; И она беспомощно замолчала. 
						<br>&mdash; Выражайся яснее! &mdash; строго сказал Червяк. &mdash; Как тебя прикажешь 
						понимать? 
						<br>&mdash; Я сама себя не понимаю, сэр, потому что получается, что я &mdash; 
						это не я! Видите, что получается? 
						<br>&mdash; Не вижу! &mdash; отрезал Червяк. 
						<br>&mdash; Простите меня, пожалуйста, &mdash; сказала Алиса очень вежливо, 
						&mdash; но лучше я, наверное, не сумею объяснить. Во-первых, я сама никак ничего не 
						пойму, а во-вторых, когда ты то большой, то маленький, то такой, то сякой то этакий &mdash; 
						все как-то путается, правда? 
						<br>&mdash; Неправда! &mdash; ответил Червяк. 
						<br>&mdash; Ну, может быть, с вами просто так еще не бывало, &mdash; сказала Алиса, &mdash; 
						а вот когда вы сами так начнете превращаться &mdash; а вам обязательно придется, знаете? &mdash; 
						сначала в куколку, потом в бабочку, вам тоже будет не по себе, да? 						
					</div>
				</td>
			</tr>
			</table>
		</div>
		</fieldset> 
	</td>
</tr><tr> 
	<td class="row_sub">
		<a href="[mod://]">&laquo;&nbsp;Назад</a>
		&nbsp;
		<input type="submit" value="ОК">
		&nbsp; 
		<input type="button" onClick="oDialog.closeTopWindow();" value="Отменить">
	</td>
</tr>
</table>

</form>
</body>
</html>
EOF;
    }

    function zoomWindow( $jsobj, $tabc ) { return <<<EOF
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
	<meta http-equiv="Pragma" content="no-cache">
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
	<title>Выбор увеличенного изображения</title>
	
	<link rel="stylesheet" href="[css://]style.main.css" type="text/css">
	
	<script language="JavaScript" type="text/javascript" src="[js://]admin.js"></script>		
	<script language="JavaScript" type="text/javascript" src="[js://]editor/_dialog.js"></script>
	<script language="JavaScript" type="text/javascript" src="[js://]editor/_dialog_image.js"></script>	

	<style type="text/css">
		p {
			margin:2px
		}
		.filename {
			width:180px;
			height:22px;
			overflow:hidden;
			white-space: nowrap;
		}
		.thumbFilename {
			width:285px;
			display: block;
			overflow:hidden;
			white-space: nowrap;
			background: left no-repeat;
			padding: 2px 0px 2px 100px;
			voice-family: "\"}\"";
			voice-family:inherit;
			width:185px;
		}
		.fileBar a {
			display: block;
			color: #000000;
			text-decoration: none;
		}
		.fileBar a:hover {
			display: block;
			color: #000000;
			text-decoration: none;
		}
		.fileBar a:active {
			display: block;
			color: #000000;
			background-color: transparent;
			text-decoration: none;
		}
		.fileBar a img {
			border-width: 0px;
		}
		#dialogLoadMessage table, #uploadMessage table {
			height: 437px;
		}
	</style>
		
	<script type="text/javascript">	
		var oDialog = new Dialog('{$jsobj}');
		
		Dialog.prototype.init = function() {
			this.hl 		= null;
			this.imgWidth	= 0; 
			this.imgHeight	= 0;
			this.imgURL		= '';
			this.modURL		= '[mod://]' + '&i=zoom';
						 
			this.hideLoadMessage();
		}
				
	//-->	
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
    	<legend>Выберите увеличенное изображение</legend>
    	<div class="fieldset">        		
			<table class="tab" cellspacing="0" cellpadding="0" style="border:none; width: 465px;">
			<tr>
	        	<td class="row2" colspan="2">      		
					<div class="scroll_list" style="height:575px; width:455px; overflow:auto;">
	        			{$tabc}							   					
	        		</div>   
        		</td>
			</tr>
    		</table>
    	</div>
    	</fieldset>
    </td>       
</tr><tr>
	<td align="center" valign="top" class="row_sub">
		<form name="image_form" id="image_form" style="display:inline" onSubmit="return oDialog.insertZoomImage()">
			<input id="ok" type="submit" value="OK">&nbsp;
			<input type="button" onClick="oDialog.closeParentWindow();" value="Отменить">&nbsp;
			<input type="button" disabled id="options" onClick="oDialog.moreOptions()" value="Детали.." style="color: silver; display: none;">
		</form>
	</td>	
</tr>
</table>
</body>
</html>
EOF;
    }
}