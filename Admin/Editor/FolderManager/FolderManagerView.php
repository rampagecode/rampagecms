<?php

namespace Admin\Editor\FolderManager;

class FolderManagerView {

    /**
     * Строка с изображением в таблицы списка файлов
     * @param int $id
     * @param string $name
     * @param string $controls
     * @return string
     */
    function listFolderRow( $id, $name, $controls ) { return <<<EOF
<tr style="background:#EEF2F7" onmouseover="this.style.backgroundColor='#F5F9FD'" onmouseout="this.style.backgroundColor='#EEF2F7'">
	<td colspan="3" class="row12" width="100%" style="cursor:pointer" onClick="oDialog.openFolder({$id})">
		<table width="100%" cellspacing="0" cellpadding="0" style="font-size:10px">
		<tr>
			<td width="1%"><img src="[img://]vfs/folder.gif"></td>
			<td width="99%">&nbsp;{$name}&nbsp;<span style="color:#000; font-size: 8px;"><i>({$id})</i></span></td>
		</tr>
		</table>
	</td>
	<td class="row2" nowrap>{$controls}</td>
</tr>							
EOF;
    }

    /**
     * Строка с изображением в таблицы списка файлов
     * @param int $id
     * @param string $name
     * @return string
     */
    function listFolderRowNoControls( $id, $name ) { return <<<EOF
<tr style="background:#EEF2F7" onmouseover="this.style.backgroundColor='#F5F9FD'" onmouseout="this.style.backgroundColor='#EEF2F7'">
	<td colspan="4" class="row12" width="100%" style="cursor:pointer" onClick="oDialog.openFolder({$id})">
		<table width="100%" cellspacing="0" cellpadding="0" style="font-size:10px">
		<tr>
			<td width="1%"><img src="[img://]vfs/folder.gif"></td>
			<td width="99%">&nbsp;{$name}&nbsp;<span style="color:#000; font-size: 8px;"><i>({$id})</i></span></td>
		</tr>
		</table>
	</td>
</tr>							
EOF;
    }

    /**
     * Окно создания новой директории
     * @param int $parentDir
     * @return string
     */
    function createDir( $parentDir ) { return <<<EOF
<div align="center" style="width:300px">
    <form action="[mod://]" name="createDirForm" method="post">
        <table class="tab" cellspacing="0">
        <tr>			
            <td class="row0">Введите имя новой директории:</td>
        </tr><tr>
            <td class="row2">
                <input type="text" name="dir_name" value="" class="textinput" style="width:100%">
                                                
                <script type="text/javascript">document.createDirForm.dir_name.focus();</script>
            </td>
        </tr><tr>
            <td class="row_sub">
                <input class="button" type="submit" name="OK" value="OK" onclick="if( document.createDirForm.dir_name.value == '' ){ alert('Введите имя директории'); return false }">
                &nbsp;					
                <a href="[mod://]"><u>Cancel</u></a>
            </td></tr>							
        </table>															
        <input type="hidden" name="[?act_var]" value="create_dir" />
        <input type="hidden" name="[?idx_var]" value="{$parentDir}" />						
    </form>
</div>
EOF;
    }

    /**
     * @param int $id
     * @param string $name
     * @return string
     */
    function editFolder( $id, $name ) { return <<<EOF
<div align="center" style="width:400px">
    <form action="[mod://]&x={$id}" name="editFolderForm" method="post">
        <table class="tab" cellspacing="0">
        <tr>
            <td class="row0" colspan="2">Параметры директории:</td>
        </tr><tr>
            <td class="row1" width="200px"><b>Имя директории</b></td>
            <td class="row2" width="200px"><input type="text" name="folder_name" value="{$name}" class="textinput"></td>
        </tr><tr>
            <td class="row_sub" colspan="2">
                <input class="button" type="submit" name="OK" value="OK" onclick="if( document.editFolderForm.folder_name.value == '' ){ alert('Введите имя директории'); return false }">
                &nbsp;					
                <a href="#" onclick="window.history.back(); return false;"><u>Cancel</u></a>
            </td></tr>							
        </table>
                                                        
        <input type="hidden" name="[?act_var]" value="edit" />						
    </form>
</div>
EOF;
    }

    function moveFolder( $id, $name, $select ) { return <<<EOF
<div align="center" style="width:400px">
    <form action="[mod://]&x={$id}" method="post">
        <table class="tab" cellspacing="0">
        <tr>
        <td class="row0" colspan="2">{$name}</td>
        </tr><tr>
            <td class="row1" width="180px" nowrap><b>Укажите новую директорию</b></td>				
            <td class="row2" width="170px" nowrap>{$select}</td>				
        </tr><tr>
            <td class="row_sub" colspan="2">
                <input class="button" type="submit" name="OK" value="OK">
                &nbsp;					
                <a href="[mod://]&x={$id}"><u>Cancel</u></a>
            </td></tr>							
        </table>
                                                        
        <input type="hidden" name="[?act_var]" value="move" />						
    </form>
</div>
EOF;
    }
}