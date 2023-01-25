<?php

namespace Module\Posts\Model;

use Admin\WidgetsView;
use App\UI\TableBuilder;
use Module\ModuleException;

class PostsForm {
    /**
     * @param WidgetsView $widgets
     * @param PostFormDTO $data
     * @return TableBuilder
     */
    function buildTable( WidgetsView $widgets, PostFormDTO $data ) {
        $table = new TableBuilder();
        $table->firstRowWidth = '10%';
        $table->secondRowWidth = '90%';
        $table->addInput( $widgets->formInput('title', $data->title ), 'Заголовок' );
        $table->addInput( $widgets->dateInputs('date', $data->day, $data->month, $data->year ), 'Дата', '(дд/мм/гггг)');
        $table->addText('<b>Краткое описание:</b>');
        $table->addRichTextEditor( 'short_text', $data->shortText );
        $table->addText('<b>Полный текст:</b>');
        $table->addRichTextEditor( 'long_text', $data->longText );
        $table->addSubmit('Сохранить');

        return $table;
    }
    /**
     * @param PostFormDTO $data
     * @return void
     * @throws ModuleException
     */
    function validateFormData( PostFormDTO $data ) {
        if( $data->year < 1970 ) {
            throw new ModuleException('Вводить даты ниже 1970 года нельзя. Это антинаучно.');
        }

        if( !checkdate( $data->month, $data->day, $data->year )) {
            throw new ModuleException('Неверно указана <b>Дата</b>');
        }

        if( $data->title == '' ) {
            throw new ModuleException('Обязательно укажите <b>Заголовок</b>');
        }

        // Если день или месяц представлены одним числом, то добавляем вперед нолик
        if( strlen( $data->day ) == 1 ) { $data->day = '0'.$data->day; }
        if( strlen( $data->month ) == 1 ) { $data->month = '0'.$data->month; }
    }
}