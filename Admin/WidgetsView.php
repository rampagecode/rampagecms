<?php

namespace Admin;

use App\UI\InputBuilder;

class WidgetsView {
    function formInput( $name, $value = "", $type = 'text', $extra = '' ) { return <<<EOF
<input type="{$type}" name="{$name}" value="{$value}" class="textinput" {$extra} />
EOF;
    }

    function formTextarea( $name, $value = '', $cols = '60', $rows = '5', $wrap = 'soft', $id = '', $style = '', $js = '' ) {
        if( $id ) {
            $id = "id='$id'";
        } else {
            $id = "id='{$name}'";
        }

        if( $style ) {
            $style = "style='$style'";
        }

        return <<<EOF
<textarea name="{$name}" cols="{$cols}" rows="{$rows}" wrap="{$wrap}" {$id} {$style} {$js} class='multitext'>{$value}</textarea>
EOF;
    }

    function formYesNo( $name, $default_val = '', $js = array() ) {
        $y_js = "";
        $n_js = "";

        if( ! empty( $js['yes'] )) {
            $y_js = $js['yes'];
        }

        if( ! empty( $js['no'] )) {
            $n_js = $js['no'];
        }

        $yes = $this->_formYes( $name, $default_val == 1, $y_js );
        $no = $this->_formNo( $name, $default_val != 1, $n_js );

        return $yes.'&nbsp;&nbsp;&nbsp;'.$no;
    }

    /**
     * @param string $name
     * @param bool $checked
     * @param string $extra
     * @return string
     */
    private function _formYes( $name, $checked, $extra ) {
        $checked = $checked ? 'checked' : '';

        return <<<EOF
Да &nbsp; <input type="radio" name="{$name}" value="1" {$extra} {$checked} id="green" />
EOF;
    }

    /**
     * @param string $name
     * @param bool $checked
     * @param string $extra
     * @return string
     */
    private function _formNo( $name, $checked, $extra ) {
        $checked = $checked ? 'checked' : '';

        return <<<EOF
Нет &nbsp; <input type="radio" name="{$name}" value="0" {$extra} {$checked} id="red" />
EOF;
    }

    function formDropdown( $name, $list = array(), $default_val = '', $js = '', $css = '' ) {
        $options = '';

        foreach( $list as $k => $v ) {
            $selected = (( $default_val != '' ) and ( $v[0] == $default_val ));
            $options .= $this->_option( $v[1], $v[0], $selected );
        }

        if( $css != '' ) {
            $css = "class='{$css}'";
        }

        return $this->_select( $name, $options, $js.' '.$css );
    }

    private function _select( $name, $options, $extra ) { return <<<EOF
<select name="{$name}" {$extra} class="dropdown">
{$options}
</select>
EOF;
    }

    /**
     * @param string $title
     * @param string $value
     * @param bool $selected
     * @return string
     */
    private function _option( $title, $value, $selected ) {
        $selected = $selected ? 'selected="selected"' : '';

        return <<<EOF
<option value="{$value}" {$selected}>{$title}</option>
EOF;
    }

    function formMultiselect( $name, $list = array(), $default = array(), $size = 5, $js = '' ) {
        $options = '';

        foreach( $list as $k => $v ) {
            $selected = count( $default ) > 0 && in_array( $v[0], $default );
            $options .= $this->_option( $v[1], $v[0], $selected );
        }

        $params = "{$js} class=\"dropdown\" multiple=\"multiple\" size=\"{$size}\"";

        return $this->_select( $name, $options, $params );
    }

    /**
     * Простая шапка для таблицы
     * @param string $title
     * @param string $options
     * @param string $tabw
     * @return string
     */
    function tableHeader( $title='', $options='', $tabw = '100%' ) { return <<<EOF
<table class="tab" cellspacing="0" style="border-bottom: 0; width: {$tabw}">
<tr>
    <td class="row0">{$title}</td>
    <td class="row0" align="right" valign="bottom" style="padding-bottom: 2px;">{$options}<span style="width: 1px;"></span></td>
</tr>
</table>
EOF;
    }

    /**
     * @param string[] $list
     * @return string
     */
    function errorList( $list ) {
        $out = '';

        if( is_array( $list )) {
            foreach( $list as $k ) {
                $out .= '&bull; ' . $k . '<br />';
            }
        }
        elseif( $list != '' ) {
            $out .= '&bull; ' . $list;
        } else {
            return '';
        }

        return $this->error( $out );
    }

    /**
     * @param string $text
     * @return string
     */
    function error( $text ) { return <<<EOF
<div style="border:1px solid #345487; background: #FFF; font-size: 11px;">
    <div style="background: #D1DCEB; color: #3A4F6C; font-weight: bold; padding: 6px; margin-top: 1px">Обнаружены следущие ошибки:</div>
    <div style="background: #E4EAF2; color: #AB1212; padding:6px; border-bottom: 1px solid #D1DCEB; border-right: 1px solid #D1DCEB ; border-top:1px solid #FFF; border-left: 1px solid #FFF;">{$text}</div>
</div><br />
EOF;
    }

    /**
     * Просто сообщение о чем-либо.
     * @param string $text
     * @return string
     */
    function messageBox( $text ) { return <<<EOF
<div style="border:1px solid #999999; background: #FFF; font-size: 11px; margin-left: 2px; margin-right: 2px;">
    <div style="background: #DBDBDB; color: #3A4F6C; font-weight: bold; padding: 6px; margin-top: 1px">Уведомление</div>
    <div style="background: #F4F4F4; color: #316388; padding:6px; border-bottom: 1px solid #DBDBDB; border-right: 1px solid #DBDBDB ; border-top:1px solid #FFF; border-left: 1px solid #FFF;">{$text}</div>
</div><br />
EOF;
    }

    function dateInputs( $namePrefix, $day, $month, $year ) {
        $builder = new InputBuilder();
        $builder->set('maxlength', 2)->set('style', 'width: 20px;');
        $inputs = [];

        $builder->set('name', $namePrefix.'_day')->set('value', $day);
        $inputs[] = $builder->build();

        $builder->set('name', $namePrefix.'_month')->set('value', $month);
        $inputs[] = $builder->build();

        $builder->set('maxlength', 4)->set('style', 'width: 33px;');
        $builder->set('name', $namePrefix.'_year')->set('value', $year);
        $inputs[] = $builder->build();

        return implode('&nbsp;/&nbsp;', $inputs);
    }
}
