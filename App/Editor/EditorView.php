<?php

namespace App\Editor;

class EditorView {
    /**
     * DROP-DOWN FONT MENU
     * @return string
     */
    function font_menu() { return <<<EOF
<div onClick="parent.oEd_##name##.change_font( 'Arial' );" title="Arial" style="font-family:'Arial'"><nobr>Arial</nobr></div>
<div onClick="parent.oEd_##name##.change_font( 'Times New Roman' );" title="Times New Roman" style="font-family:'Times New Roman'"><nobr>Times New Roman</nobr></div>
<div onClick="parent.oEd_##name##.change_font( 'Tahoma' );" title="Tahoma" style="font-family:'Tahoma'"><nobr>Tahoma</nobr></div>
<div onClick="parent.oEd_##name##.change_font( 'Courier' );" title="Courier" style="font-family:'Courier'"><nobr>Courier</nobr></div>
<div onClick="parent.oEd_##name##.change_font( 'Georgia' );" title="Georgia" style="font-family:'Georgia'"><nobr>Georgia</nobr></div>
<div onClick="parent.oEd_##name##.change_font( 'Verdana' );" title="Verdana" style="font-family:'Verdana'"><nobr>Verdana</nobr></div>
<div onClick="parent.oEd_##name##.change_font( 'Geneva' );" title="Geneva" style="font-family:'Geneva'"><nobr>Geneva</nobr></div>
EOF;
    }

    /**
     * DROP-DOWN FORMAT MENU
     * @return string
     */
    function format_menu() { return <<<EOF
<div onClick="parent.oEd_##name##.change_format('&lt;##lineReturns##&gt;');"><nobr><##lineReturns##>##normal##</##lineReturns##></nobr></div>
<div onClick="parent.oEd_##name##.change_format('&lt;h1&gt;');"><nobr><h1>##heading_1##</h1></nobr></div>
<div onClick="parent.oEd_##name##.change_format('&lt;h2&gt;');"><nobr><h2>##heading_2##</h2></nobr></div>
<div onClick="parent.oEd_##name##.change_format('&lt;h3&gt;');"><nobr><h3>##heading_3##</h3></nobr></div>
<div onClick="parent.oEd_##name##.change_format('&lt;h4&gt;');"><nobr><h4>##heading_4##</h4></nobr></div>
<div onClick="parent.oEd_##name##.change_format('&lt;h5&gt;');"><nobr><h5>##heading_5##</h5></nobr></div>
<div onClick="parent.oEd_##name##.change_format('&lt;h6&gt;');"><nobr><h6>##heading_6##</h6></nobr></div>
<div onClick="parent.oEd_##name##.change_format('&lt;pre&gt;');"><nobr><pre>##pre_formatted##</pre></nobr></div>
<div onClick="parent.oEd_##name##.change_format('&lt;address&gt;');"><nobr><address>Address</address><nobr></div>
EOF;
    }

    /**
     * DROP-DOWN FONT SIZE MENU
     * @return string
     */
    function size_menu() { return <<<EOF
<div onClick="parent.oEd_##name##.change_font_size('1');"><font size="1">##1##</font></div>
<div onClick="parent.oEd_##name##.change_font_size('2');"><font size="2">##2##</font></div>
<div onClick="parent.oEd_##name##.change_font_size('3');"><font size="3">##3##</font></div>
<div onClick="parent.oEd_##name##.change_font_size('4');"><font size="4">##4##</font></div>
<div onClick="parent.oEd_##name##.change_font_size('5');"><font size="5">##5##</font></div>
<div onClick="parent.oEd_##name##.change_font_size('6');"><font size="6">##6##</font></div>
<div onClick="parent.oEd_##name##.change_font_size('7');"><font size="7">##7##</font></div>
EOF;
    }

    function init() { return <<<EOF
<script language="JavaScript" type="text/javascript">
<!-- begin 1st instance only -->
	var isGecko  = '##is_gecko##';		
	var language = new Object();	
	language['guidelines_hidden'] 		= '##guidelines_hidden##';
	language['guidelines_visible'] 		= '##guidelines_visible##';
	language['place_cursor_in_table'] 	= '##place_cursor_in_table##';
	language['only_split_merged_cells'] = '##only_split_merged_cells##';
	language['no_cell_right'] 			= '##no_cell_right##';
	language['different_row_spans'] 	= '##different_row_spans##';
	language['no_cell_below'] 			= '##no_cell_below##';
	language['different_column_spans'] 	= '##different_column_spans##';
	language['select_hyperlink_text'] 	= '##select_hyperlink_text##';
	language['upgrade'] 				= '##upgrade##';
	language['format'] 					= '##format##';
	language['font'] 					= '##font##';
	language['class'] 					= '##class##';
	language['size'] 					= '##size##';
	
	var configuration = new Object();
<!-- end 1st instance only -->
    
	configuration['##name##'] = new Object();		
	configuration['##name##']['adsess_url'] 		= '##adsess_url##';
	configuration['##name##']['images_url']			= '##imgs##';	
	configuration['##name##']['is_ie50'] 			= ##is_ie50##;	
	configuration['##name##']['url_3xi_css']		= '##url_3xi_css##';
	configuration['##name##']['url_3xi_img']		= '##url_3xi_img##';
	configuration['##name##']['encoding'] 			= '##encoding##';
	configuration['##name##']['xhtml_lang'] 		= '##xhtml_lang##';
	configuration['##name##']['useXHTML'] 		 	= '##usexhtml##';
	configuration['##name##']['baseURLurl'] 		= '##baseURLurl##';
	configuration['##name##']['baseURL'] 		 	= '##baseURL##';
	configuration['##name##']['instance_img_dir'] 	= '##instance_img_dir##';
	configuration['##name##']['instance_doc_dir'] 	= '##instance_doc_dir##';
	configuration['##name##']['domain1']            = new RegExp('(href=|src=|action=)"##domain##',"gi");
	configuration['##name##']['domain2']            = new RegExp('(href=|src=|action=)"##domain2##',"gi");
	configuration['##name##']['stylesheet'] 		= ##stylesheet##
	configuration['##name##']['imenu_height'] 	 	= ##imenu_height##;
	configuration['##name##']['bmenu_height'] 	 	= ##bmenu_height##;
	configuration['##name##']['smenu_height'] 	 	= ##smenu_height##;
	configuration['##name##']['tmenu_height'] 	 	= ##tmenu_height##;	
	configuration['##name##']['links'] 			 	= ##links##;	
	configuration['##name##']['usep'] 			 	= ##usep##;		
	configuration['##name##']['color_swatches'] 	= '##color_swatches##';
	configuration['##name##']['border_visible'] 	= ##guidelines##;
	configuration['##name##']['doctype'] 		 	= '##doctype##';
	configuration['##name##']['charset'] 		 	= '##charset##';
</script>
EOF;
    }

    function start() { return <<<EOF
<script language="JavaScript" type="text/javascript">	
	var oEd_##name## = new Editor( '##name##' );	
	oEd_##name##.init();

	$("button.wpReady").mouseover( function(){ oEd_##name##.m_over(this); });
	$("button.wpReady").mouseout ( function(){ oEd_##name##.m_out(this);  });
	$("button.wpReady").mousedown( function(){ oEd_##name##.m_down(this); });
	$("button.wpReady").mouseup  ( function(){ oEd_##name##.m_up(this);   }); 
</script>

<noscript>
	<p>##javascript_warning##</p>
</noscript>
EOF;
    }

    function imageContextMenu() { return <<<EOF
<div id="##name##_imageMenu" style="display:none"> 
    <div class="wpContextBorder"> 
        <table class="wpContextTable" style="height:##imenu_height##px;" border="0" cellpadding="0" cellspacing="0">
        <!-- begin image -->
        <tr cid="insertimage" onClick="oEd_##name##.open_image_window(this)" onMouseOver="oEd_##name##.menuover(this)" onMouseOut="oEd_##name##.menuout(this)"> 
            <td class="wpContextCellOne"><img class="wpDisabled" width="22" height="22" alt="" src="##imgs##image.gif" /></td>
            <td class="wpContextCellTwo">&nbsp;##image_properties##</td>
        </tr>
        <!-- end image -->
        <!-- begin zoom -->
        <tr cid="insertimage" onClick="oEd_##name##.open_image_zoom_window(this)" onMouseOver="oEd_##name##.menuover(this)" onMouseOut="oEd_##name##.menuout(this)"> 
            <td class="wpContextCellOne"><img class="wpDisabled" width="22" height="22" alt="" src="##imgs##zoom.gif" /></td>
            <td class="wpContextCellTwo">&nbsp;##image_zoom##</td>
        </tr>			
        <!-- end zoom -->
        <!-- begin link -->
        <tr cid="forecolor"> 
            <td class="wpContextCellOne"><img src="##imgs##spacer.gif" width="1" height="1" alt="" /></td>
            <td align="right"><img style="background-color:threedshadow; margin: 3px 0px"  src="##imgs##spacer.gif" width="200" height="1" alt="" /></td>
        </tr>
        <tr cid="createlink" onClick="oEd_##name##.open_hyperlink_window(this)" onMouseOver="oEd_##name##.menuover(this)" onMouseOut="oEd_##name##.menuout(this)"> 
            <td class="wpContextCellOne"><img width="22" height="22" alt="" src="##imgs##link.gif" /></td>
            <td class="wpContextCellTwo">&nbsp;##insert_hyperlink##</td>
        </tr>
        <!-- end link -->
        <!-- begin document -->
        <tr cid="createlink" onClick="oEd_##name##.open_document_window(this)" onMouseOver="oEd_##name##.menuover(this)" onMouseOut="oEd_##name##.menuout(this)"> 
            <td class="wpContextCellOne"><img width="22" height="22" alt="" src="##imgs##doc_link.gif" /></td>
            <td class="wpContextCellTwo">&nbsp;##document_link##</td>
        </tr>
        <!-- end document -->
        </table>
    </div>
</div>
EOF;
    }

    function bookmarkContextMenu() { return <<<EOF
<div id="##name##_bookmarkMenu" style="display:none"> 
    <div class="wpContextBorder"> 
        <table class="wpContextTable" style="height:##bmenu_height##px;" border="0" cellpadding="0" cellspacing="0">
        <!-- begin bookmark -->
        <!-- begin pasteword -->
        <tr cid="forecolor"> 
            <td class="wpContextCellOne"><img src="##imgs##spacer.gif" width="1" height="1" alt="" /></td>
            <td align="right"><img style="background-color:threedshadow; margin: 3px 0px"  src="##imgs##spacer.gif" width="200" height="1" alt="" /></td>
        </tr>
        <!-- end pasteword -->
        <tr cid="insertimage" onClick="oEd_##name##.open_bookmark_window(this)" onMouseOver="oEd_##name##.menuover(this)" onMouseOut="oEd_##name##.menuout(this)"> 
            <td class="wpContextCellOne"><img class="wpDisabled" width="22" height="22" alt="" src="##imgs##bookmark.gif" /></td>
            <td class="wpContextCellTwo">&nbsp;##bookmark_properties##</td>
        </tr>
        <!-- end bookmark -->
        </table>
    </div>
</div>
EOF;
    }

    function tableContextMenu() { return <<<EOF
<div id="##name##_tableMenu" style="display:none"> 
    <div class="wpContextBorder"> 
        <table class="wpContextTable" style="height:##tmenu_height##px;" border="0" cellpadding="0" cellspacing="0">
        <!-- begin tbl -->
        <!-- begin edittable -->
        <!-- begin pasteword -->
        <tr cid="forecolor" > 
            <td class="wpContextCellOne"><img src="##imgs##spacer.gif" width="1" height="1" alt="" /></td>
            <td align="right"><img style="background-color:threedshadow; margin: 3px 0px"  src="##imgs##spacer.gif" width="240" height="1" alt="" /></td>
        </tr>
        <!-- end pasteword -->
        <tr cid="forecolor" onClick="oEd_##name##.open_table_editor()" onMouseOver="oEd_##name##.menuover(this)" onMouseOut="oEd_##name##.menuout(this)"> 
            <td class="wpContextCellOne"><img width="22" height="22" alt="" src="##imgs##edittable.gif" /></td>
            <td class="wpContextCellTwo">&nbsp;##table_properties##</td>
        </tr>
        <tr cid="forecolor" onClick="oEd_##name##.processRow('addabove')" onMouseOver="oEd_##name##.menuover(this)" onMouseOut="oEd_##name##.menuout(this)"> 
            <td class="wpContextCellOne"><img width="22" height="22" alt="" src="##imgs##insrowabove.gif" /></td>
            <td class="wpContextCellTwo">&nbsp;##add_row_above##</td>
        </tr>
        <tr cid="forecolor" onClick="oEd_##name##.processRow('addbelow')" onMouseOver="oEd_##name##.menuover(this)" onMouseOut="oEd_##name##.menuout(this)"> 
            <td class="wpContextCellOne"><img width="22" height="22" alt="" src="##imgs##insrowbelow.gif" /></td>
            <td class="wpContextCellTwo">&nbsp;##add_row_below##</td>
        </tr>
        <tr cid="forecolor" onClick="oEd_##name##.processColumn('addleft')" onMouseOver="oEd_##name##.menuover(this)" onMouseOut="oEd_##name##.menuout(this)"> 
            <td class="wpContextCellOne"><img width="22" height="22" alt="" src="##imgs##inscolleft.gif" /></td>
            <td class="wpContextCellTwo">&nbsp;##add_column_left##</td>
        </tr>
        <tr cid="forecolor" onClick="oEd_##name##.processColumn('addright')" onMouseOver="oEd_##name##.menuover(this)" onMouseOut="oEd_##name##.menuout(this)"> 
            <td class="wpContextCellOne"><img width="22" height="22" alt="" src="##imgs##inscolright.gif" /></td>
            <td class="wpContextCellTwo">&nbsp;##add_column_right##</td>
        </tr>
        <tr cid="forecolor" onClick="oEd_##name##.processRow('remove')" onMouseOver="oEd_##name##.menuover(this)" onMouseOut="oEd_##name##.menuout(this)"> 
            <td class="wpContextCellOne"><img width="22" height="22" alt="" src="##imgs##delrow.gif" /></td>
            <td class="wpContextCellTwo">&nbsp;##delete_row##</td>
        </tr>
        <tr cid="forecolor" onClick="oEd_##name##.processColumn('remove')" onMouseOver="oEd_##name##.menuover(this)" onMouseOut="oEd_##name##.menuout(this)"> 
            <td class="wpContextCellOne"><img width="22" height="22" alt="" src="##imgs##delcol.gif" /></td>
            <td class="wpContextCellTwo">&nbsp;##delete_column##</td>
        </tr>
        <tr cid="mergeright" onClick="oEd_##name##.mergeRight()" onMouseOver="oEd_##name##.menuover(this)" onMouseOut="oEd_##name##.menuout(this)"> 
            <td class="wpContextCellOne"><img width="22" height="22" alt="" src="##imgs##mrgcellh.gif" /></td>
            <td class="wpContextCellTwo">&nbsp;##merge_right##</td>
        </tr>
        <tr cid="mergebelow" onClick="oEd_##name##.mergeDown()" onMouseOver="oEd_##name##.menuover(this)" onMouseOut="oEd_##name##.menuout(this)"> 
            <td class="wpContextCellOne"><img width="22" height="22" alt="" src="##imgs##mrgcelld.gif" /></td>
            <td class="wpContextCellTwo">&nbsp;##merge_below##</td>
        </tr>
        <tr cid="unmergeright" onClick="oEd_##name##.unMergeRight()" onMouseOver="oEd_##name##.menuover(this)" onMouseOut="oEd_##name##.menuout(this)"> 
            <td class="wpContextCellOne"><img width="22" height="22" alt="" src="##imgs##spltcellh.gif" /></td>
            <td class="wpContextCellTwo">&nbsp;##unmerge_right##</td>
        </tr>
        <tr cid="unmergebelow" onClick="oEd_##name##.unMergeDown()" onMouseOver="oEd_##name##.menuover(this)" onMouseOut="oEd_##name##.menuout(this)"> 
              <td class="wpContextCellOne"><img width="22" height="22" alt="" src="##imgs##unmrgcelld.gif" /></td>
            <td class="wpContextCellTwo">&nbsp;##unmerge_below##</td>
        </tr>
        <!-- end edittable -->
        <!-- end tbl -->
        <!-- begin link -->
        <tr cid="forecolor" > 
            <td class="wpContextCellOne"><img src="##imgs##spacer.gif" width="1" height="1" alt="" /></td>
            <td align="right"><img style="background-color:threedshadow; margin: 3px 0px"  src="##imgs##spacer.gif" width="240" height="1" alt="" /></td>
        </tr>
        <tr cid="createlink" onClick="oEd_##name##.open_hyperlink_window(this)" onMouseOver="oEd_##name##.menuover(this)" onMouseOut="oEd_##name##.menuout(this)"> 
            <td class="wpContextCellOne"><img width="22" height="22" alt="" src="##imgs##link.gif" /></td>
            <td class="wpContextCellTwo">&nbsp;##insert_hyperlink##</td>
        </tr>
        <!-- end link -->
        <!-- begin document -->
        <tr cid="createlink" onClick="oEd_##name##.open_document_window(this)" onMouseOver="oEd_##name##.menuover(this)" onMouseOut="oEd_##name##.menuout(this)"> 
            <td class="wpContextCellOne"><img width="22" height="22" alt="" src="##imgs##doc_link.gif" /></td>
            <td class="wpContextCellTwo">&nbsp;##document_link##</td>
        </tr>
        <!-- end document -->
        </table>
    </div>
</div>
EOF;
    }

    function defaultContextMenu() { return <<<EOF
<div id="##name##_standardMenu" style="display:none"> 
    <div class="wpContextBorder"> 
        <table class="wpContextTable" style="height:##smenu_height##px;" border="0" cellpadding="0" cellspacing="0">
        <!-- begin link -->
        <!-- begin pasteword -->
        <tr cid="forecolor"> 
            <td class="wpContextCellOne"><img src="##imgs##spacer.gif" width="1" height="1" alt="" /></td>
            <td align="right"><img style="background-color:threedshadow; margin: 3px 0px"  src="##imgs##spacer.gif" width="200" height="1" alt="" /></td>
        </tr>
        <!-- end pasteword -->
        <tr cid="createlink" onClick="oEd_##name##.open_hyperlink_window(this)" onMouseOver="oEd_##name##.menuover(this)" onMouseOut="oEd_##name##.menuout(this)"> 
            <td class="wpContextCellOne"><img width="22" height="22" alt="" src="##imgs##link.gif" /></td>
            <td class="wpContextCellTwo">&nbsp;##insert_hyperlink##</td>
        </tr>
        <!-- end link -->
        <!-- begin document -->
        <tr cid="createlink" onClick="oEd_##name##.open_document_window(this)" onMouseOver="oEd_##name##.menuover(this)" onMouseOut="oEd_##name##.menuout(this)"> 
            <td class="wpContextCellOne"><img width="22" height="22" alt="" src="##imgs##doc_link.gif" /></td>
            <td class="wpContextCellTwo">&nbsp;##document_link##</td>
        </tr>
        <!-- end document -->
        </table>
    </div>
</div>
EOF;
    }

    function editor() {
        $init = $this->init();
        $start = $this->start();
        $imageContextMenu = $this->imageContextMenu();
        $bookmarkContextMenu = $this->bookmarkContextMenu();
        $tableContextMenu = $this->tableContextMenu();
        $defaultContextMenu = $this->defaultContextMenu();

        return <<<EOF
<!-- begin 1st instance only -->
<link rel="stylesheet" href="##url_3xi_css##style.editor.css" type="text/css" />
<!-- end 1st instance only -->
<div id="##name##_load_message" style="display:block; position:absolute; z-index:1000;"> 
	<table width="##width##" height="##height2##">
		<tr> 
			<td valign="middle" style="text-align: center"><div style="margin: auto; background-color:#666666; border:1px solid #333333; padding: 10px; width: 100px; color:#FFFFFF; font-family:verdana,arial,helvetica,sans-serif; font-size:12px; font-weight:bold">##please_wait##&nbsp;</div></td>
		</tr>
	</table>
</div>
{$init}
<!-- begin 1st instance only -->
<script language="JavaScript" type="text/javascript" src="##js_lib_url##_editor.js"></script>
<script language="JavaScript" type="text/javascript" src="##js_lib_url##_editor_dialogs.js"></script>
<script language="JavaScript" type="text/javascript" src="##js_lib_url##_editor_formatting.js"></script>
<script language="JavaScript" type="text/javascript" src="##js_lib_url##_editor_views.js"></script>
<script language="JavaScript" type="text/javascript" src="##js_lib_url##_editor_table_ops.js"></script>
<script language="JavaScript" type="text/javascript" src="##js_lib_url##_editor_undoredo.js"></script>
<script language="JavaScript" type="text/javascript" src="##js_lib_url##_editor_gecko.js"></script>
<script language="JavaScript" type="text/javascript" src="##js_lib_url##_editor_dialogs_gecko.js"></script>
<!-- end 1st instance only -->
<script language="JavaScript" type="text/javascript">	
	function ##name##_mouseUpHandler(evt) 	{ oEd_##name##.mouseUpHandler( evt ); }	
	function ##name##_keyHandler(evt) 		{ oEd_##name##.keyHandler( evt ); }	
	function ##name##_contextHandler(evt) 	{ oEd_##name##.context( evt ); }
	function ##name##_submitHandler(evt) 	{ oEd_##name##.prepare_submission( evt ); }
	function ##name##_beforeCopyHandler(evt)	{ oEd_##name##.beforeCopyHandler(evt); }
	function ##name##_onPasteHandler(evt) 		{ oEd_##name##.onPasteHandler(evt); }
</script>

<table id="##name##_container" width="##width##" class="wpContainer" border="0" cellpadding="0" cellspacing="0">
<tr valign="bottom"> 
	<td> 
		<div id="##name##_tab_one" style="display:block;">
    		<div style="float: left;">
    			<table class="wpToolbar" style="##toolbar1_style##" border="0" cellpadding="0" cellspacing="0">
	       		<tr>
    				<td><!-- dots --></td>
	       			##toolbar1##
				    <td>&nbsp;</td>
                </tr>
                </table>
            </div>
            <div style="float: left;">
                <table class="wpToolbar" style="##toolbar2_style##" border="0" cellpadding="0" cellspacing="0">
                <tr>
                    <td><!-- dots -->&nbsp;</td>
                    ##toolbar2##
                </tr>
                </table>
            </div>
            
			<iframe id="##name##_editFrame" class="wpEditFrame" height="##height##" frameborder="0"></iframe>			
		</div>		
		<div id="##name##_tab_two" style="display:none;"> 
			<textarea class="wpHtmlEditArea" style="height:##height2##px;" id="##name##" name="##original_name##" wrap="off">##htmlCode##</textarea>
		</div>		
		<div id="##name##_tab_three" style="display:none;"> 
			<iframe id="##name##_previewFrame" class="wpHtmlEditArea" style="height:##height3##px;" frameborder="0"></iframe>
		</div>
		
		<table id="##name##_tab_table" class="wpTabHolder" width="100%" border="0" cellspacing="2" cellpadding="0">
		<tr> 
			<td width="7" class="wpTabNoTab"><img src="##imgs##spacer.gif" width="1" height="1" alt="" /></td>
			<!-- begin tab -->
			<!-- begin design -->
			<td id="##name##_designTab" class="wpTButtonUp" onMouseDown="oEd_##name##.on_mouse_down_tab(this)" onClick="oEd_##name##.showDesign();">&nbsp;<img src="##imgs##normal.gif" width="10" height="10" alt="" />&nbsp;##design##&nbsp;&nbsp;</td>
			<!-- end design -->
			<!-- begin html -->
			<td id="##name##_sourceTab" class="wpTButtonDown" onMouseDown="oEd_##name##.on_mouse_down_tab(this)" onClick="oEd_##name##.showCode();">&nbsp;<img src="##imgs##html.gif" width="10" height="10" alt="" />&nbsp;##html_code##&nbsp;&nbsp;</td>
			<!-- end html -->
			<!-- begin preview -->
			<td id="##name##_previewTab" class="wpTButtonDown" onMouseDown="oEd_##name##.on_mouse_down_tab(this)" onClick="oEd_##name##.showPreview();">&nbsp;<img src="##imgs##preview.gif" width="10" height="10" alt="" />&nbsp;##preview##&nbsp;&nbsp;</td>
			<!-- end preview -->
			<!-- end tab -->
			<td width="100%" class="wpTabNoTab" style="display:none">
				<div class="wpStyled" align="right">
					<span id="##name##_moremessages">##br_tag##</span> &nbsp;
					<span id="##name##_messages" class="wpBorderMessage" onClick="oEd_##name##.toggle_table_borders(this);" title="##toggle_guidelines##" onMouseOver="this.style.textDecoration='underline'" onMouseOut="this.style.textDecoration='none'"></span>
				</div>
			</td>
			<td width="100%" class="wpTabNoTab">&nbsp;</td>			
			<td id="##name##_plusTab" class="wpTButtonDown" onMouseDown="oEd_##name##.on_mouse_down_tab(this)" onClick="oEd_##name##.resizeWindow( 1, this, ##step_inc##, ##step_dec## );">&nbsp;&nbsp;+&nbsp;&nbsp;</td>			
			<td id="##name##_minusTab" class="wpTButtonDown" onMouseDown="oEd_##name##.on_mouse_down_tab(this)" onClick="oEd_##name##.resizeWindow( 0, this, ##step_inc##, ##step_dec## );">&nbsp;&nbsp;-&nbsp;&nbsp;</td>						
		</tr>
		</table>
				
		<div id="##name##_hidden">
			{$defaultContextMenu}
			{$imageContextMenu}
			{$bookmarkContextMenu}					
			{$tableContextMenu}
			<!-- drop-down menus -->			
			<div id="##name##_font-menu" style="display:none; border: 1px solid #000000"> 
				<div onClick="parent.oEd_##name##.change_font( 'null' );">##default##</div>
				##font_menu## 
			</div>
			<div id="##name##_size-menu" style="display:none; border: 1px solid #000000"> 
				<div onClick="parent.oEd_##name##.change_font_size( 'null' );">##default##</div>
				##size_menu##
			</div>
			<div id="##name##_format-menu" style="display:none; border: 1px solid #000000"> 
				##format_menu## 
			</div>
			<!--
			<div id="##name##_class-menu" style="display:none"> 
				<div class="off" onClick="parent.wp_change_class(parent.wp_current_obj,'wp_none');off(this)" onMouseOver="on(this)" onMouseOut="off(this)">##clear_styles##</div>
				##class_menu## 
			</div>
			-->
		</div>
	</td>
</tr>
</table>
{$start}
EOF;
    }
}