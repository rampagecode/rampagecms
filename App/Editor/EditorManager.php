<?php

namespace App\Editor;

class EditorManager {
    static $instance_num = 0;

    var $lang;
    var $toolbar1 = array( 'print', 'find', 'spacer', 'cut', 'copy', 'paste', 'spacer', 'undo', 'redo', 'spacer', 'image', 'link', 'doc', 'spacer', 'table', 'forecolor', 'backcolor', 'char', 'bookmark', 'ruler' );//, 'spacer', 'youtube' );
    var $toolbar2 = array( 'format_list', 'class_list', 'font_list', 'size_list', 'spacer', 'bold', 'italic', 'underline', 'spacer', 'left', 'center', 'right', 'justify', 'spacer', 'ol', 'ul', 'outdent', 'indent' );
    var $useP = 'false';
    var $xhtml_lang = "en";
    var $encoding = "iso-8859-1";
    var $guidelines = '1';
    var $doctype = '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">';
    var $charset = '';

    var $result	= '';
    var $color_swatches 	= '';
    var $name				='htmlCode';
    var $original_name		='htmlCode';
    var $code				='';

    var $font_menu 			= '';
    var $format_menu 		= '';
    var $size_menu 			= '';

    var $editorWidth	= '100%';
    var $editorHeight   = '457px';
    var $stepMoreHeight = '100';
    var $stepLessHeight = '100';

    public function __construct( $name, $html ) {
        $this->lang = (new EditorLang())->getData();

        $view = new EditorView();
        $this->font_menu = $view->font_menu();
        $this->format_menu = $view->format_menu();
        $this->size_menu = $view->size_menu();
        $this->result = $view->editor();

        $this->setName( $name );
        $this->setHTML( $html );
    }

    function setColors( $colors ) {
        if( ! empty( $colors )) {
            $this->color_swatches = str_replace( ' ', '', $colors );
        }
    }

    function setName( $name = 'htmlCode' ) {
        if( ! empty( $name )) {
            $this->name = preg_replace("/[^A-Za-z0-9_]/smi", '', $name );
            $this->original_name = $name;
        }
    }

    function setHTML( $code = '' ) {
        // we will not check the cache because code is not cached so that you can still insert different code into a saved configuration.
        // standardise carriage returns, strip slashes increase sent from a form post or database.
        $code = str_replace(array("\r\n", "\r"), array("\n", "\n"), $code);

        // strip out the XML declaration because this can confuse Internet Explorer
        $code = preg_replace('/<\?php echo "<\?xml version=\"1\.0\" encoding=\"(.*?)\"\?"\.">"; \?>/smi',  "", $code);
        $code = preg_replace("/<\?xml version=\"1.0\" encoding=\"(.*?)\"\?>/smi",  "", $code);
        $code = $this->parseEntities($code);

        // convert to htmlentities to make it safe to paste in a textarea
        $this->code = htmlspecialchars($code);
    }

    function make( $width = '', $height = '' ) {
        if( ! $width = ( $width ? $width : $this->editorWidth )) {
            $width = '100%';
        }

        //-----------------------------------------
        // Check we have been sent valid height settings
        //-----------------------------------------

        if( ! $height = ( $height ? $height : $this->editorHeight )) {
            $height = '500px';
        }

        //-----------------------------------------
        // Initialize variables
        //-----------------------------------------

        $imenu_height = 110;
        $bmenu_height = 50;
        $tmenu_height = 350;
        $smenu_height = 80;

        $format_list_style 	= '';
        $font_list_style 	= '';
        $size_list_style 	= '';
        $class_list_style 	= 'display: none;';
        $toolbar1_style 	= '';
        $toolbar2_style 	= '';

        $tab1Height = ($height - 80);
        $tab2Height = ($height - 75);
        $tab3Height = ($height - 76);

        $toolbar1 = $this->buildToolbar( $this->toolbar1 );
        $toolbar2 = $this->buildToolbar( $this->toolbar2 );

        $domain_address = strtolower(
            substr( $_SERVER['SERVER_PROTOCOL'],
            0,
            strpos( $_SERVER['SERVER_PROTOCOL'], '/' )) . (
                isset( $_SERVER['HTTPS'] )
                    ? ( $_SERVER['HTTPS'] == "on" ? 's://' : '://' )
                    : '://'
                )
                . $_SERVER['SERVER_NAME']
        );

        $this->result = preg_replace(
            "/<!-- dots -->/smi",
            '<img width="3" height="15" src="[img://]editor/dots.gif" alt="" align="left">',
            $this->result
        );

        $this->result = $this->replace( $this->result, array(
            'toolbar1'					=> $toolbar1,
            'toolbar2'					=> $toolbar2,
            'is_gecko'					=> true,
            'url_3xi_css'				=> '[css://]',
            'url_3xi_img'				=> '[img://]',
            'step_inc'					=> intval( $this->stepMoreHeight ),
            'step_dec'					=> intval( $this->stepLessHeight ),
            'usexhtml' 					=> 'false',
            'stylesheet' 				=> empty($this->set_style ) ? "''" : '[\''.$this->set_style.'\']',
            'adsess_url' 				=> '[base://]',
            'js_lib_url' 				=> '[js://]editor/',
            'imgs'		 				=> '[img://]editor/',
            'width' 					=> $width,
            'absheight' 				=> $height,
            'height' 					=> $tab1Height,
            'height2' 					=> $tab2Height,
            'height3' 					=> $tab3Height,
            'imenu_height' 				=> $imenu_height,
            'bmenu_height' 				=> $bmenu_height,
            'tmenu_height' 				=> $tmenu_height,
            'smenu_height' 				=> $smenu_height,
            'baseURLurl' 				=> '[base://]',
            'baseURL' 					=> '',
            'domain' 					=> '',
            'domain2' 					=> str_replace( '/', '\/', quotemeta( $domain_address )) ."([^\/]*)",
            'is_ie50' 					=> 'false',
            'format_list_style' 		=> $format_list_style,
            'font_list_style' 			=> $font_list_style,
            'size_list_style' 			=> $size_list_style,
            'class_list_style' 			=> $class_list_style,
            'toolbar1_style' 			=> $toolbar1_style,
            'toolbar2_style' 			=> $toolbar2_style,
            'font_menu' 				=> $this->font_menu,
            'format_menu' 				=> $this->format_menu,
            'size_menu' 				=> $this->size_menu,
            "links" 					=> empty($this->links) ? "''" : '['.$this->links.']',
            "xhtml_lang" 				=> $this->xhtml_lang,
            "encoding" 					=> $this->encoding,
            "usep" 						=> $this->useP,
            "lineReturns" 				=> ($this->useP=='true') ? 'p' : 'div',
            "savebutton" 				=> '',
            'color_swatches' 			=> $this->color_swatches,
            'instance_img_dir' 			=> '',
            'instance_doc_dir' 			=> '',
            'secure' 					=> false,
            'guidelines' 				=> $this->guidelines,
            'name' 						=> $this->name,
            'original_name' 			=> $this->original_name,
            'doctype' 					=> $this->doctype,
            'charset' 					=> $this->charset,
        ));

        $this->result = $this->parseLanguage( $this->result );

        $arr = explode( '</script>', $this->result );
        $num = count( $arr );

        for( $i = 0; $i < $num; $i++ ) {
            $arr2 = explode( '<script', $arr[$i] ); //>
            $foo = str_replace( array("\t", "\r", "\n"), '', $arr2[0] );

            if( isset( $arr2[1] )) {
                $arr[$i] = $foo . '<script' . $arr2[1]; //>
            } else {
                $arr[$i] = $foo;
            }
        }

        $this->result = implode( '</script>', $arr );

        // set code if empty
        if( empty( $this->code )) {
            if( $this->useP == 'true' ) {
                $this->code = '<p>&nbsp;</p>';
            } else {
                $this->code = '<div>&nbsp;</div>';
            }
        }

        self::$instance_num++;

        if( self::$instance_num > 1 ) {
            $this->result = preg_replace( '/<!-- begin 1st instance only -->.*?<!-- end 1st instance only -->/smi', '', $this->result );
        }

        // tidy up comments
        $this->result = preg_replace("/<!--\s.*?-->/smi", '', $this->result );

        // now insert the code to be edited, this is done after the caching so that the code is not cached.
        return str_replace('##htmlCode##', $this->code, $this->result );
    }

    /**
     * @param string[] $list
     * @return string
     */
    private function buildToolbar( $list ) {
        $buttons = new EditorButtons();
        $result = '';

        if( is_array( $list ) AND ( count( $list ))) {
            foreach( $list AS $item ) {
                $_method = 'b_' . $item;

                if( method_exists( $buttons, $_method )) {
                    $result .= $buttons->$_method();
                }
            }
        }

        return $result;
    }
    
    private function parseLanguage( $text ) {
        $data = (new EditorLang())->getData();
        $replace = [
            'please_wait' 				=> $data['please_wait'],
            'save' 						=> $data['save'],
            'post' 						=> $data['post'],
            'send' 						=> $data['send'],
            'print' 					=> $data['print'],
            'find_and_replace' 			=> $data['find_and_replace'],
            'cut' 						=> $data['cut'],
            'copy'					 	=> $data['copy'],
            'paste' 					=> $data['paste'],
            'paste_word' 				=> $data['paste_word'],
            'undo' 						=> $data['undo'],
            'redo' 						=> $data['redo'],
            'insert_table' 				=> $data['insert_table'],
            'table_properties' 			=> $data['table_properties'],
            'add_row' 					=> $data['add_row'],
            'delete_row' 				=> $data['delete_row'],
            'insert_column' 			=> $data['insert_column'],
            'delete_column' 			=> $data['delete_column'],
            'merge_cell' 				=> $data['merge_cell'],
            'unmerge_cell' 				=> $data['unmerge_cell'],
            'insert_emoticon' 			=> $data['insert_emoticon'],
            'insert_image' 				=> $data['insert_image'],
            'horizontal_line' 			=> $data['horizontal_line'],
            'insert_hyperlink' 			=> $data['insert_hyperlink'],
            'document_link' 			=> $data['document_link'],
            'insert_bookmark' 			=> $data['insert_bookmark'],
            'special_characters' 		=> $data['special_characters'],
            'paragraph_format' 			=> $data['paragraph_format'],
            'style_class' 				=> $data['style_class'],
            'font_face' 				=> $data['font_face'],
            'font_size' 				=> $data['font_size'],
            'bold' 						=> $data['bold'],
            'italic' 					=> $data['italic'],
            'underline' 				=> $data['underline'],
            'align_left' 				=> $data['align_left'],
            'align_right' 				=> $data['align_right'],
            'align_center' 				=> $data['align_center'],
            'justify' 					=> $data['justify'],
            'numbering' 				=> $data['numbering'],
            'bullets' 					=> $data['bullets'],
            'increase_indent' 			=> $data['increase_indent'],
            'decrease_indent' 			=> $data['decrease_indent'],
            'font_color' 				=> $data['font_color'],
            'highlight' 				=> $data['highlight'],
            'design' 					=> $data['design'],
            'html_code' 				=> $data['html_code'],
            'preview' 					=> $data['preview'],
            'br_tag' 					=> $data['br_tag'],
            'toggle_guidelines' 		=> $data['toggle_guidelines'],
            'image_properties' 			=> $data['image_properties'],
            'image_zoom' 				=> $data['image_zoom'],
            'bookmark_properties' 		=> $data['bookmark_properties'],
            'add_row_above' 			=> $data['add_row_above'],
            'add_row_below' 			=> $data['add_row_below'],
            'add_column_left' 			=> $data['add_column_left'],
            'add_column_right' 			=> $data['add_column_right'],
            'merge_right' 				=> $data['merge_right'],
            'merge_below' 				=> $data['merge_below'],
            'unmerge_right' 			=> $data['unmerge_right'],
            'unmerge_below' 			=> $data['unmerge_below'],
            'cancel' 					=> $data['cancel'],
            'default' 					=> $data['default'],
            'clear_styles' 				=> $data['clear_styles'],
            'javascript_warning' 		=> $data['javascript_warning'],
            // javascript
            'guidelines_hidden' 		=> $data['guidelines_hidden'],
            'guidelines_visible' 		=> $data['guidelines_visible'],
            'place_cursor_in_table' 	=> $data['place_cursor_in_table'],
            'only_split_merged_cells' 	=> $data['only_split_merged_cells'],
            'no_cell_right' 			=> $data['no_cell_right'],
            'different_row_spans' 		=> $data['different_row_spans'],
            'no_cell_below' 			=> $data['no_cell_below'],
            'different_column_spans' 	=> $data['different_column_spans'],
            'select_hyperlink_text' 	=> $data['select_hyperlink_text'],
            'upgrade' 					=> $data['upgrade'],
            'format' 					=> $data['format'],
            'font' 						=> $data['font'],
            'class' 					=> $data['class'],
            'size' 						=> $data['size1'],
            // format menu
            'normal' 		            => $this->lang['normal'] ,
            'heading_1'	 	            => $this->lang['heading_1'],
            'heading_2' 	            => $this->lang['heading_2'],
            'heading_3' 	            => $this->lang['heading_3'],
            'heading_4' 	            => $this->lang['heading_4'],
            'heading_5' 	            => $this->lang['heading_5'],
            'heading_6' 	            => $this->lang['heading_6'],
            'pre_formatted'             => $this->lang['pre_formatted'],
            'address1' 		            => $this->lang['address1'],
            // size menu
            '1'                         => $this->lang['1'],
            '2'                         => $this->lang['2'],
            '3'                         => $this->lang['3'],
            '4'                         => $this->lang['4'],
            '5'                         => $this->lang['5'],
            '6'                         => $this->lang['6'],
            '7'                         => $this->lang['7'],
        ];

        return $this->replace( $text, $replace );
    }

    private function replace( $code, $array ) {
        $search  = array();
        $replace = array();

        foreach( $array as $k => $v ) {
            $search[] = '##' . $k . '##';
            $replace[] = $v;
        }

        return str_replace( $search, $replace, $code );
    }

    private function parseEntities( $code ) {
        // Convert anchors
        $code = preg_replace("/<a name=\"(.*?)\".*?>(.*?)<\/a>/smi", "<img name=\"\$1\" src=\"[img://]editor/bookmark_symbol.gif\" contenteditable=\"false\" width=\"16\" height=\"13\">\$2", $code);

        // Convert text cut
        $code = preg_replace( "#<!--textcut-->#", '<input style="border-right: medium none; background-position: center 50%; border-top: medium none; background-image: url(/assets/admin/images/editor/imgtextcut.png); border-left: medium none; width: 100%; border-bottom: medium none; background-repeat: repeat-x; height: 15px; background-color: transparent;" id="textcuttag" title="Текст расположенный ниже этой линии будет скрыт, а на его месте появится ссылка" disabled="disabled" type="text" value="">', $code );

        //$code = preg_replace("/<a(.*?)href=\"#(.*?)\"(.*?)>/smi", "<a\$1href=\"WP_BOOKMARK#\$2\"\$3>", $code);
        // Both browsers will ignore ASP tags so we need to make them into comments here
        $code = preg_replace("/<%(.*?)\%>/smi",  "<!--asp\$1-->", $code);

        // Mozilla will completely ignore PHP tags so we need to convert them into comments here instead
        $code = preg_replace("/<\?php(.*?)\?>/smi",  "<!--p\$1-->", $code);

        // hack to stop bug in Mozilla where row modifications cause an endless loop if table html is not all on one line (Note for future development: is this a bug in Mozilla or a flaw in the table editing script?)
        // table
        $code = preg_replace("/<table([^>]*?)>\s+/smi",  "<table\$1>", $code);

        // tbody
        $code = preg_replace("/<tbody([^>]*?)>\s+/smi",  "<tbody\$1>", $code);

        // close tbody
        $code = preg_replace("/<\/tbody>\s+/smi",  "</tbody>", $code);

        // thead
        $code = preg_replace("/<thead([^>]*?)>\s+/smi",  "<thead\$1>", $code);

        // close thead
        $code = preg_replace("/<\/thead>\s+/smi",  "</thead>", $code);

        // tfoot
        $code = preg_replace("/<tfoot([^>]*?)>\s+/smi",  "<tfoot\$1>", $code);

        // close tfoot
        $code = preg_replace("/<\/tfoot>\s+/smi",  "</tfoot>", $code);

        // tr
        $code = preg_replace("/<tr([^>]*?)>\s+/smi",  "<tr\$1>", $code);

        // close tr
        $code = preg_replace("/<\/tr>\s+/smi",  "</tr>", $code);

        // close td
        $code = preg_replace("/<\/td>\s+/smi",  "</td>", $code);

        $code = preg_replace("/<font>(.*?)<\/font>/smi",  "\$1", $code);

        return $code;
    }
}