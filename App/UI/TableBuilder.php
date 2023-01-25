<?php

namespace App\UI;

use App\Editor\EditorManager;
use Lib\FormItem;

class TableBuilder extends AbstractBuilder {

    /**
     * @var string
     */
    public $firstRowWidth = '30%';

    /**
     * @var string
     */
    public $secondRowWidth = '70%';

    /**
     * @var string
     */
    public $tableWidth = '100%';

    /**
     * @var string[]
     */
    private $rows = [];

    /**
     * @return string
     */
    function build() {
        $out = '<table class="tab" cellspacing="0" cellpadding="0" style="border-top: 0; width: '.$this->tableWidth.'">';
        $out .= '<tr><td width="'.$this->firstRowWidth.'" style="height: 0;"></td>';
        $out .= '<td width="'.$this->secondRowWidth.'" style="height: 0;"></td></tr>';
        $out .=  implode("\n", $this->rows );
        $out .= '</table>';

        return $out;
    }

    /**
     * @param string $widget
     * @param string $title
     * @param string $text
     * @return TableBuilder
     */
    function addInput( $widget, $title, $text = '', $rowId = null ) {
        $rowExtra = empty( $rowId ) ? '' : " id=\"{$rowId}\"";
        $this->rows[] = '<tr'.$rowExtra.'><td class="row1"><b>'.$title.'</b><div style="color:gray">'.$text.'</div></td><td class="row2">'.$widget.'</td></tr>';
        return $this;
    }

    /**
     * @param string $title
     * @return TableBuilder
     */
    function addSubmit( $title ) {
        $this->rows[] = '<tr><td colspan="2" class="row_sub"><input name="form_submit" type="submit" value="'.$title.'"></td></tr>';
        return $this;
    }

    function addRichTextEditor( $name, $value, $width = null, $height = null ) {
        $editor = new EditorManager( $name ? $name : 'content', $value );
        $editor->setColors( '#000000, #003399, #71B638, #9A0000' );
        $content = $editor->make();
        $this->rows[] = '<tr><td colspan="2" class="row_sub">'.$content.'</td></tr>';

        return $this;
    }

    /**
     * @param string $text
     * @return TableBuilder
     */
    function addText( $text ) {
        $this->rows[] = '<tr><td colspan="2" class="row1">'.$text.'</td></tr>';
        return $this;
    }

    /**
     * @return int
     */
    function rowCount() {
        return count( $this->rows );
    }
}
