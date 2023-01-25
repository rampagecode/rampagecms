<?php

namespace Admin\Menu\Members;

use App\AppInterface;
use App\UI\FormBuilder;
use App\UI\FormSelectOptions;

class MembersModel {

    /**
     * @var AppInterface
     */
    private $app;

    public function __construct( AppInterface $app ) {
        $this->app = $app;
    }

    /**
     * @return FormSelectOptions
     */
    function getGroupOption() {
        $groupRows = $this->app->db()->select()->from('groups')->order('g_title')->query()->fetchAll();
        $groupOptions = new FormSelectOptions();

        foreach( $groupRows as $r ) {
            if( $this->app->getVar('admin_group') == $r['g_id'] ) {
                if( $this->app->getUser()->isWebmaster() ) {
                    continue;
                }
            }

            $groupOptions->addOption( $r['g_title'], $r['g_id'] );
        }

        return $groupOptions;
    }

    /**
     * @param int $groupId
     * @return bool
     */
    function hasAccessToGroup( $groupId ) {
        $adminGroup = $this->app->getVar('admin_group');

        if( $groupId == $adminGroup ) {
            if( $this->app->getUser()->getGroup() != $adminGroup ) {
                return false;
            }
        }

        return true;
    }
}