<?php

namespace App\Page;

use App\AppInterface;
use App\Parser\ParserInterface;

/**
 * Шаблон в HTML коде описывается выражением: <!--[TEMPLATE]OPTION--> где TEMPLATE может принимать следующим формы:
 * Простой плейсхолдер, например:
 * <!--[Текст]-->
 * где "Текст" является плейсхолдером, который по-умолчанию ничего не выводит и должен быть переопределен в панели управления.
 * Далее, определенный текст, например:
 * <!--[Текст~12]-->
 * где 12 это идентификатор контента в БД, текст которого и будет подставлен.
 * Вызов модуля:
 * <!--[Главное_меню=Menu]-->
 * такая запись означает, что у фронт-контроллера модуля Menu будет вызван метод auto_run.
 * <!--[Голосование=Polls*showAsList(10, true)]-->
 * означает, что у фронт-контроллера модуля Polls будет вызван метод showAsList с указанными параметрами.
 * Также можно указать имя параметра, значение которого нужно вывести, например:
 * <!--[Голосование=Polls*generate()?list]-->
 * выполнит метод generate() у фронт-контроллера модуля Polls и затем выведет значение свойства list контроллера.
 * <!--[Заголовок~12?title]-->
 * выведет заголовок контента с идентификатором 12.
 * Также можно ссылаться на другие шаблоны:
 * <!--[Заголовок~Текст?title]-->
 * Означает, что нужно вывести заголовок текста используемого в шаблоне Текст. При этом нужно обратить внимание,
 * что так как шаблон Текст может быть переопределен как модуль, то в таком случае заголовок выводится не будет, так как
 * этот шаблон обращается к нему как к контенту, используя нотацию ~.
 *
 * Элемент OPTION может быть опущен, либо применяться как с контентом, так и с модулем и иметь один из двух видов:
 * <!--[Меню=dynamic_menu*main_menu(3)]:noadmin--> или <!--[Меню=dynamic_menu*main_menu(3)]:adminon=32-->
 * значение "noadmin" означает что этот элемент должен быть скрыт от редактирования администратором сайта через панель управления
 * значение adminon и следующее за ним после знака равенства число означают идентификатор страницы на которой модуль доступен
 * для управления (например, только на главной странице)
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
            'longtitle' => $this->page->getLongTitle(),
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
        preg_match_all( "#<!--\[([^\]]+?)\](\?[\w\d\_]+|:noadmin|:adminon=([0-9]+))?-->#sie", $text, $w );

        $templates = [];

        foreach( $w[1] AS $k => $v ) {
            $pageTemplate = new PageTemplate( $page_id, $overloads, $w[1][$k], $w[2][$k] );
            $placeholder = $pageTemplate->getPlaceholder();

            if( !empty( $placeholder )) {
                $templates[] = $pageTemplate;
            }
        }

        return $templates;
    }
}