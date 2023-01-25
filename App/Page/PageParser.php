<?php

namespace App\Page;

use App\AppInterface;
use App\Parser\ParserInterface;

/**
 * Шаблон в HTML коде описывается следующим выражением:
 * <!--[!CONTENT]OPTION-->
 * где CONTENT это обязательный параметр может принимать два вида:
 * <!--[!KEY=VALUE]--> или <!--[!KEY~VALUE]-->
 * KEY это placeholder который должен уникален в шаблоне
 * при написании через знак "~" значением VALUE должен быть целочисленный идентификатор контента, например:
 * <!--[!Текст_справа~12]--> подставит на это место текстовый контент из БД с идентификатором "12"
 * при написании через знак "=" правая часть считается вызовом модуля где VALUE может иметь вид:
 * <!--[!KEY=MODULE_NAME]--> где MODULE_NAME это имя модуля у класса которого будет вызван метод auto_run(), либо:
 * <!--[!KEY=MODULE_NAME*METHOD_NAME()]--> где METHOD_NAME это имя метода который будет вызван у класса модуля
 * Вызов метода может содержать параметры, например:
 * <!--[!Меню=menu*build(2, true)]-->
 * Если на странице есть несколько вызовов одного и того же метода с идентичной сигнатурой то метод будет вызван только один раз
 * Так же, метод может записать результат или некоторые промежуточные значения в свойства своего класса
 * Значения этих свойств могут быть запрошены следующим образом:
 * <!--[!Меню=menu*build(2, true)?title]--> или  <!--[!Меню=menu*build(2, true)]@result,title-->
 * В первом случае будет получено значение из свойства title, а во втором из элемента с ключом title у свойства result
 * Элемент OPTION может быть опущен, либо применяться как с контентом, так и с модулем и иметь один из двух видов:
 * <!--[!Меню=dynamic_menu*main_menu(3)]:noadmin--> или <!--[!Меню=dynamic_menu*main_menu(3)]:adminon=32-->
 * значение "noadmin" означает что этот элемент должен быть скрыт от редактирования администратором сайта через панель управления
 * значение adminon и следующее за ним после знака равенства число означают идентификатор страницы на которой модуль доступен для управления (например, только на главной странице)
 */
class PageParser implements ParserInterface {
    /**
     * @var AppInterface
     */
    private $app;

    /**
     * @var Page
     */
    private $page;

    /**
     * @param AppInterface $app
     * @param Page $page
     */
    public function __construct( AppInterface $app, Page $page ) {
        $this->app = $app;
        $this->page = $page;
    }

    /**
     * @param string $content
     * @return string
     */
    function parse( $content ) {
        $overloads = $this->page->getTemplates();
        $page_id = $this->page->getId();
        $templates = $this->parseTemplates( $page_id, $content, $overloads );
        $modulesProcessor = new PageModulesProcessor( $templates, $this->app );
        $modules = $modulesProcessor->process();
        $contentProcessor = new PageContentProcessor( $templates, $this->app );
        $texts = $contentProcessor->process();

		// Поменяем шаблоны в макете
		$content = str_replace( $modules['src'], $modules['val'], $content );
		$content = str_replace( $texts['src'], $texts['val'], $content );

		// Parse global variables [?var]
		$content = preg_replace( "#\[\?([a-z_0-9]+)\]#sie", "\$this->replaceVar('\\1')", $content );

        // Заменяем ссылки на страницы [~id]
		$content = preg_replace( "#\[\~([0-9]+)\]#sie", "\$this->app->pages()->pageAddressById('\\1')", $content );

        // Подстановка текста локализации [@text]
        $content = preg_replace( "#\[\@([a-z_0-9]+)\]#sie", "\$this->app->language()['\\1']", $content );

		return $content;
    }

    function replaceVar( $var ) {
        $pageSettings = [
            'pagetitle' => $this->page->getTitle(),
        ];

        if( isset( $pageSettings[ $var ] )) {
            return $pageSettings[ $var ];
        } else {
            return $this->app->getVar( $var );
        }
    }

    /**
     * @param $page_id
     * @param $text
     * @param $overloads
     * @return PageTemplate[]
     */
    function parseTemplates( $page_id, $text, $overloads ) {
        // Получаем перегруженные шаблоны страницы
        $overloads = @unserialize( $overloads );

        if( ! is_array( $overloads ) and count( $overloads )) {
            $overloads = array();
        }

        // Находим все шаблоны
        preg_match_all( "#<!--\[\!([^\]]+?)\](\?[\w\d\_]+|:noadmin|:adminon=([0-9]+))?-->#sie", $text, $w );

        $templates = [];

        foreach( $w[1] AS $k => $v ) {
            $templates[] = new PageTemplate( $page_id, $overloads, $w[1][$k], $w[2][$k] );
        }

        return $templates;
    }
}