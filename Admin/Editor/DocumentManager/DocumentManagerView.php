<?php

namespace Admin\Editor\DocumentManager;

class DocumentManagerView {
    // $maxFileSize = {$this->sys->vars['max_doc_file_size']};
    // $folder = $params['folder'];
    function window( $tabc, $sortTableHeader, $path, $folder, $maxFileSize, $exts_str, $jsobj, $createFolderURL ) { return <<<EOF
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
	<title>Ссылка на документ</title>
	
	<link rel="stylesheet" href="[css://]style.main.css" type="text/css">

	<script language="JavaScript" type="text/javascript" src="[js://]admin.js"></script>
	<script language="JavaScript" type="text/javascript" src="[js://]editor/_dialog.js"></script>
	<script language="JavaScript" type="text/javascript" src="[js://]editor/_dialog_document.js"></script>
	<script language="JavaScript" type="text/javascript" src="[js://]editor/multiupload/run.js"></script>	
	<script type="text/javascript">	
		var oDialog = new Dialog('{$jsobj}');
		
		Dialog.prototype.openFolder = function( id ) {
			document.location.replace( '[mod://]&folder=' + id );
		}

		function do_redirect() {
			window.location = '[mod://]&folder={$folder}';
		}
		
		function flash_multiupload_complete(){					
			setTimeout( "do_redirect()", 1000 );
		}
		
		function flash_get_uploading_url(){
			return '[mod://]&i=upload_file&folder={$folder}';
		}
		
		function get_file_types( x ) {
			if( x == 'name' ) {
				return "Документы";
			}
			
			if( x == 'list' ) {
				return "{$exts_str}";
			}
		}
		
		function get_max_file_size( x ) {
			return {$maxFileSize};	
		}			
	</script>		
	
	<base target="_self" />
</head>

<body scroll="no" onLoad="oDialog.init()" style="margin:0;padding:0;background: #EFF3F7;">
	
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
        <input class="button" type="button" value="Отменить" onClick="oDialog.cancelUpload()"></div></td>
    </tr>
    </table>
</div>

<table class="tab" cellpadding="0" cellspacing="0" style="border:none">
<tr>
	<td class="row1" valign="top" rowspan="3" style="width: 400px;"> 
		<fieldset>
    	<legend>Выберите документ</legend>
    	<div class="fieldset">        		
			<table class="tab" cellspacing="0" cellpadding="0" style="border:none; width:392px;">
    		<tr>
    			<td class="row2" nowrap>
        			<input class="textinput" style="color: #949FBE; width: 350px;" type="text" name="imagename" value="{$path}" readonly="readonly">
        				        									
					<a href="{$createFolderURL}">
        				<img src="[img://]editor/newfolder.gif" width="23" height="22" alt="Создать директорию" title="Создать директорию" border="0" align="absmiddle">
        			</a>            			            			
	        	</td>
			</tr><tr>
				<td class="row1">        		        
	        		{$sortTableHeader}
	        	</td>
	        </tr><tr>  
	        	<td class="row2">      		
	        		<div class="scroll_list" style="height:290px; width:380px; overflow:auto; background: #F7FBFF;">
	        			{$tabc}		   					
	        		</div>   
        		</td>
			</tr>
    		</table>
    	</div>
    	</fieldset>
    </td>
    
    <!-- -->
    
  	<td valign="top" class="row1" style="height:85px; width:290px;">
		<fieldset>				
        <legend>Загрузить с диска</legend>
		<div class="fieldset">			
            <form style="display:inline" enctype="multipart/form-data" action="" method="post" onsubmit="oDialog.showUploadMessage()">        	
                
                <table class="tab" cellspacing="0" cellpadding="0" style="border:none;">
                <tr>
                    <td class="row1" width="40px"><b>Файл</b> &nbsp;</td>
                    <td class="row2" width="*">
                        <input name="upload_field" id="upload_field" type="file" class="textinput" title="Нажмите 'Browse...', выберите файл. Потом нажмите 'Загрузить'.">
                    </td>
                </tr><tr>
                    <td class="row_sub" colspan="2" style="padding:2px">
                        <input type="submit" class="button" value="Загрузить">
                    </td>
                </tr>
                </table>		
                <input type="hidden" name="[?act_var]" value="upload_file">						
                
            </form>			
		</div>
    	</fieldset>
    	<br />
	</td>
</tr><tr>
	<td valign="top" class="row1" style="height:150px"> 					
		<fieldset>
		<legend>Информация о документе</legend>
		<div class="fieldset">
			<table class="tab" cellspacing="0" cellpadding="0" style="border:none; width: 470px;">
			<tr>
				<td class="row1" width="30%" nowrap><b>Имя</b></td>
				<td class="row2" width="70%"><input id="doc_name" value="" class="textinput"></td>
	        </tr><tr>
				<td class="row1" nowrap><b>URL</b></td>
				<td class="row2"><input id="doc_url" value="" class="textinput"></td>
	        </tr><tr>        	
				<td class="row1" nowrap><b>Размер</b></td>
				<td class="row2" id="doc_size">&nbsp;</td>
	        </tr><tr>
				<td class="row1" nowrap><b>Загружен</b></td>
				<td class="row2" id="doc_date">&nbsp;</td>			
	        </tr><tr>
				<td class="row1" nowrap><b>Доступ</b></td>
				<td class="row2" id="doc_group">&nbsp;</td>        	
	        </tr>
			</table>
		</div>
        </fieldset>
	</td>    	
</tr><tr>
	<td align="center" valign="top" class="row_sub">    		
	    <input id="ok" type="submit" onClick="oDialog.insertLink()" value="OK">
	    &nbsp;
	    <input type="button" onClick="oDialog.closeTopWindow();" value="Отменить">	            		
	</td>	
</tr>
</table>
</div>
</body>
</html>
EOF;
    }

    function sortTableHeader( $nameLink, $nameIcon, $typeLink, $typeIcon ) { return <<<EOF
<table width="360px" border="0" cellspacing="0" cellpadding="0">
    <tr>
        <td style="font-size: 11px; font-weight: bold; width: 230px;">				            	
            <a href="{$nameLink}">
                Название:&nbsp;
                <img src="{$nameIcon}" width="8" height="7" border="0">
            </a>
        </td>
        <td style="font-size: 11px; font-weight: bold; width: 100px;">				            	
            <a href="{$typeLink}">
                Тип:&nbsp;
                <img src="{$typeIcon}" width="8" height="7" border="0">
            </a>
        </td>				            
    </tr>
</table>
EOF;
    }

//=========================================================================
//
// Таблицы списка документов
//
//=========================================================================

    function list_table( $content, $js_array ) { return <<<EOF
<script>
	var aDocName  = new Array();
	var aDocURL   = new Array();
	var aDocSize  = new Array();
	var aDocDate  = new Array();
	var aDocGroup = new Array();
	
	{$js_array}		
		
	function openFolder( id ) {
		window.location = '[mod://]&folder=' + id;
	}
	
</script>

<table class="tab" cellspacing="0" style="border:none;cursor:default;">
{$content}    
</table>
EOF;
    }

//=========================================================================
//
// Строка таблицы списка файлов
//
//=========================================================================

    function list_row( $v ) { return <<<EOF
<tr style="background: #EEF2F7;" onmouseover="this.style.backgroundColor='#F5F9FD'" onmouseout="this.style.backgroundColor='#EEF2F7'">
	<td id="doc{$v['id']}row1" class="row12" onclick="{$v['onclick']}" style="cursor:{$v['cursor']}" nowrap><img src="{$v['icon']}" boder="0"></td>	
	<td id="doc{$v['id']}row2" class="row12" onclick="{$v['onclick']}" style="cursor:{$v['cursor']}" width="99%">{$v['name']}</td>
	<td class="row12" onclick="{$v['onclick']}" style="cursor:{$v['cursor']}" nowrap>{$v['type']}</td>
	<td class="row12" nowrap>{$v['controls']}</td>	
</tr>
EOF;
    }
}
