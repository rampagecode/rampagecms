<?php

namespace App\Editor;

class EditorLang {
    private $data;

    public function __construct() {
        $lang = array();

        // buttons
        $lang['ok'] = 'OK';
        $lang['yes'] = 'Да';
        $lang['apply'] = 'Применить';
        $lang['insert'] = 'Вставить';
        $lang['cancel'] = 'Отменить';
        $lang['close'] = 'Закрыть';
        $lang['next'] = 'Дальше...';

        // main editor interface;
        $lang['please_wait'] = 'Подождите...';
        $lang['save'] = 'Сохранить';
        $lang['post'] = 'Добавить';
        $lang['send'] = 'Отправить';
        $lang['print'] = 'Печать...';
        $lang['find_and_replace'] = 'Найти и Заменить...';
        $lang['cut'] = 'Вырезать';
        $lang['copy'] = 'Скопировать';
        $lang['paste'] = 'Вставить';
        $lang['paste_word'] = 'Вставить из Word...';
        $lang['undo'] = 'Отменить';
        $lang['redo'] = 'Повторить';
        $lang['insert_table'] = 'Вставить таблицу...';
        $lang['table_properties'] = 'Свойства таблицы...';
        $lang['add_row'] = 'Добавить строку...';
        $lang['delete_row'] = 'Удалить строку';
        $lang['insert_column'] = 'Добавить колонку...';
        $lang['delete_column'] = 'Удалить колонку';
        $lang['merge_cell'] = 'Объеденить ячейки...';
        $lang['unmerge_cell'] = 'Разъеденить ячейки';
        $lang['insert_emoticon'] = 'Вставить смайлик';
        $lang['insert_image'] = 'Добавить/редактировать изображение...';
        $lang['horizontal_line'] = 'Горизонтальная линия...';
        $lang['insert_hyperlink'] = 'Создать/изменить ссылку...';
        $lang['document_link'] = 'Ссылка на документ...';
        $lang['insert_bookmark'] = 'Добавить/редактировать закладку...';
        $lang['special_characters'] = 'Специальные символы...';
        $lang['insert_object'] = 'Вставить объект...';
        $lang['paragraph_format'] = 'Параграф';
        $lang['style_class'] = 'Класс стиля';
        $lang['font_face'] = 'Шрифт';
        $lang['font_size'] = 'Размер шрифта';
        $lang['bold'] = 'Жирный';
        $lang['italic'] = 'Наклоненный';
        $lang['underline'] = 'Подчеркнутый';
        $lang['align_left'] = 'Выровнять слева';
        $lang['align_right'] = 'Выровнять справа';
        $lang['align_center'] = 'Выровнять по-центру';
        $lang['justify'] = 'Выровнять по ширине';
        $lang['numbering'] = 'Нумерация';
        $lang['bullets'] = 'Метки';
        $lang['increase_indent'] = 'Увеличить отступ';
        $lang['decrease_indent'] = 'Уменьшить отступ';
        $lang['font_color'] = 'Цвет шрифта...';
        $lang['highlight'] = 'Выделение...';
        $lang['design'] = 'Дизайн';
        $lang['html_code'] = 'html код';
        $lang['preview'] = 'Просмотр';
        $lang['br_tag'] = 'Используйте Shift+Enter для вставки тэга &lt;BR&gt;';
        $lang['toggle_guidelines'] = 'Включить/выключить направляющие';
        $lang['image_properties'] = 'Свойства изображения...';
        $lang['image_zoom'] = 'Увеличенное изображение...';
        $lang['bookmark_properties'] = 'Свойства закладки...';
        $lang['add_row_above'] = 'Добавить строку выше';
        $lang['add_row_below'] = 'Добавить строку ниже';
        $lang['add_column_left'] = 'Добавить колонку слева';
        $lang['add_column_right'] = 'Добавить колонку справа';
        $lang['merge_right'] = 'Объеденить с ячейкой справа';
        $lang['merge_below'] = 'Объеденить с ячейкой ниже';
        $lang['unmerge_right'] = 'Разъеденить с ячейкой справа';
        $lang['unmerge_below'] = 'Разъеденить с ячейкой ниже';
        $lang['javascript_warning'] = '<b>WARNING:</b> JavaScript must be enabled to use this form.';
        $lang['default'] = 'По умолчанию';
        $lang['clear_styles'] = 'Очистить стили';

        // drop down format menus
        $lang['normal'] = 'Обычный';
        $lang['heading_1'] = 'Заголовок 1';
        $lang['heading_2'] = 'Заголовок 2';
        $lang['heading_3'] = 'Заголовок 3';
        $lang['heading_4'] = 'Заголовок 4';
        $lang['heading_5'] = 'Заголовок 5';
        $lang['heading_6'] = 'Заголовок 6';
        $lang['pre_formatted'] = 'Предварительно форматированный';
        $lang['address1'] = 'Адрес';

        // javascript alerts
        $lang['guidelines_hidden'] = 'направляющие спрятаны';
        $lang['guidelines_visible'] = 'направляющие видимы';
        $lang['place_cursor_in_table'] = 'Установите курсор в ячейку, которую вы хотите изменить';
        $lang['only_split_merged_cells'] = 'Можно разделить только объедененные ячейки';
        $lang['no_cell_right'] = 'Нет ячеек справа для объеденения';
        $lang['different_row_spans'] = 'Невозможно объеденить ячейки из разных строк';
        $lang['no_cell_below'] = 'Нет ячеек ниже для объеденения';
        $lang['different_column_spans'] = 'Невозможно объеденить ячейки из разных колонок';
        $lang['select_hyperlink_text'] = 'Выделите текст, для создания ссылки';
        $lang['upgrade'] = 'You need to upgrade your web browser to fully enable this editor.\n\nSupported browsers include Mozilla 1.4, Netscape 7.1 and Internet Explorer 5.0 or newer.\n\nYou can still edit this document but only in HTML code view. \n\nNote: If you are using a supported browser you just need to refresh the page.';
        $lang['format'] = 'Формат';
        $lang['font'] = 'Шрифт';
        $lang['class'] = 'Class';
        $lang['size1'] = 'Размер';

        // add a column
        $lang['titles']['insert_column'] = 'Вставить колонку';
        $lang['column_placement'] = 'Размещение колонки:';
        $lang['left_of_selection'] = 'Вставить слева от выделения';
        $lang['right_of_selection'] = 'Вставить справа от выделения';

        // add a row
        $lang['titles']['insert_row'] = 'Вставить строку';
        $lang['row_placement'] = 'Размещение строки:';
        $lang['above_selection'] = 'Вставить выше от выделения';
        $lang['below_selection'] = 'Вставить ниже от выделения';

        // blank
        $lang['preview'] = 'Просмотр';

        // bookmark
        $lang['titles']['insert_bookmark'] = 'Вставить закладку';
        $lang['bookmark_name'] = 'Название закладки:';

        // custom
        $lang['titles']['custom'] = 'Вставить объект';
        $lang['click_on_an_object'] = 'Выберите объект';

        // document and image
        // values surrounded by '##' are variables that are dynamically inserted and should be left untouched!
        $lang['upload_in_progress'] = 'Загрузка в процессе.';
        $lang['titles']['document'] = 'Ссылка на документ для закачки';
        $lang['titles']['image'] = 'Вставить изображение';
        $lang['file_deleted'] = '##file## был удален из ##folder##';
        $lang['check_directory_permission'] = 'Проверьте ваши права';
        $lang['cannot_delete'] = '<b>Предупреждение:</b> Невозможно удалить файл ##file##. ';
        $lang['enter_new_filename'] = 'Введите новое имя файла ##file##:';
        $lang['bad_file_name'] = '<b>Предупреждение:</b> Неправильное имя файла. Название файла должно состоять из букв и цифр.';
        $lang['file_already_exists'] = '<b>Предупреждение:</b> Файл уже существует. Выберите другое имя файла.';
        $lang['file_already_exists2'] = '<b>Предупреждение:</b> Файл уже существует. Переименуйте файл.';
        $lang['dir_not_exist'] = '<b>Предупреждение:</b> Директория не существует!';
        $lang['file_renamed'] = 'Файл ##file## был переименован в ##name##';
        $lang['could_not_rename'] = '<b>Предупреждение:</b> файл ##file## невозможно переименовать в ##name##.';
        $lang['enter_dirname_for_new_dir'] = 'Введите имя новой директории:';
        $lang['file_created'] = '##file## был создан в ##folder##';
        $lang['dir_not_created'] = '<b>Предупреждение:</b> Новая директория не может быть создана.';
        $lang['file_uploaded1'] = 'Файл успешно загружен.';
        $lang['upload_failed'] = '<b>Предупреждение:</b> Невозможно загрузить файл.';
        $lang['dir_exists'] = '<b>Предупреждение:</b> Директория с таким именем уже существует.';
        $lang['copy_error'] = 'При копировании файла возникла ошибка.';
        $lang['bad_filetype'] = '<b>Ошибка:</b> Неправильный тип файла. Только файлы типов ##filetypes## разрешены.';
        $lang['file_too_large'] = '<b>Ошибка:</b> Размер файла слишком большой. Допустимый размер ##max_size##kb.';
        $lang['dimensions_too_large'] = '<b>Ошибка:</b> Большой размер изображениея. Допустимые размеры ##width## px в ширину и ##height## px в высоту.';
        $lang['but_was_renamed'] = 'Был переименован в ##name##';
        $lang['should_i_overwrite'] = '<b>Предупреждение:</b> Файл с таким именем уже существует. Перезаписать?';
        $lang['no_overwrite_permission'] = '<b>Предупреждение:</b> Файл с таким именем уже существует. <br>У вас не прав для перезаписи файлов. Переименуйте существующий файл';
        $lang['file_uploaded2'] = '##file## успешно загружен. ';
        $lang['upload_failed2'] = '<b>Предупреждение:</b> ошибка загрузки файла ##file##.';
        $lang['select_document'] = 'Выберите документ:';
        $lang['select_image'] = 'Выберите изображение:';
        $lang['looking_in'] = 'Директория:';
        $lang['up_one_level'] = 'На уровень выше';
        $lang['new_folder'] = 'Создать директорию';
        $lang['no_files'] = 'В данной лиректории нет файлов.';
        $lang['upload_an_image'] = 'Загрузить изображение:';
        $lang['upload_a_document'] = 'Загрузить документ:';
        $lang['images_must_be'] = 'Изображения должны быть в форматах JPG GIF или PNG, меньше чем ##max_size##kb и размером ##max_width## X ##max_height##.';
        $lang['documents_must_be'] = 'Размер документа не должен превышать ##max_size##kb';
        $lang['document_information'] = 'Данные о документе:';
        $lang['image_information'] = 'Данные об изображении:';
        $lang['file_delete_warning'] = 'Предупреждение: Файл будет удален. Продолжить?';
        $lang['folder_delete_warning'] = 'Предупреждение: Директория и ее содержимое будет удалено. Продолжить?';
        $lang['click_browse'] = "Нажмите 'Browse...', выберите файл. Потом нажмите 'Загрузить'.";
        $lang['select_image_first'] = 'Для начала выберите изображение.';
        $lang['rename'] = 'Переименовать';
        $lang['move'] = 'Переместить';
        $lang['delete'] = 'Удалить';
        $lang['type_document_address'] = 'Вы можете ввести URL изображение на другом сайте.';
        $lang['type_image_address'] = 'Вы можете ввести URL документа на другом сайте.';
        $lang['parent_directory'] = 'Родительская директория';
        $lang['name'] = 'Название:';
        $lang['type'] = 'Тип:';
        $lang['action'] = 'Действие:';
        $lang['source'] = 'Источник:';
        $lang['address'] = 'Адрес:';
        $lang['size'] = 'Размер:';
        $lang['width'] = 'Ширина:';
        $lang['height'] = 'Высота:';
        $lang['dimensions'] = 'Размеры:';
        $lang['upload_file'] = 'Загрузить';

        // filetype descriptions
        $lang['files']['folder'] = 'Директория';
        $lang['files']['html'] = 'HTML Document';
        $lang['files']['pdf'] = 'Adobe Acrobat PDF';
        $lang['files']['doc'] = 'Word Document';
        $lang['files']['xl'] = 'Excel Spreadsheet';
        $lang['files']['ppt'] = 'PowerPoint';
        $lang['files']['pps'] = 'PowerPoint';
        $lang['files']['rtf'] = 'Rich Text';
        $lang['files']['txt'] = 'Plain Text';
        $lang['files']['zip'] = 'Zip File';
        $lang['files']['tar'] = 'Tar File';
        $lang['files']['swf'] = 'Flash Movie';
        $lang['files']['wmv'] = 'Windows Media';
        $lang['files']['rm'] = 'Real Media';
        $lang['files']['mov'] = 'QuickTime';
        $lang['files']['jpg'] = 'JPEG Image';
        $lang['files']['gif'] = 'GIF Image';
        $lang['files']['png'] = 'PNG Image';
        $lang['files']['exe'] = 'Приложение';
        $lang['files']['file'] = 'Файл';

        // edittable
        $lang['titles']['edittable'] = 'Редактировать свойства таблицы, ячейки или строки';
        $lang['this_cell'] = 'Ячейка';
        $lang['this_row'] = 'Строка';
        $lang['this_table'] = 'Таблица';
        $lang['cell_size'] = 'Размер ячейки:';
        $lang['cell_width'] = 'Ширина ячейки:';
        $lang['cell_height'] = 'Высота ячейки:';
        $lang['text_alignment'] = 'Выравнивание текста:';
        $lang['vertical_alignment'] = 'Вертикальное:';
        $lang['top'] = 'Поверху';
        $lang['middle'] = 'Посередине';
        $lang['bottom'] = 'Понизу';
        $lang['base_line'] = 'Baseline';
        $lang['horizontal_alignment'] = 'Горизонтальное:';
        $lang['left'] = 'Слева';
        $lang['center'] = 'По центру';
        $lang['right'] = 'Справа';
        $lang['borders'] = 'Границы:';
        $lang['background_color'] = 'Цвет фона:';
        $lang['background_image'] = 'Изображение фона:';
        $lang['choose'] = 'Выбрать...';
        $lang['spacing_and_padding'] = 'Отступы:';
        $lang['cell_spacing'] = 'Интервалы:';
        $lang['cell_padding'] = 'Отступы в ячейке:';
        $lang['table_size'] = 'Размер таблицы:';
        $lang['table_width'] = 'Ширины таблицы:';
        $lang['table_height'] = 'Высота таблицы:';
        $lang['alignment'] = 'Выравнивание:';
        $lang['align_on_page'] = 'Выравнивание на странице:';
        $lang['border_size'] = 'Размеры рамок:';
        $lang['border_color'] = 'Цвет рамок:';
        $lang['border_collapse'] = 'Видимые границы:';
        $lang['style_preview'] = 'Просмотр стиля:';
        $lang['row'] = 'Строка:';
        $lang['cell'] = 'Ячейка:';

        // find
        // values surrounded by '##' are variables that are dynamically inserted and should be left untouched!
        $lang['titles']['find'] = 'Найти и Заменить';
        $lang['please_complete_find_what'] = "Введите текст в поле 'Искать:'.";
        $lang['reached_bottom1'] = 'Поиск достиг конца документа.\nНажмите OK чобы продолжить поиск сначала, Нажмите Отмена стобы изменить поисковые слова.';
        $lang['reached_bottom2'] = 'Поиск достиг конца документа.\nНайдено ##matches## результатов.\nНажмите OK чобы продолжить поиск сначала, Нажмите Отмена стобы изменить поисковые слова.';
        $lang['find_what'] = 'Найти:';
        $lang['replace_with'] = 'Заменить на:';
        $lang['match_case'] = 'Соблюдать регистр';
        $lang['match_word'] = 'Искать целое слово';
        $lang['find_next'] = 'Найти далее';
        $lang['replace'] = 'Заменить';
        $lang['replace_all'] = 'Заменить все';

        // hyperlink
        $lang['titles']['hyperlink'] = 'Вставить ссылку';
        $lang['link_to'] = 'Ссылка на:';
        $lang['place_on_website'] = 'Документ на сайте';
        $lang['place_in_document'] = 'Ссылка на закладку';
        $lang['email_address'] = 'Адрес E-mail';
        $lang['web_location'] = 'Место Web';
        $lang['title'] = 'Заголовок:';
        $lang['window'] = 'Окно:';
        $lang['same_window'] = 'Открыть в этом окне';
        $lang['parent_window'] = 'Открыть в родительском окне';
        $lang['new_window'] = 'Открыть в новом окне';
        $lang['top_window'] = 'Открыть в верхнем окне';
        $lang['email_address2'] = 'Адрес E-mail:';
        $lang['subject'] = 'Тема:';
        $lang['creates_popup_message'] = 'Создает сообщение в pop-up (наподобие того, что вы читаете сейчас).';
        $lang['example'] = 'Пример:';
        $lang['type_a_subject'] = 'Вы можете ввести тему сообщение';
        $lang['top_of_document'] = 'Вверх документа';
        $lang['bookmarks'] = 'Закладки:';

        // imageoptions
        $lang['titles']['imageoptions'] = 'Свойства изображения';
        $lang['positioning'] = 'Размещение:';
        $lang['text_flow'] = 'Обтекание текстом:';
        $lang['absmiddle'] = 'Absolute Middle';
        $lang['texttop'] = 'Text Top';
        $lang['absbottom'] = 'Absolute Bottom';
        $lang['distance_to_surrounding_text'] = 'Отступы от текста:';
        $lang['pixels'] = 'pixels';
        $lang['top2'] = 'Вверху:';
        $lang['bottom2'] = 'Внизу:';
        $lang['left2'] = 'Слева:';
        $lang['right2'] = 'Справа:';
        $lang['positioning_preview'] = 'Предпросмотр:';
        $lang['choose_different_image'] = 'Выбрать другое изображение...';
        $lang['border'] = 'Рамка:';
        $lang['reset_dimensions'] = 'Сбросить';

        // insert_hr
        $lang['titles']['insert_hr'] = 'Вставить горизонтальную линию';
        $lang['horizontal_rule'] = 'Горизонтальная линия:';
        $lang['color'] = 'Цвет:';

        // mrgcell
        $lang['titles']['mrgcell'] = 'Объеденить ячейки';
        $lang['merge_cell2'] = 'Объеденить ячейки:';
        $lang['link_to'] = 'Ссылка к:';

        // no_preview
        $lang['no_preview'] = 'Предпросмотр недоступен';

        // pastewin
        $lang['titles']['pastewin'] = 'Вставить из Word';
        $lang['paste_word_contents_below'] = 'Это уберет лишний мусор из Word HTML. Вставьте содержимое документа. (Windows: Ctrl + v, Mac: Apple + v) Потом нажмите кнопку Вставить.';
        $lang['remove_styles'] = 'Очистить';

        // selcolor
        $lang['titles']['selcolor'] = 'Выберите цвет';
        $lang['web_colors'] = 'Цвета Web';
        $lang['site_colors'] = 'Цвета сайта';

        // smileys
        $lang['titles']['smileys'] = 'Смайлики';
        $lang['emoticon_smilies'] = 'Смайлики:';
        $lang['smile'] = 'Smile';
        $lang['embarrassed'] = 'Embarrassed';
        $lang['wink'] = 'Wink';
        $lang['star'] = 'Star';
        $lang['shocked'] = 'Shocked';
        $lang['dead'] = 'Dead';
        $lang['big_smile'] = 'Big smile';
        $lang['sleepy'] = 'Sleepy';
        $lang['confused'] = 'Confused';
        $lang['disapprove'] = 'Disapprove';
        $lang['unhappy'] = 'Unhappy';
        $lang['shocked'] = 'Shocked';
        $lang['approve'] = 'Approve';
        $lang['angry'] = 'Angry';
        $lang['evil_smile'] = 'Evil smile';
        $lang['shocked'] = 'Shocked';
        $lang['clown'] = 'Clown';
        $lang['cool'] = 'Cool';

        // special_characters
        $lang['titles']['special_characters'] = 'Специальные символы';
        $lang['special_characters2'] = 'Специальные символы:';
        $lang['insert2'] = 'Вставить:';

        // table
        $lang['titles']['table'] = 'Вставить таблицу';
        $lang['rows'] = 'Строки:';
        $lang['columns'] = 'Колонки:';

        // unmrgcell
        $lang['titles']['unmrgcell'] = 'Резделить ячейку';
        $lang['unmerge_cell2'] = 'Резделить ячейку:';

        // numerals
        $lang['0'] = '0';
        $lang['1'] = '1';
        $lang['2'] = '2';
        $lang['3'] = '3';
        $lang['4'] = '4';
        $lang['5'] = '5';
        $lang['6'] = '6';
        $lang['7'] = '7';
        $lang['8'] = '8';
        $lang['9'] = '9';

        $this->data = $lang;
    }

    function getData() {
        return $this->data;
    }
}
