<?php

namespace Admin;

class ButtonsView {
    private function std_img_button( $url, $img, $title, $text, $w, $h ) { return <<<EOF
<a href="{$url}"><img src="[img://]buttons/{$img}" width="{$w}" height="{$h}" border="0" align="absmiddle" title="{$title}" />&nbsp;{$text}</a>
EOF;
    }

    private function confirm_button( $url, $img, $title, $text, $w, $h ) { return <<<EOF
<a href="#" onClick="doConfirm('{$url}'); return 0;"><img src="[img://]buttons/{$img}" width="{$w}" height="{$h}" border="0" align="absmiddle" title="{$title}" />&nbsp;{$text}</a>
EOF;
    }

    private function confirm_button2( $url, $img, $title, $text, $w, $h ) { return <<<EOF
<a href="#" onClick="doConfirm('{$url}', 'Все не сохраненные изменения на странице будут потеряны!\\n\\nПродолжить?' ); return 0;"><img src="[img://]buttons/{$img}" width="{$w}" height="{$h}" border="0" align="absmiddle" title="{$title}" />&nbsp;{$text}</a>
EOF;
    }

    private function jumpto_button( $url, $img, $title, $text, $w, $h ) { return <<<EOF
<a href="#" onClick="jumpToSheet('{$url}'); return 0;"><img src="[img://]buttons/{$img}" width="{$w}" height="{$h}" border="0" align="absmiddle" title="{$title}" />&nbsp;{$text}</a>
EOF;
    }

    private function disable_button( $img, $title, $text, $w, $h ) { return <<<EOF
<img src="[img://]buttons/{$img}" width="{$w}" height="{$h}" border="0" align="absmiddle" title="{$title}" /><span style="color: #9C9A9C; font-weight: normal;">&nbsp;{$text}</span>
EOF;
    }

    //

    function edit( $url = '', $text = '', $title = '' ) {
        return $this->std_img_button( $url, 'edit.gif', $title ?: 'Редактировать', $text, 20, 20 );
    }

    function delete( $url = '', $text = '', $title = '' ) {
        return $this->confirm_button( $url, 'delete.gif', $title ?: 'Удалить', $text, 20, 20 );
    }

    function noDel( $text = '', $title = '' ) {
        return $this->disable_button( 'nodelete.gif', $title ?: 'Удалить(нельзя)', $text, 20, 20 );
    }

    function info( $url = '', $text = '', $title = '' ) {
        return $this->std_img_button( $url, 'info.gif', $title ?: 'Просмотреть', $text, 18, 18 );
    }

    function up( $url = '', $text = '', $title = '' ) {
        return $this->std_img_button( $url, 'up.png', $title ?: 'Вверх', $text, 20, 20 );
    }

    function noUp( $text = '', $title = '' ) {
        return $this->disable_button( 'noup.png', $title ?: 'Вверх(нельзя)', $text, 20, 20 );
    }

    function down( $url = '', $text = '', $title = '' ) {
        return $this->std_img_button( $url, 'down.png', $title ?: 'Вниз', $text, 20, 20 );
    }

    function noDown( $text = '', $title = '' ) {
        return $this->disable_button( 'nodown.png', $title ?: 'Вниз(нельзя)', $text, 20, 20 );
    }

    function program( $url = '', $text = '', $title = '' ) {
        return $this->std_img_button( $url, 'prog.jpg', $title ?: 'Программа', $text, 23, 20 );
    }

    function resume( $url = '', $text = '', $title = '' ) {
        return $this->std_img_button( $url, 'resume.jpg', $title ?: 'Резюме', $text, 22, 20 );
    }

    function comments( $url = '', $text = '', $title = '' ) {
        return $this->std_img_button( $url, 'comments.jpg', $title ?: 'Отзывы', $text, 20, 20 );
    }

    function photo( $url = '', $text = '', $title = '' ) {
        return $this->std_img_button( $url, 'foto.jpg', $title ?: 'Фотоотчет', $text, 20, 20 );
    }

    function members( $url = '', $text = '', $title = '' ) {
        return $this->std_img_button( $url, 'members.jpg', $title ?: 'Участники', $text, 20, 20 );
    }

    function createNew( $url = '', $text = '', $title = '' ) {
        return $this->std_img_button( $url, 'new.gif', $title ?: 'Создать новый..', $text, 20, 20 );
    }

    function newConfirm( $url = '', $text = '', $title = '' ) {
        return $this->confirm_button2( $url, 'new.gif', $title ?: 'Создать новый..', $text, 20, 20 );
    }

    function special( $url = '', $text = '', $title = '' ) {
        return $this->std_img_button( $url, 'special.jpg', $title ?: 'Специальные гости', $text, 20, 20 );
    }

    function unlock( $url = '', $text = '', $title = '' ) {
        return $this->std_img_button( $url, 'lock_close.gif', $title ?: 'Разблокировать задачу', $text, 16, 16 );
    }

    function moveTo( $url = '', $text = '', $title = '' ) {
        return $this->std_img_button( $url, 'moveto.png', $title ?: 'Переместить', $text, 20, 20 );
    }

    function unload( $url = '', $text = '', $title = '' ) {
        return $this->std_img_button( $url, 'reload.gif', $title ?: 'Сбросить', $text, 18, 18 );
    }

    function overload( $url = '', $text = '', $title = '' ) {
        return $this->std_img_button( $url, 'overload.gif', $title ?: 'Перегрузить', $text, 18, 18 );
    }

    function jumpTo( $url = '', $text = '', $title = '' ) {
        return $this->jumpto_button( $url, 'jump.gif', $title ?: 'Перейти к странице', $text, 14, 14 );
    }

    function install( $url = '', $text = '', $title = '' ) {
        return $this->std_img_button( $url, 'install.png', $title ?: 'Установить', $text, 16, 16 );
    }
}