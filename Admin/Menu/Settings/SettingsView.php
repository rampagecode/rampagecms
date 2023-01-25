<?php

namespace Admin\Menu\Settings;

class SettingsView {
    /**
     * Начало таблицы группы настроек
     * @return string
     */
    function group_start_table() { return <<<EOF
<table class="tab" cellspacing="0">
<tr><td class="row0" colspan="2">Настройки</td></tr>
EOF;
    }

    /**
     * Строка таблицы
     * @param string[] $data
     * @return string
     */
    function group_row( $data ) {
        return <<<EOF
<tr>
    <td class="row1" nowrap><img src="[img://]settings_folder.gif">&nbsp;</td>
    <td class="row2" width="99%" style="font-size: 10px;">
        <b><a href="[mod://]&i=view&x={$data['conf_title_id']}">{$data['extra']} {$data['conf_title_title']}</a></b><br>
        <font color="#808080">{$data['conf_title_desc']}</font>
    </td>
</tr>
EOF;
    }

    /**
     * Конец таблицы
     * @return string
     */
    function group_end_table() {
        return <<<EOF
</table><br /><br />
EOF;
    }

    /**
     * @param $conf_group
     * @param $group_title
     * @param $content
     * @param $key_array
     * @return string
     */
    function group_view_table( $conf_group, $group_title, $content, $key_array ) { return <<<EOF
<form action="[mod://]" method="post">
    <input type="hidden" name="i" value="update">
    <input type="hidden" name="x" value="{$conf_group}">
    
    <table class="tab" cellspacing="0">
        <tr>
            <td class="row0" colspan="2">{$group_title}</td>
        </tr>
        {$content}
        <tr>
            <td colspan="2" class="row_sub">
                <input type="submit" value="Сохранить изменения">
            </td>
        </tr>
    </table>
    
    <input type='hidden' name='settings_save' value="{$key_array}">
</form>
EOF;
    }

    function settingRow( $title, $key, $text, $control ) { return <<<EOF
<tr>
    <td class="row1" width='50%'>
        <b>{$title}</b>
        <span style='color:gray'>(<i>{$key}</i>)</span>
        <div style='color:gray'>{$text}</div>
    </td>
    <td class='row2' width='50%'>{$control}</td>
</tr>
EOF;
    }
}
