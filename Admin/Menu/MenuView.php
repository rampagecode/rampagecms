<?php

namespace Admin\Menu;

class MenuView {

    function main( $head, $body ) { return <<<EOF
<!doctype html>
<html>
    <head>
        {$head}
    </head>    
    <body style="margin: 10px; text-align: left;">
        {$body}
    </body>
</html> 
EOF;
    }

    function head( $title, $styles, $scripts ) { return <<<EOF
<title>{$title}</title>
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta http-equiv="content-type" content="text/html; charset=utf-8">	
<meta name="robots" content="none">
<meta name="document-state" content="dynamic">
<meta http-equiv="cache-control" content="no-cache">
<meta http-equiv="pragma" content="no-cache">

{$styles}
{$scripts}
EOF;
    }

    function styles() { return <<<EOF
<link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.2/dist/css/bootstrap.min.css" integrity="sha384-Zenh87qX5JnK2Jl0vWa8Ck2rdkQ2Bzep5IDxbcnCeuOxjzrPF/et3URy9Bv1WTRi" crossorigin="anonymous">	
<link rel="stylesheet" type="text/css" href="http://extjs.cachefly.net/ext-2.2.1/resources/css/ext-all.css" />
<link rel="stylesheet" type="text/css" href="[css://]style.main.css">
EOF;
    }

    function scripts() { return <<<EOF
<script type="text/javascript">
    var jsGlobalURL 	= "http://{$_SERVER['SERVER_NAME']}[base://]";
    var adsessGlobalURL = "http://{$_SERVER['SERVER_NAME']}[js://]";
</script>
<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.8.3/jquery.min.js"></script>
<script type="text/javascript" src="http://extjs.cachefly.net/builds/ext-cdn-778.js"></script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-OERcA2EqjJCMA+/3y+gxIOqMEjwtxJY7qPCqsdltbNJuaOe923+mo//f6V8Qbsw3" crossorigin="anonymous"></script>
<script type="text/javascript" src="[js://]admin.js"></script>    
<script type="text/javascript" src="[js://]jquery/jquery.form.js"></script>		
<script type="text/javascript" src="[js://]mainMenu.js"></script>
<script type="text/javascript" src="[js://]modalDialog.js"></script>
<script type="text/javascript" src="[js://]editor/_dialog.js"></script>
<script type="text/javascript" src="[js://]editor/_dialog_contents.js"></script>
<script type="text/javascript">
    pages_shown 				= 0;
    pages_array 				= [];
    st_val 						= 0;		
    var global_img_url 			= '[img://]';
    var global_js_path  		= "[js://]";
    var global_site_menu_path 	= "[base://]&u=x&a=sitetree&i=menu";
    var global_base_url 		= "[base://]";				
    Ext.BLANK_IMAGE_URL 		= '[img://]s.gif';		
</script>
EOF;
    }

    function body( $title, $tabs, $content, $logs, $notice ) { return <<<EOF
<div class="page_header">
    <b>{$title}</b>
    <div style="display: inline; margin-left: 20px;">{$notice}</div>
</div>

<!--
<div style="position: absolute; left: 10px; top: 35px; text-align: left; font-size: 11px;">
	Инструкция по работе с CMS:	
	<a target="_blank" href="/manual.pdf" style="color: #315869; text-decoration: underline; font-size: 11px;">PDF</a>,
	<a target="_blank" href="/manual.doc" style="color: #315869; text-decoration: underline; font-size: 11px;">Word</a>.
</div>
-->

{$tabs}
{$content}
{$logs}
EOF;
    }

    function log( $content ) { return <<<EOF
<br/>
<textarea style="width: 99%; height: 999px;">{$content}</textarea>
EOF;
    }

    function with_menu( $menu, $content ) { return <<<EOF
<table id="tabwrap" width="100%" height="90%" cellspacing="0" cellpadding="0">
<tr>
<td id="mainmenu" valign="top" nowrap style="border-right: 1px dashed #518697;">
	<div style="background: #fff; padding: 10px; border-left: 1px solid #fff; border-right: 1px solid #fff; height: 100%">
		{$menu}
	</div>
</td>
<!--<td id="menu_divider">&nbsp;</td>-->
<td id="premainwnd">
	<table width="100%" height="100%" border="0" cellpadding="0" cellspacing="0">
		<tr><td id="mainwnd">{$content}</td></tr>
	</table>
</td>
</tr>
</table>

EOF;
    }

    function without_menu( $content ) { return <<<EOF
<!--<div id="only_premainwnd">-->
	<div id="only_mainwnd">
		{$content}
	</div>
<!--</div>-->
EOF;
    }

    function noticeMsg( $text ) { return <<<EOF
<span style="padding: 4px 8px; text-align: center; border: 1px solid #ffe3ac; background: #fff5e0; color: #315869; font-size: 11px;">{$text}</span>
EOF;
    }

    function no_mod_active() { return <<<EOF
<div style="font-size: 26px; color: #E6EDF6; font-weight: bold; font-family: Arial;">
Выберите нужный раздел в меню слева.
</div>
EOF;
    }


    function site_link( $link ) {
        if( $link == '' || $link == '/') {
            return '';
        } else {
            return "<a target=\"_blank\" href=\"{$link}\">Перейти на редактируемую страницу</a>";
        }
    }

    /**
     * Вывод сообщения об успешном завершении какого-либо действия
     * @param string $text
     * @return string
     */
    function showMessage( $text ) {
        return <<<EOF
<div align="center">
	<br />
	<fieldset style="border: 1px solid #1C9BCD; width: 600px;">
		<legend style="color: #1C9BCD; font-weight: bold;" align="center">&nbsp;Сообщение:&nbsp;</legend>	
		<div style="color: #1C9BCD; padding: 6px; text-align: center;">{$text}</div>
	</fieldset>
	<br /><br />
</div>
EOF;
    }
}