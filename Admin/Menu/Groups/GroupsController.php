<?php

namespace Admin\Menu\Groups;

use Admin\AdminControllerInterface;
use Admin\AdminControllerParameters;
use Admin\ButtonsView;
use Admin\Menu\Settings\Exception;
use Admin\WidgetsView;
use App\Access\GroupAccess;
use App\Access\ResourceAccess;
use App\AppInterface;
use App\Page\Page;
use App\UI\FormBuilder;
use App\UI\SelectBuilder;
use App\UI\SelectOptionBuilder;
use App\UI\TableBuilder;
use App\User\User;
use App\User\UserGroup;
use Data\UserGroup\Row;
use Data\UserGroup\Table;
use Lib\FormProcessing;

class GroupsController implements AdminControllerInterface {

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

    /**
     * @param AdminControllerParameters $parameters
     * @return void
     */
    function setRequestParameters( AdminControllerParameters $parameters ) {
        $this->parameters = $parameters;
    }

    public function auto_run() {
        return $this->showGroups();
    }

    function availableActions() {
        return [

        ];
    }

    function showGroups() {
        $view = new GroupsView();
        $buttons = new ButtonsView();
        $content = '';
        $model = new GroupsModel( $this->app->db() );
        $groups = $model->fetchGroupsList();
        $selectGroup = new SelectBuilder();

        foreach( $groups as $r ) {
            $editButton = $buttons->edit( $this->parameters->buildURL( $r['g_id'], 'edit' ));
            $deleteButton = $r['g_id'] <= 4
                ? $buttons->noDel()
                : $buttons->delete( $this->parameters->buildURL( $r['g_id'], 'delete' ))
            ;

            $_title = $r['prefix'] . $r['g_title'] . $r['suffix'];

            if( $this->app->getVar('admin_group') == $r['g_id'] ) {
                $_title .= ' <small><i>(super admin)</i></small>';
            }

            $content .= $view->groupListRow(
                $editButton,
                $deleteButton,
                $_title,
                $r['g_id'],
                $r['members_count']
            );

            $selectGroup->addOption( new SelectOptionBuilder( $r['g_title'], $r['g_id'] ));
        }

        return $view->userGroupsList( $content, $selectGroup->build() );
    }

    function editAction( $values = null ) {
        $canAccessGroups = $this->app->getUser()->getGroupObject()->getAccess()->adminAccess()->canAccessGroups();
        $widgets = new WidgetsView();

        if( ! $canAccessGroups ) {
            return $widgets->messageBox('У вас нет прав на редактирование групп пользователей');
        }

        if( !empty( $this->parameters->idx )) {
            $groupTable = new Table();
            $groupRow = $groupTable->fetchRow('g_id = ' . $this->parameters->idx);
        } else {
            $groupRow = null;
        }

        $submitTitle = empty( $groupRow ) ? 'Создать' : 'Обновить';
        $tableHeader = empty( $groupRow ) ? 'Создание группы пользователей' : "Редактирование группы '{$groupRow['g_title']}'";
        $builder = new TableBuilder();
        $form = new GroupEditForm();

        if( !empty( $values ) && !empty( $groupRow )) {
            $groupRow->setFromArray( $values );
        }

        $userGroup = new UserGroup( $groupRow );

        $content = $widgets->tableHeader( $tableHeader );
        $content .= $form
            ->createRows( $userGroup )
            ->buildTable( $builder, $submitTitle )
        ;

        return $content;
    }

    function saveAction() {
        $canAccessGroups = $this->app->getUser()->getGroupObject()->getAccess()->adminAccess()->canAccessGroups();
        $widgets = new WidgetsView();

        if( ! $canAccessGroups ) {
            return $widgets->messageBox('У вас нет прав на изменение групп пользователей');
        }

        $form = new GroupEditForm();
        $processing = new FormProcessing( $form->items );
        $processing->process( $this->app->in() );
        $errors = $processing->getErrors();
        $values = $processing->getValues();
        $widgets = new WidgetsView();
        $fields = [];

        if( !empty( $errors )) {
            return $widgets->errorList( $errors ) . $this->editAction( $values );
        }

        foreach( $values as $key => $value ) {
            ResourceAccess::unFlattenResource( $key, $value, $fields );
        }

        $fields['suffix'] = html_entity_decode( $fields['suffix'] );
        $fields['prefix'] = html_entity_decode( $fields['prefix'] );
        $fields['g_access'] = json_encode( $fields['g_access'] );

        $groupTable = new Table();
        $groupRow = $groupTable->fetchRow('g_id = ' . $this->parameters->idx );

        if( empty( $groupRow )) {
            $groupRow = (new Table())->createRow( $fields );
            $message = 'Запись создана';
        } else {
            $groupRow->setFromArray( $fields );
            $message = 'Изменения сохранены';
        }

        $groupRow->save();

        return $widgets->messageBox( $message ) . $this->showGroups();
    }

    function addAction() {
        return $this->editAction();
    }

    function deleteAction() {
        $canAccessGroups = $this->app->getUser()->getGroupObject()->getAccess()->adminAccess()->canAccessGroups();
        $widgets = new WidgetsView();

        if( ! $canAccessGroups ) {
            return $widgets->error('У вас нет прав на удаление групп пользователей');
        }

        $groupId = (int)$this->parameters->idx;

        if( empty( $groupId )) {
            return $widgets->error('Идентификатор группы не указан');
        }

        $groupTable = new Table();
        $groupRow = $groupTable->fetchRow('g_id = '.$groupId );

        if( empty( $groupRow )) {
            return $widgets->error('Запись группы не найдена');
        }

        $userGroup = new UserGroup( $groupRow );
        $userTable = new \Data\User\Table();
        $usersNumber = (int)$userTable->select()
            ->from( $userTable, 'COUNT(*) as usersCount')
            ->where('mgroup = ?', $groupRow['g_id'] )
            ->query()
            ->fetchColumn()
        ;

        $select = new SelectBuilder();
        $select->name = 'new_group_id';
        $groups = $groupTable->fetchAll()->toArray();
        foreach( $groups as $group ) {
            if( $group['g_id'] != $userGroup->id() ) {
                $select->addOption( new SelectOptionBuilder( $group['g_title'], $group['g_id'] ));
            }
        }

        $table = new TableBuilder();
        $table->addInput( $usersNumber, 'Количество пользователей в группе' );
        $table->addInput( $select->build(), 'Переместить этих пользователей в группу...');
        $table->addSubmit('Удалить группу');

        $form = new FormBuilder( $table->build() );
        $form->addHidden('x', $userGroup->id() );
        $form->addHidden('i', 'remove');

        $content = $widgets->tableHeader( "Подтверждение удаления группы: ".$userGroup->getTitle() );
        $content .= $form->build();

        return $content;
    }

    function removeAction() {
        $canAccessGroups = $this->app->getUser()->getGroupObject()->getAccess()->adminAccess()->canAccessGroups();
        $widgets = new WidgetsView();

        if( ! $canAccessGroups ) {
            return $widgets->error('У вас нет прав на удаление групп пользователей');
        }

        $groupId = (int)$this->parameters->idx;

        if( empty( $groupId )) {
            return $widgets->error('Идентификатор группы для удаления не указан');
        }

        $newGroupId = (int)$this->app->in('new_group_id');

        if( empty( $newGroupId )) {
            return $widgets->error('Идентификатор группы для переноса не указан');
        }

        $requiredGroups = [ User::GROUP_AUTH, User::GROUP_GUEST, User::GROUP_MEMBER, User::GROUP_ADMIN ];

        if( in_array( $groupId, $requiredGroups )) {
            return $widgets->error('Эту группу нельзя удалить');
        }

        $groupTable = new Table();
        $newGroupRow = $groupTable->fetchRow('g_id = '.$newGroupId );

        if( empty( $newGroupRow )) {
            return $widgets->error('Группа для переноса не найдена');
        }

        $userTable = new \Data\User\Table();
        $userTable->getAdapter()->beginTransaction();
        $userTable->update([ 'mgroup' => $newGroupId ], 'mgroup = ' . $groupId );
        $groupTable->delete('g_id = '.$groupId );
        $userTable->getAdapter()->commit();

        return $widgets->messageBox('Группа удалена') . $this->showGroups();

    }
}