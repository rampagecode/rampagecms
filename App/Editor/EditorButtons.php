<?php

namespace App\Editor;

class EditorButtons {

    function b_cut() { return <<<EOF
<td><button style="background-position: -574px -3px;" onClick="oEd_##name##.execCut(); return false;" title="##cut##" class="wpReady" cid="forecolor" type="btn"></button></td>
EOF;
    }

    function b_copy() { return <<<EOF
<td><button style="background-position: -640px -3px;" onClick="oEd_##name##.execCopy(); return false;" title="##copy##" class="wpReady" cid="forecolor" type="btn"></button></td>
EOF;
    }

    function b_paste() { return <<<EOF
<td><button style="background-position: -552px -3px;" onClick="oEd_##name##.execPaste(); return false;" title="##paste##" class="wpReady" cid="forecolor" type="btn"></button></td>
EOF;
    }

    function b_print() { return <<<EOF
<td><button style="background-position: -618px -3px;" onClick="oEd_##name##.edit_object.print(); return false;" title="##print##" class="wpReady" cid="forecolor" type="btn"></button></td>
EOF;
    }

    function b_find() { return <<<EOF
<td><button style="background-position: -596px -3px;" onClick="oEd_##name##.openFind(); return false;" title="##find_and_replace##" class="wpReady" cid="forecolor" type="btn"></button></td>
EOF;
    }

    function b_spacer() { return <<<EOF
<td><img class="wpSpacer" width="1" height="22" src="##imgs##spacer.gif" alt="" /></td>
EOF;
    }

    function b_undo() { return <<<EOF
<td><button style="background-position: -530px -3px;" onClick="oEd_##name##.doUndo(); return false;" title="##undo##" class="wpReady" cid="undo" type="btn"></button></td>
EOF;
    }

    function b_redo() { return <<<EOF
<td><button style="background-position: -508px -3px;" onClick="oEd_##name##.doRedo(); return false;" title="##redo##" class="wpReady" cid="redo" type="btn"></button></td>
EOF;
    }

    function b_table() { return <<<EOF
<td><button style="background-position: -420px -3px;" onClick="oEd_##name##.open_table_window(this); return false;" title="##insert_table##" class="wpReady" cid="forecolor" type="btn"></button></td>
EOF;
    }

    function b_image() { return <<<EOF
<td><button style="background-position: -486px -3px;" onClick="oEd_##name##.open_image_window(this); return false;" title="##insert_image##" class="wpReady" cid="insertimage" type="btn"></button></td>
EOF;
    }

    function b_link() { return <<<EOF
<td><button style="background-position: -464px -3px;" onClick="oEd_##name##.open_hyperlink_window(this); return false;" title="##insert_hyperlink##" class="wpReady" cid="forecolor" type="btn"></button></td>
EOF;
    }

    function b_doc() { return <<<EOF
<td><button style="background-position: -440px -3px;" onClick="oEd_##name##.open_document_window(this); return false;" title="##document_link##" class="wpReady" cid="forecolor" type="btn"></button></td>
EOF;
    }

    function b_bookmark() { return <<<EOF
<td><button style="background-position: -332px -3px;" onClick="oEd_##name##.open_bookmark_window(this); return false;" title="##insert_bookmark##" class="wpReady" cid="insertimage" type="btn"></button></td>
EOF;
    }

    function b_char() { return <<<EOF
<td><button style="background-position: -354px -3px;" onClick="oEd_##name##.open_spec_char_window(this); return false;" title="##special_characters##" class="wpReady" cid="inserthorizontalrule" type="btn"></button></td>
EOF;
    }

    function b_ruler() { return <<<EOF
<td><button style="background-position: -288px -3px;" onClick="oEd_##name##.open_rule_window(this); return false;" title="##horizontal_line##" class="wpReady" cid="inserthorizontalrule" type="btn"></button></td>
EOF;
    }

    function b_format_list() { return <<<EOF
<td>
	<table id="##name##_format_list" class="DropDownList" style="##format_list_style##" onClick="oEd_##name##.show_menu('format', this)" border="0" cellpadding="0" cellspacing="1" title="##paragraph_format##">
	<tr> 
		<td width="70"><div id="##name##_format_list-text">##format##</div></td>
		<td width="10"><img unselectable="on" src="##imgs##down_arrow.gif" width="10" height="14" alt="" /></td>
	</tr>
	</table>					
	<iframe ##secure##width="272" height="202" id="##name##_format_frame" frameborder="0" class="frameDropBorder"></iframe>
</td>
EOF;
    }

    function b_class_list() { return <<<EOF
<td>
	<table id="##name##_class_menu" class="DropDownList" style="##class_list_style##" onClick="oEd_##name##.show_menu('class', this)" border="0" cellpadding="0" cellspacing="1" title="##style_class##">
	<tr> 
		<td width="50"><div id="##name##_class_menu-text">##class##</div></td>
		<td width="10"><img src="##imgs##down_arrow.gif" width="10" height="14" alt="" /></td>
	</tr>
	</table>						
	<iframe ##secure##width="272" height="141" id="##name##_class_frame" frameborder="0" class="frameDropBorder"></iframe>
</td>
EOF;
    }

    function b_font_list() { return <<<EOF
<td>
	<table id="##name##_font-face" class="DropDownList" style="##font_list_style##" onClick="oEd_##name##.show_menu('font', this)" border="0" cellpadding="0" cellspacing="1" title="##font_face##">
	<tr> 
		<td width="110"><div id="##name##_font-face-text">##font##</div></td>
		<td width="10"><img src="##imgs##down_arrow.gif" width="10" height="14" alt="" /></td>
	</tr>
	</table>							
	<iframe ##secure##width="150" height="141" id="##name##_font_frame" frameborder="0" class="frameDropBorder"></iframe>
</td>
EOF;
    }

    function b_size_list() { return <<<EOF
<td>
	<table id="##name##_font_size" class="DropDownList" style="##size_list_style##" onClick="oEd_##name##.show_menu('size', this)" border="0" cellpadding="0" cellspacing="1" title="##font_size##">
	<tr> 
		<td width="50"><div id="##name##_font_size-text">##size##</div></td>
		<td width="10"><img src="##imgs##down_arrow.gif" width="10" height="14" alt="" /></td>
	</tr>
	</table>							
	<iframe ##secure##width="112" height="202" id="##name##_size_frame" frameborder="0" class="frameDropBorder"></iframe>
</td>		
EOF;
    }

    function b_bold() { return <<<EOF
<td><button style="background-position: -222px -3px;" onClick="oEd_##name##.callFormatting('bold'); return false;" title="##bold##" class="wpReady" cid="bold" type="btn"></button></td>
EOF;
    }

    function b_italic() { return <<<EOF
<td><button style="background-position: -200px -3px;" onClick="oEd_##name##.callFormatting('italic'); return false;" title="##italic##" class="wpReady" cid="italic" type="btn"></button></td>
EOF;
    }

    function b_underline() { return <<<EOF
<td><button style="background-position: -178px -3px;" onClick="oEd_##name##.callFormatting('underline'); return false;" title="##underline##" class="wpReady" cid="underline" type="btn"></button></td>
EOF;
    }

    function b_left() { return <<<EOF
<td><button style="background-position: -156px -3px;" onClick="oEd_##name##.callFormatting('justifyleft'); return false;" title="##align_left##" class="wpReady" cid="justifyleft" type="btn"></button></td>
EOF;
    }

    function b_center() { return <<<EOF
<td><button style="background-position: -112px -3px;" onClick="oEd_##name##.callFormatting('justifycenter'); return false;" title="##align_center##" class="wpReady" cid="justifycenter" type="btn"></button></td>
EOF;
    }

    function b_right() { return <<<EOF
<td><button style="background-position: -134px -3px;" onClick="oEd_##name##.callFormatting('justifyright'); return false;" title="##align_right##" class="wpReady" cid="justifyright" type="btn"></button></td>
EOF;
    }

    function b_justify() { return <<<EOF
<td><button style="background-position: -90px -3px;" onClick="oEd_##name##.callFormatting('justifyfull'); return false;" title="##justify##" class="wpReady" cid="justifyfull" type="btn"></button></td>
EOF;
    }

    function b_ol() { return <<<EOF
<td><button style="background-position: -68px -3px;" onClick="oEd_##name##.callFormatting('insertorderedlist'); return false;" title="##numbering##" class="wpReady" cid="insertorderedlist" type="btn"></button></td>
EOF;
    }

    function b_ul() { return <<<EOF
<td><button style="background-position: -46px -3px;" onClick="oEd_##name##.callFormatting('insertunorderedlist'); return false;" title="##bullets##" class="wpReady" cid="insertunorderedlist" type="btn"></button></td>
EOF;
    }

    function b_outdent() { return <<<EOF
<td><button style="background-position: -2px -3px;" onClick="oEd_##name##.callFormatting('outdent'); return false;" title="##decrease_indent##" class="wpReady" cid="outdent" type="btn"></button></td>
EOF;
    }

    function b_indent() { return <<<EOF
<td><button style="background-position: -24px -3px;" onClick="oEd_##name##.callFormatting('indent'); return false;" title="##increase_indent##" class="wpReady" cid="indent" type="btn"></button></td>
EOF;
    }

    function b_forecolor() { return <<<EOF
<td><button style="background-position: -398px -3px;" onClick="oEd_##name##.colordialog(this,'forecolor'); return false;" title="##font_color##" class="wpReady" cid="forecolor" type="btn"></button></td>
EOF;
    }

    function b_backcolor() { return <<<EOF
<td><button style="background-position: -376px -3px;" onClick="oEd_##name##.colordialog(this,'hilitecolor'); return false;" title="##highlight##" class="wpReady" cid="hilitecolor" type="btn"></button></td>
EOF;
    }

    function b_textcut() { return <<<EOF
<!--
<td><img style="background-image: url(##imgs##alltoolbar.gif); background-position: -308px 0;" cid="textcut" class="wpReady" width="22" height="22" onclick="oEd_##name##.insertTextCut()" src="##imgs##spacer.gif" alt="Обрезание текста.." title="Обрезание текста.." type="btn" onMouseOver="oEd_##name##.m_over(this);" onMouseOut="oEd_##name##.m_out(this);" onMouseDown="oEd_##name##.m_down(this);" onMouseUp="oEd_##name##.m_up(this);"></td>
-->
EOF;
    }

    function b_hiddenblock() { return <<<EOF
<td><button style="background-position: -266px -3px;" onClick="oEd_##name##.insertHiddenBlock(); return false;" title="Скрытый блок" class="wpReady" cid="redo" type="btn"></button></td>
EOF;
    }

    function b_youtube() { return <<<EOF
<td><button style="background-position: -244px -3px;" onClick="oEd_##name##.insertYoutubeVideo(); return false;" title="Вставить видео с YouTube..." class="wpReady" cid="redo" type="btn"></button></td>
EOF;
    }
}
