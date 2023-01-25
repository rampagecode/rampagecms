<?php

namespace Admin\Menu;

use Admin\AdminControllerInterface;
use Admin\AdminControllerParameters;
use Admin\AdminSection;
use Admin\AdminException;
use Admin\JsonResult;
use Admin\PageResult;
use Admin\TabMenuView;
use Admin\WidgetsView;use App\AppInterface;
use App\Module\ModuleControllerProtocol;
use App\Module\ModuleFactory;
use App\Module\ModuleReader;
use App\Parser\ParserInterface;
use App\User\UserGroup;
use Lib\ResultInterface;

class MenuController implements AdminControllerInterface, ResultInterface {

    /**
     * @var AppInterface
     */
    private $app;

    /**
     * @var AdminSection[]
     */
    private $map;

    /**
     * @var ResultInterface
     */
    private $result;

    /**
     * @var string
     */
    private $content;

    /**
     * @var bool
     */
    private $hasAccess;

    /**
     * @var AdminControllerParameters
     */
    private $parameters;

    /**
     * @param AppInterface $app
     */
    public function __construct( AppInterface $app ) {
        $this->app = $app;
        $this->map = [
            'desktop' => new AdminSection('Desktop'),
            'content' => new AdminSection(null, [
                'page' => 'Content',
            ]),
            'users' => new AdminSection('UsersSection', [
                'members'	=> 'members',
                'groups'	=> 'groups',
            ]),
            'system' => new AdminSection('SystemSection', [
                'system' => 'system',
                //'admenu'	=> 'admin_menu',
                //'settbuild'	=> 'setts_builder',
                //'taskman'	=> 'task_manager',
                //'vfsmgr'	=> 'vfs_managers',
                //'modules'	=> 'modules',
            ]),
            'tree'  => new AdminSection('Tree'),
            'module'=> new AdminSection('ModuleSection', null),
            'settings' => new AdminSection('SettingsSection', [
                'conf'      => 'Settings',
                'members'	=> 'members',
            ]),
        ];
    }

    public function setRequestParameters( AdminControllerParameters $parameters ) {
        $this->parameters = $parameters;

        if( ! isset( $this->map[ $this->parameters->tab ] )) {
            $this->parameters->tab = 'desktop';
            $this->parameters->mod = $this->parameters->act = $this->parameters->idx = null;
        }
    }

    function availableActions() {
        return $this->map[ $this->parameters->tab ]->modules;
    }

    /**
     * @return string
     */
    public function auto_run() {
        $user = $this->app->getUser();
        $userGroup = empty( $user ) ? null : $user->getGroupObject( $this->app->config() );

        if( $this->app->isAjaxRequest() ) {
            $this->result = new JsonResult();
        } else {
            $tabs = $this->buildMainMenu(
                $userGroup,
                $this->parameters->tab,
                $userGroup->getAccess()->adminAccess()->canAccessContent()
            );
            $view = new MenuView();

            $this->result = new PageResult(
                $view,
                $tabs,
                $this->app->getVar('site_name'),
                $this->samePageLink( $view ),
                DEBUG_ON
            );
        }

        if( empty( $userGroup ) || false == $userGroup->getAccess()->adminAccess()->canAccess( $this->parameters->tab )) {
            $this->hasAccess = false;
        } else {
            $this->hasAccess = true;
        }

        return '';
    }

    /**
     * @return ModuleControllerProtocol|null
     * @throws AdminException|\App\AppException
     */
    function loadModule() {
        if( ! empty( $this->parameters->tab )) {
            if( ! isset( $this->map[ $this->parameters->tab ] )) {
                throw new AdminException('Wrong module name');
            }

            $section = $this->map[ $this->parameters->tab ];

            if( ! empty( $section->name )) {
                $modPath = $this->app->rootDir('Admin', 'Menu', ucfirst( $section->name ));
                $module = ModuleFactory::loadModuleController( $modPath, 'Admin\Menu', $this->app );

                if( $module instanceof AdminControllerInterface ) {
                    $module->setRequestParameters( $this->parameters );
                }

                return $module;
            }
        }

        return null;
    }

    function getResult() {
        try {
            if( ! $this->hasAccess ) {
                throw new AdminException('Вы не имеете доступа к запрошенному разделу функций управления');
            }

            $module = $this->loadModule();

            if( $module ) {
                $content = $module->auto_run();

                if( $module instanceof ResultInterface ) {
                    $module->setContent( $this->content );
                    $content = $module->getResult();
                }
            }
        } catch( \Exception $e ) {
            $view = new MenuView();
            $widgets = new WidgetsView();
            $content = $view->without_menu( $widgets->error( $e->getMessage() ));
        }

        if( ! isset( $content )) {
            $content = $this->content;
        }

        $this->result->setContent( $content );

        return $this->result->getResult();
    }

    function embedLog( $log ) {
        $this->result->embedLog( $log );
    }

    function setContent( $content ) {
        $this->content = $content;
    }

    function setParser( ParserInterface $parser ) {
        $this->result->setParser( $parser );
    }

    /**
     * @param UserGroup $group
     * @param string $section
     * @param bool $canAccessContent
     * @return string
     */
    function buildMainMenu( UserGroup $group, $section, $canAccessContent ) {
        $menu = [
            'desktop' => [
                'active'	=> 0,
                'visible'	=> $group->getAccess()->adminAccess()->canAccessDesktop(),
                'icon'		=> 'desktop.gif',
                'title'		=> 'Рабочий стол',
                'link'		=> 'desktop',
            ],
            'tree' => [
                'active'	=> 0,
                'visible'	=> $group->getAccess()->adminAccess()->canAccessTree(),
                'icon'		=> 'tab_tree.gif',
                'title'		=> 'Структура сайта',
                'link'		=> 'tree',
            ],
            'module' => [
                'active'	=> 0,
                'visible'	=> $group->getAccess()->adminAccess()->canAccessModule(),
                'icon'		=> 'tab_content.png',
                'title'		=> 'Модули',
                'link'		=> 'module',
            ],
            'UsersSection' => [
                'active'	=> 0,
                'visible'	=> $group->getAccess()->adminAccess()->canAccessUsers(),
                'icon'		=> 'user.png',
                'title'		=> 'Пользователи',
                'link'		=> 'users',
            ],
            'SettingsSection' => [
                'active'	=> 0,
                'visible'	=> $group->getAccess()->adminAccess()->canAccessSettings(),
                'icon'		=> 'tab_manage.png',
                'title'		=> 'Настройки',
                'link'		=> 'settings',
            ],
            'SystemSection' => [
                'active'	=> 0,
                'visible'	=> $group->getAccess()->adminAccess()->canAccessSystem(),
                'icon'		=> 'tab_system.png',
                'title'		=> 'Системные функции',
                'link'		=> 'system',
            ],
        ];

        $view = new TabMenuView();
        $tabs = '';

        foreach( $menu AS $s ) {
            $css_class = $s['active'] ? 'tabon' : ( $section == $s['link'] ? 'tabon' : 'taboff' );

            if( $s['visible'] ) {
                $tabs .= $view->tab(
                    $css_class,
                    $this->app->getVar('img').$s['icon'],
                    '[env://]&s='.$s['link'],
                    $s['title']
                );
            }
        }

        if( $canAccessContent ) {
            return $view->main(
                $view->tabs( $view->space() . $tabs ),
                $view->menu()
            );
        } else {
            return $view->main(
                $view->tabs( $tabs ),
                ''
            );
        }
    }

    private function samePageLink( MenuView $view ) {
        $link_out = '';

        if( $this->parameters->mod == 'page' ) {
            $site_tree = [];
            include $this->app->rootDir('conf', 'cache.tree.php');

            if( array_key_exists( $this->parameters->idx, $site_tree )) {
                $link_out = $view->site_link( $site_tree[ $this->parameters->idx ] );
            }
        }

        $page_id = intval( $this->app->in('pageid'));

        if( empty( $link_out ) && $page_id > 0 ) {
            $site_tree = [];
            include $this->app->rootDir('conf', 'cache.tree.php');

            if( array_key_exists( $page_id, $site_tree )) {
                $link_out = $view->site_link( $site_tree[ $page_id ] );
            }
        }

        if( empty( $link_out )) {
            $link_out = $view->site_link( '/' );
        }

        return $link_out;
    }
}