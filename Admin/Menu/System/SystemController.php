<?php

namespace Admin\Menu\System;

use Admin\AdminControllerInterface;
use Admin\AdminControllerParameters;
use Admin\WidgetsView;
use App\AppInterface;
use App\UI\FormBuilder;
use App\UI\InputBuilder;
use App\UI\TableBuilder;
use Data\Page\Table;
use Lib\FileFlag;
use Lib\FormItem\Bool;

class SystemController implements AdminControllerInterface {

    /**
     * @var AppInterface
     */
    private $app;

    /**
     * @var AdminControllerParameters
     */
    private $parameters;

    /**
     * @param AppInterface $app
     */
    public function __construct( AppInterface $app ) {
        $this->app = $app;
    }

    function setRequestParameters( AdminControllerParameters $parameters ) {
        $this->parameters = $parameters;
    }

    function availableActions() {
        return [];
    }

    public function auto_run() {}

    /**
     * Shows UI with a button to reset cache
     * @return string
     */
    function cachingAction() {
        $submit = InputBuilder::make('input')
            ->set('type', 'submit')
            ->set('class', 'popup-button')
            ->set('value', "Очистить кеш")
            ->build()
        ;

        $form = new FormBuilder($submit);
        $form->addHidden('i', 'clearCache' );

        $builder = new TableBuilder();
        $builder->firstRowWidth = '300px';
        $builder->secondRowWidth = 'auto';
        $builder->addInput( $form->build(), 'Очистка кеша', 'Будет удален кеш настроек и кеш дерева сайта');

        $widgets = new WidgetsView();

        return $widgets->tableHeader('Кеширование') . $builder->build();
    }

    /**
     * Resets cache and shows a message
     * @return string
     */
    function clearCacheAction() {
        $this->app->config()->updateSettingsCache();
        $this->app->pages()->updateCache(
            $this->app->rootDir('conf', 'cache.tree.php'),
            new Table()
        );

        $widgets = new WidgetsView();

        return $widgets->messageBox('Кеш обновлен');
    }

    /**
     * Show UI with a toggle to switch between site blocked or not
     * @return string
     */
    function blockingAction() {
        $builder = new TableBuilder();
        $builder->firstRowWidth = '500px';
        $builder->secondRowWidth = 'auto';

        $form = new FormBuilder('');
        $form->addHidden('i', 'blockSite' );
        $form->addToggle(
            new Bool(
                'block',
                'Заблокировать сайт для посетителей',
                true
            ), $this->app->isSiteOffline(),
            'Сайт будет закрыт для всех пользователей, кто не имеет специального доступа'
        );

        $widgets = new WidgetsView();

        return $widgets->tableHeader('Блокировка') . $form->buildTable( $builder, 'Применить' );
    }

    /**
     * Turns site on/off by using a file
     * @return string
     */
    function blockSiteAction() {
        $block = $this->app->in('block');
        $widgets = new WidgetsView();

        if( $this->app->toggleOffline( !!$block )) {
            if( $block ) {
                return $widgets->messageBox('Сайт заблокирован для всех кроме групп пользователей с доступом');
            } else {
                return $widgets->messageBox('Сайт разблокирован');
            }
        } else {
            if( $block ) {
                return  $widgets->error('Не удалось заблокировать сайт');
            } else {
                return  $widgets->error('Не удалось разблокировать сайт');
            }
        }
    }

    /**
     * Show UI with a toggle to switch debug mode on/off
     * @return string
     */
    function debuggingAction() {
        $builder = new TableBuilder();
        $builder->firstRowWidth = '550px';
        $builder->secondRowWidth = 'auto';

        $form = new FormBuilder('');
        $form->addHidden('i', 'debugSite' );
        $form->addToggle(
            new Bool(
                'debug',
                'Включить режим отладки',
                true
            ), DEBUG_ON,
            'На сайте будет выводиться дополнительная информация, такая как: ошибки, логи, запросы к БД'
        );

        $widgets = new WidgetsView();

        return $widgets->tableHeader('Отладка') . $form->buildTable( $builder, 'Применить' );
    }

    /**
     * Toggles debug mode
     * @return string
     */
    function debugSiteAction() {
        $debugMode = $this->app->in('debug');
        $debugFlag = new FileFlag( $this->app->rootDir('conf', 'debug_on' ));
        $widgets = new WidgetsView();

        if( $debugFlag->setFlag( !!$debugMode )) {
            if( $debugMode ) {
                return $widgets->messageBox('Режим отладки включен');
            } else {
                return $widgets->messageBox('Режим отладки отключен');
            }
        } else {
            if( $debugMode ) {
                return  $widgets->error('Не удалось включить режим отладки');
            } else {
                return  $widgets->error('Не удалось выключить режим отладки');
            }
        }
    }
}