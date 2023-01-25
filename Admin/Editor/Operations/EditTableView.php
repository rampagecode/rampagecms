<?php

namespace Admin\Editor\Operations;

class EditTableView {
    /**
     * @param $content
     * @param $jsobj
     * @return string
     */
    function window( $content, $jsobj ) { return <<<EOF
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
	<title>Редактировать свойства таблицы, ячейки или строки</title>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<link rel="stylesheet" href="[css://]style.main.css" type="text/css">
	<style type="text/css">	
        #tab_one {
            position 	: absolute; 
            text-align 	: center; 
            left 		: 0;
            background	: #EFF3F7;		
        }
        #tab_two {
            position 	: absolute; 
            text-align 	: center; 
            left 		: 0;
            visibility 	: hidden;
            background	: #EFF3F7;	 		
        }
        #tab_three {
            position 	: absolute; 
            text-align 	: center;
            left 		: 0;
            visibility 	: hidden;
            background	: #EFF3F7;
        }
	</style>
	
	<script language="JavaScript" type="text/javascript" src="[js://]editor/_dialog.js"></script>
	<script language="JavaScript" type="text/javascript" src="[js://]editor/_dialog_table_edit.js"></script>
	<script type="text/javascript">	
		var oDialog = new Dialog('{$jsobj}');
	</script>	
</head>

<body style="margin:0;padding:0;background-color:#EFF3F7;" onLoad="oDialog.init();">
	
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
     * @param $tab1
     * @param $tab2
     * @param $tab3
     * @param $preview
     * @return string
     */
    function content( $tab1, $tab2, $tab3, $preview ) { return <<<EOF
<form id="edit_table_form" name="edit_table_form" style="display:inline" onSubmit="return oDialog.apply();">
    <table class="tab" id="tab_table" width="422" cellspacing="0" cellpadding="0" style="border:none; height:30px; cursor:pointer;">
    <tr> 
        <td id="tbar1" class="row1" align="center" onClick="oDialog.changeView(0)" nowrap><b>Ячейка</b></td>
        <td id="tbar2" class="row1" align="center" onClick="oDialog.changeView(1)" nowrap><b>Строка</b></td>
        <td id="tbar3" class="row1" align="center" onClick="oDialog.changeView(2)" nowrap><b>Таблица</b></td>
    </tr>
    </table>
    <div id="tab_one">
        {$tab1}
    </div>
    <div id="tab_two">
        {$tab2}
    </div>
    <div id="tab_three">
        {$tab3}
    </div>
    <div style="position: absolute; top: 380; left:0;" id="botm">
        {$preview}
    </div>
</form>
EOF;
    }

    /**
     * Cell properties
     * @return string
     */
    function tab1( $jsobj ) { return <<<EOF
<table class="tab" cellpadding="0" cellspacing="0" style="border:none; height:300px;">
<tr> 
    <td class="row1" valign="top">
        <fieldset style="width:406px;">
        <legend>Размер ячейки</legend>
        <div class="fieldset">						
            <table class="tab" cellpadding="0" cellspacing="0" style="border:none; width:384px;">
            <tr> 
                <td class="row1" width="25%"><b>Ширина</b></td>
                <td class="row2" width="25%"><input type="text" name="td_width" id="td_width" value="" class="textinput" style="width:60px" onChange="oDialog.updateStyle()"></td>
                <td class="row1" width="25%"><b>Высота</b></td>													 							
                <td class="row2" width="25%"><input type="text" name="td_height" id="td_height" value="" class="textinput" style="width:60px" onChange="oDialog.updateStyle()"></td>
            </tr>
            </table>
        </div>
        </fieldset>  
        <div style="width: 2px; font-size: 4px;">&nbsp;</div>
        <fieldset style="width:406px;">
        <legend>Выравнивание текста</legend>
        <div class="fieldset">
            <table class="tab" cellpadding="0" cellspacing="0" style="border:none; width:384px;">
            <tr> 
                <td class="row1" width="50%"><b>Вертикальное</b></td>
                <td class="row2" width="50%"> 
                    <select class="textinput" name="td_valign" id="td_valign" style="width:130px" onChange="oDialog.updateStyle()">
                        <option value="">По умолчанию</option>
                        <option value="top">Поверху</option>
                        <option value="middle">Посередине</option>
                        <option value="bottom">Понизу</option>
                        <option value="baseline">Baseline</option>
                    </select> 
                </td>
            </tr><tr> 
                <td class="row1" width="140"><b>Горизонтальное</b></td>
                <td class="row2"> 
                    <select class="textinput" name="td_align" id="td_align" style="width:130px" onChange="oDialog.updateStyle()">
                        <option value="">По умолчанию</option>
                        <option value="left">Слева</option>
                        <option value="center">По центру</option>
                        <option value="right">Справа</option>
                    </select> 
                </td>
            </tr>
            </table>
        </div>
        </fieldset>										
        
        <!-- ЦВЕТ РАМОК -->
        
        <div style="width: 2px; font-size: 4px;">&nbsp;</div>
        <fieldset style="width:406px;">
        <legend>Цвет рамок</legend>
        <div class="fieldset">
            
            <table class="tab" cellpadding="0" cellspacing="0" style="border:none; width:384px;">
            <tr> 
                <td class="row1" width="25%">
                    <b>Сверху</b>&nbsp; 
                    <button type="button" onClick="oDialog.colordialog(5,'{$jsobj}');" style="background: #F7FBFF; border:0; font-size:11px; padding:1; cursor: pointer;" title="Выбрать цвет"> 
                        <span id="td_border_top_chosen_color" style="border: 1px solid #ADB6CE;">&nbsp;&nbsp;&nbsp;&nbsp;</span>
                    </button>
                    <input type="hidden" name="td_border_top_color" id="td_border_top_color" value="" onChange="oDialog.updateStyle()"> 
                </td>
                
                <td class="row1" width="25%">
                    <b>Снизу</b>&nbsp; 
                    <button type="button" onClick="oDialog.colordialog(6,'{$jsobj}');" style="background: #F7FBFF; border:0; font-size:11px; padding:1; cursor: pointer;" title="Выбрать цвет"> 
                        <span id="td_border_bottom_chosen_color" style="border: 1px solid #ADB6CE;">&nbsp;&nbsp;&nbsp;&nbsp;</span>
                    </button>
                    <input type="hidden" name="td_border_bottom_color" id="td_border_bottom_color" value="" onChange="oDialog.updateStyle()"> 
                </td>

                <td class="row1" width="25%">
                    <b>Справа</b>&nbsp; 
                    <button type="button" onClick="oDialog.colordialog(7,'{$jsobj}');" style="background: #F7FBFF; border:0; font-size:11px; padding:1; cursor: pointer;" title="Выбрать цвет"> 
                        <span id="td_border_right_chosen_color" style="border: 1px solid #ADB6CE;">&nbsp;&nbsp;&nbsp;&nbsp;</span>
                    </button>
                    <input type="hidden" name="td_border_right_color" id="td_border_right_color" value="" onChange="oDialog.updateStyle()"> 
                </td>

                <td class="row1" width="25%">
                    <b>Слева</b>&nbsp; 
                    <button type="button" onClick="oDialog.colordialog(8,'{$jsobj}');" style="background: #F7FBFF; border:0; font-size:11px; padding:1; cursor: pointer;" title="Выбрать цвет"> 
                        <span id="td_border_left_chosen_color" style="border: 1px solid #ADB6CE;">&nbsp;&nbsp;&nbsp;&nbsp;</span>
                    </button>
                    <input type="hidden" name="td_border_left_color" id="td_border_left_color" value="" onChange="oDialog.updateStyle()"> 
                </td>
                
            </tr>
            </table>

        </div>
        </fieldset>
        
        <!-- ТОЛЩИНА РАМОК -->
        
        <div style="width: 2px; font-size: 4px;">&nbsp;</div>
        <fieldset style="width:406px;">
        <legend>Толщина рамок</legend>
        <div class="fieldset">
            
            <table class="tab" cellpadding="0" cellspacing="0" style="border:none; width:384px;">
            <tr> 
                <td class="row1" width="25%">
                    <b>Сверху</b>&nbsp; 
                    <input type="text" name="td_border_top_width" id="td_border_top_width" value="1" class="textinput" style="width:20px" onChange="oDialog.updateStyle()">
                </td>
                
                <td class="row1" width="25%">
                    <b>Снизу</b>&nbsp;
                    <input type="text" name="td_border_bottom_width" id="td_border_bottom_width" value="1" class="textinput" style="width:20px" onChange="oDialog.updateStyle()">
                </td>

                <td class="row1" width="25%">
                    <b>Справа</b>&nbsp; 
                    <input type="text" name="td_border_right_width" id="td_border_right_width" value="1" class="textinput" style="width:20px" onChange="oDialog.updateStyle()"> 
                </td>

                <td class="row1" width="25%">
                    <b>Слева</b>&nbsp;
                    <input type="text" name="td_border_left_width" id="td_border_left_width" value="1" class="textinput" style="width:20px" onChange="oDialog.updateStyle()">
                </td>
                
            </tr>
            </table>

        </div>
        </fieldset>					
        
        <!-- ЦВЕТА -->
        
        <div style="width: 2px; font-size: 4px;">&nbsp;</div>
        <fieldset style="width:406px;">
        <legend>Цвета</legend>
        <div class="fieldset">						
            <table class="tab" cellpadding="0" cellspacing="0" style="border:none; width:384px;">
            <tr> 
                <td class="row1" width="50%"><b>Цвет фона</b></td>
                <td class="row2" width="50%"> 
                    <button type="button" onClick="oDialog.colordialog(4,'{$jsobj}');" style="background: #F7FBFF; border:0; font-size:11px; padding:1; cursor: pointer;" title="Выбрать цвет"> 
                        <span id="tdbgchosencolor" style="border: 1px solid #ADB6CE;">&nbsp;&nbsp;&nbsp;&nbsp;</span> ...
                    </button>
                    <input type="hidden" name="td_bgcolor" id="td_bgcolor" value="" onChange="oDialog.updateStyle()"> 
                </td>
            </tr>
            </table>
        </div>
        </fieldset>
    </td>
</tr>
</table>
EOF;
    }

    /**
     * Row properties
     * @return string
     */
    function tab2( $jsobj ) { return <<<EOF
<table class="tab" width="420" cellpadding="0" cellspacing="0" style="border:none; height: 300px;"> 
<tr> 
    <td class="row1" valign="top">					
        <fieldset style="width:406px;">
        <legend>Выравнивание текста</legend>
        <div class="fieldset">						
            <table class="tab" cellpadding="0" cellspacing="0" style="border:none; width:384px;">
            <tr> 
                <td class="row1" width="50%"><b>Вертикальное</b></td>
                <td class="row2" width="50%"> 
                    <select class="textinput" name="tr_valign" id="tr_valign" style="width:130px" onChange="oDialog.updateStyle()">
                        <option value="">По умолчанию</option>
                        <option value="top">Поверху</option>
                        <option value="middle">Посередине</option>
                        <option value="bottom">Понизу</option>
                        <option value="baseline">Baseline</option>
                    </select> 
                </td>
            </tr><tr> 
                <td class="row1" width="50%"><b>Горизонтальное</b></td>
                <td class="row2" width="50%">  
                    <select class="textinput" name="tr_align" id="tr_align" style="width:130px" onChange="oDialog.updateStyle()">
                        <option value="">По умолчанию</option>
                        <option value="left">Слева</option>
                        <option value="center">По центру</option>
                        <option value="right">Справа</option>
                    </select> 
                </td>
            </tr>
            </table>						
        </div>
        </fieldset>
        <div style="width: 2px; font-size: 4px;">&nbsp;</div>
        <fieldset style="width:406px;">
        <legend>Границы</legend>
        <div class="fieldset">						
            <table class="tab" cellpadding="0" cellspacing="0" style="border:none; width:384px;">
            <tr> 
                <td class="row1" width="50%"><b>Цвет фона</b></td>
                <td class="row2" width="50%">
                    <button type="button" onClick="oDialog.colordialog(3,'{$jsobj}');" style="background: #F7FBFF; border:0; font-size:11px; padding:1; cursor: pointer;" title="Выбрать цвет"> 
                        <span id="trbgchosencolor" style="border: 1px solid #ADB6CE;">&nbsp;&nbsp;&nbsp;&nbsp;</span> ...
                    </button>            								
                    <input type="hidden" name="tr_bgcolor" id="tr_bgcolor" value="" onChange="oDialog.updateStyle()"> 
                </td>
            </tr>
            </table>
        </div>
        </fieldset>         
        
        <div style="height:110px"></div>
    </td>
</tr>
</table>
EOF;
    }

    /**
     * Table properties
     * @return string
     */
    function tab3( $jsobj ) { return <<<EOF
<table class="tab" width="420" cellpadding="0" cellspacing="0" style="border:none; height: 300px;">
<tr> 
    <td class="row1" valign="top">  
        <fieldset style="width:406px;">
        <legend>Отступы</legend>
        <div class="fieldset">						
            <table class="tab" cellpadding="0" cellspacing="0" style="border:none; width:384px;">
            <tr> 
                <td class="row1" width="30%"><b>Интервалы</b></td>
                <td class="row2" width="20%"><input type="text" name="table_cellspacing" id="table_cellspacing" class="textinput" style="width:60px" onChange="oDialog.updateStyle()"></td>
                <td class="row1" width="30%"><b>Отступы в ячейке</b></td>
                <td class="row2" width="20%"><input type="text" name="table_cellpadding" id="table_cellpadding" class="textinput" style="width:60px" onChange="oDialog.updateStyle()"> </td>
            </tr>
            </table>
        </div>
        </fieldset>
        <div style="width: 2px; font-size: 4px;">&nbsp;</div>
        <fieldset style="width:406px;">
        <legend>Размер таблицы</legend>
        <div class="fieldset">						
            <table class="tab" cellpadding="0" cellspacing="0" style="border:none; width:384px;">
            <tr> 
                <td class="row1" width="30%"><b>Ширины таблицы</b></td>
                <td class="row2" width="20%"><input type="text" name="table_width" id="table_width" class="textinput" style="width:60px"></td>
                <td class="row1" width="30%"><b>Высота таблицы</b></td>
                <td class="row2" width="20%"><input type="text" name="table_height" id="table_height" class="textinput" style="width:60px"></td>
            </tr>
            </table>
        </div>
        </fieldset>
        <div style="width: 2px; font-size: 4px;">&nbsp;</div>
        <fieldset style="width:406px;">
        <legend>Выравнивание</legend>
        <div class="fieldset">
            <table class="tab" cellpadding="0" cellspacing="0" style="border:none; width:384px;"> 
            <tr> 
                <td class="row1" width="50%"><b>Выравнивание на странице</b></td>
                <td class="row2" width="50%"> 
                    <select name="table_align" id="table_align" class="textinput" style="width:120px" onChange="oDialog.updateStyle()">
                        <option value="">По умолчанию</option>
                        <option value="left">Слева</option>
                        <option value="center">По центру</option>
                        <option value="right">Справа</option>
                    </select> 
                </td>
            </tr>
            </table>
        </div>
        </fieldset>
        <div style="width: 2px; font-size: 4px;">&nbsp;</div>
        <fieldset style="width:406px;">
        <legend>Границы</legend>
        <div class="fieldset">
            <table class="tab" cellpadding="0" cellspacing="0" style="border:none; width:384px;">
            <tr> 
                <td class="row1" width="36%"><b>Размеры рамок</b></td>
                <td class="row2" width="14%"><input type="text" name="table_border" id="table_border" class="textinput" style="width:40px" onChange="oDialog.updateStyle()"></td>
                <td class="row1" width="36%"><b>Сжатые границы</b></td>
                <td class="row2" width="14%"><input type="checkbox" name="collapse" id="collapse" value="ON" onClick="oDialog.updateStyle()"></td>
            </tr><tr> 
                <td class="row1"><b>Цвет фона</b></td>
                <td class="row2">
                    <button type="button" onClick="oDialog.colordialog(2,'{$jsobj}');" style="background: #F7FBFF; border:0; font-size:11px; padding:1; cursor: pointer;" title="Выбрать цвет"> 
                        <span id="tablebgchosencolor" style="border: 1px solid #ADB6CE;">&nbsp;&nbsp;&nbsp;&nbsp;</span> ...
                    </button>
                    
                    <input type="hidden" name="table_bgcolor" id="table_bgcolor" value="" onChange="oDialog.updateStyle()"> 
                </td>
                <td class="row1"><b>Цвет рамок</b></td>
                <td class="row2"> 
                    <button type="button" onClick="oDialog.colordialog(1,'{$jsobj}');" style="background: #F7FBFF; border:0; font-size:11px; padding:1; cursor: pointer;" title="Выбрать цвет"> 
                        <span id="tableborderchosencolor" style="border: 1px solid #ADB6CE;">&nbsp;&nbsp;&nbsp;&nbsp;</span> ...
                    </button>
                                                    
                    <input type="hidden" name="table_bordercolor" id="table_bordercolor" value="" onChange="oDialog.updateStyle()"> 
                </td>
            </tr>							
            </table>
        </div>
        </fieldset>
    </td>
</tr>
</table>
EOF;
    }

    /**
     * @return string
     */
    function preview() { return <<<EOF
<table class="tab" cellpadding="0" cellspacing="0" height="40" width="420" style="border:none">
<tr> 
    <td class="row1">
        <fieldset class="fieldsetLine">
        <legend>Просмотр стиля:</legend>

        <div class="fieldset">		
            <div class="row1" id="tbl_background" style="text-align: center; padding:6px; background-color:#FFFFFF; overflow:hidden; border: 1px solid #ADB6CE;">
                    
                <table height="70" cellpadding="0" cellspacing="0" border="0" width="360px">
                <tr> 
                    <td> 
                        <table cellspacing="0" cellpadding="0" border="0" height="100%" style="font-size:10px">
                        <tr> 
                            <td width="33%" valign="top" align="right">Таблица&nbsp;</td>
                        </tr><tr> 
                            <td width="34%" valign="middle" nowrap>Строка <font face="Tahoma">&rarr;</font> &nbsp;</td>
                        </tr>
                        </table>
                    </td>
                    <td width="100%" valign="middle"> 
                        <table cellspacing="0" cellpadding="3" height="60" width="95%" id="tbl" border="0" style="font-size:10px">
                        <tr> 
                            <td width="34%">&nbsp;</td>
                            <td width="33%">&nbsp;</td>
                            <td width="33%">&nbsp;</td>
                        </tr>
                        <tr id="tbl_tr"> 
                            <td width="34%">&nbsp;</td>
                            <td width="33%" id="tbl_td">Ячейка</td>
                            <td width="33%">&nbsp;</td>
                        </tr>
                        </table>
                    </td>
                </tr>
                </table>
            </div>
        </div>		
        </fieldset>

    </td>
</tr><tr> 
    <td class="row_sub" height="100%" class="text" width="100%"> 
        <input type="submit" value="Применить">
        &nbsp;&nbsp; 
        <input type="button" onClick="oDialog.closeWindow();" value="Отменить">
    </td>
</tr>		
</table>
EOF;
    }
}