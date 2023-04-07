<?php

namespace Admin\Menu\Members;

use Admin\Action\PageContent\PageSettingsForm;
use Admin\AdminControllerInterface;
use Admin\AdminControllerParameters;
use Admin\Menu\Settings\Exception;
use Admin\WidgetsView;
use App\AppInterface;
use App\UI\FormBuilder;
use App\UI\FormSelectOptions;
use App\UI\InputBuilder;
use App\UI\Paginator;
use App\UI\SelectBuilder;
use App\UI\SelectOptionBuilder;
use App\UI\TableBuilder;
use App\UI\TagBuilder;
use App\User\User;
use App\User\UserGroup;
use Data\User\Table;
use Lib\FormProcessing;

class MembersController implements AdminControllerInterface {

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

    public function auto_run() {}

    function availableActions() {
        return [
            'doadd' => 'create',
            'mod' => 'approve',
            'searchresults' => 'searchResults',
        ];
    }

    /**
     * @param AdminControllerParameters $parameters
     * @return void
     */
    function setRequestParameters( AdminControllerParameters $parameters ) {
        $this->parameters = $parameters;
    }

    /**
     * @param $values
     * @return string
     */
    function addAction( $values = [] ) {
        $widgets = new WidgetsView();
        $builder = new TableBuilder();
        $builder->firstRowWidth = '40%';
        $builder->secondRowWidth = '60%';
        $model = new MembersModel( $this->app );
        $form = new FormCreate();
        $form->createRows( $values, $model->getGroupOption() );
        $form->action = $this->parameters->buildURL( null, 'doadd' );

        return $widgets->tableHeader( 'Регистрация пользователя' )
            . $form->buildTable( $builder, 'Сохранить' )
        ;
    }

    /**
     * @return string
     */
    function createAction() {
        $model = new MembersModel( $this->app );
        $form = new FormProcessing(( new FormCreate())->getItemsArray() );
        $form->process( $this->app->in() );
        $errors = $form->getErrors();
        $values = $form->getValues();
        $widgets = new WidgetsView();

        if( empty( $errors ) && ! $model->hasAccessToGroup( $values['group'] )) {
            $errors[] = 'Вы не можете назначить эту группу';
        }

        if( empty( $errors )) {
            $userTable = new Table();
            $userExists = $userTable->fetchRow(
                $userTable->select()
                    ->where('LOWER(login) = ?', strtolower( $values['login'] ))
                    ->orWhere('LOWER(dname) = ?', strtolower( $values['name'] ))
                    ->orWhere('LOWER(email) = ?', strtolower( $values['email'] ))
            );

            if( $userExists ) {
                if( strtolower( $userExists['login'] ) == strtolower( $values['login'] )) {
                    $errors[] = 'Пользователь с таким логином уже зарегистрирован';
                }
                elseif( strtolower( $userExists['dname'] ) == strtolower( $values['name'] )) {
                    $errors[] = 'Пользователь с таким именем уже зарегистрирован';
                }
                elseif( strtolower( $userExists['email'] ) == strtolower( $values['email'] )) {
                    $errors[] = 'Пользователь с таким адресом уже зарегистрирован';
                }
            }
        }

        if( ! empty( $errors )) {
            return $widgets->errorList( $errors )
                . $this->addAction( $values )
            ;
        }

        $pass_salt = \App\User\Password::generatePasswordSalt();
        $pass_hash = \App\User\Password::generateCompiledPasshash( $pass_salt, md5( $values['password'] ));
        $member = [
            'login'             => $values['login'],
            'dname' 			=> $values['name'],
            'pass_hash'         => $pass_hash,
            'pass_salt'         => $pass_salt,
            'email'             => $values['email'],
            'mgroup'            => $values['group'],
            'reg_time'          => time(),
            'ip_address'        => $this->app->getIpAddr(),
            'time_offset'       => $this->app->getVar('time_offset'),
        ];

        $db = $this->app->db();
        $db->beginTransaction();
        try {
            $db->insert('members', $member);
            $member_id = $db->lastInsertId();
            $db->insert('members_extra', ['member_id' => $member_id]);
            $db->commit();
        } catch( \Exception $e ) {
            $db->rollBack();
            return $widgets->error( $e->getMessage() );
        }

        return $widgets->messageBox('Новый пользователь успешно создан');
    }

    /**
     * @return string
     */
    function searchAction() {
        $model = new MembersModel( $this->app );
        $groups = $model->getGroupOption()->getOptions();
        $options = array_map( function( $title, $value ) {
            return new SelectOptionBuilder( $title, $value );
        }, array_keys( $groups ), array_values( $groups ));

        $builder = (new SelectBuilder())
            ->set( 'name', 'mgroup[]' )
            ->setOptions( $options )
            ->set( 'size', count( $options ))
        ;

        $view = new MembersView();
        $form = new FormBuilder( $view->searchForm([
            '_groups_list' => $builder->build(),
        ]));
        $form->method = 'get';
        $form->action = $this->parameters->buildURL(null, 'searchresults' );

        return $form->build();
    }

    /**
     * @return string
     */
    function searchResultsAction() {
        $widgets = new WidgetsView();
        $searchForm = new FormSearch( $this->app->in(), $this->app->db() );
        $searchForm->process();

        if( ! $searchForm->isSucceed() ) {
            return $widgets->errorList( $searchForm->getErrors() );
        }

        $countQuery = $searchForm->getQuery()
            ->columns(['count' => new \Zend_Db_Expr('COUNT(m.id)')])
            ->query()
            ->fetch();
        $countResult = $countQuery ? $countQuery['count'] : 0;
        $perPage = 12;
        $colsPerRow = 3;
        $st = intval( $this->app->in('st' ));
        $page_query = http_build_query( $searchForm->getValues() );
        $paginator_url = '[mod://]&i=searchresults&'.$page_query.'&st=';
        $paginator = new Paginator( $st, $countResult, $perPage, $paginator_url );
        $currentPage = $paginator->currentPage;
        $cellWidth = (100 / $colsPerRow).'%';
        $view = new MembersView();
        $query = $searchForm->getQuery()
            ->limitPage( $currentPage, $perPage )
            ->joinLeft(['x' => 'members_extra'], 'x.member_id = m.id', [

            ])
            ->joinLeft(['g' => 'groups'], 'g.g_id = m.mgroup', [
                'group_name' => 'g_title',
            ])
            ->query()
        ;

        $cells = [];
        $rows = [];

        while( $row = $query->fetch( \Zend_Db::FETCH_ASSOC )) {
            $deleteURL = $this->parameters->buildURL( $row['id'], 'delete');
            $deleteMsg = 'Точно удалить?';
            $deleteCmd = "confirm('{$deleteMsg}') ? window.location = '{$deleteURL}' : $.noop()";

            $actions = [
                $view->actionItem(
                    $this->parameters->buildURL( $row['id'], 'edit'),
                    'Редактировать'
                ),
                $view->actionItem(
                    "javascript:{$deleteCmd}",
                    'Удалить'
                ),
            ];
            $row['_dropdown'] = $view->actionsMenu( join("\n", $actions ));
            $row['_reg_time'] = date( 'd.m.Y', $row['reg_time'] );

            $cells[] = $view->searchResultsCell( $row, $cellWidth );

            if( count( $cells ) == $colsPerRow ) {
                $rows[] = $view->searchResultsRow( implode("\n", $cells ));
                $cells = [];
            }
        }

        $count = count( $cells );

        if( $count > 0 and $count != $colsPerRow ) {
            for( $i = $count ; $i < $colsPerRow ; ++$i ) {
                $cells[] = $view->searchResultsEmptyCell();
            }

            $rows[] = $view->searchResultsRow( implode("\n", $cells ));
        }

        return $view->searchResultsTable(
            $countResult,
            $paginator->resultHTML,
            $colsPerRow,
            implode("\n", $rows ),
            '[mod://]&i=searchresults&'.$page_query.'&i=deleteAll'
        );
    }

    /**
     * @param $values
     * @return string
     */
    function editAction( $values ) {
        $widgets = new WidgetsView();
        $userTable = new Table();
        $errors = [];
        $model = new MembersModel( $this->app );

        try {
            $userRow = $userTable->findById( $this->parameters->idx );
        } catch( \Exception $e ) {
            $errors[] = $e->getMessage();
        }

        if( empty( $userRow )) {
            $errors[] = 'Пользователь не найден';
        }

        if( empty( $errors ) && ! $model->hasAccessToGroup( $userRow['mgroup'] )) {
            $errors[] = 'Вы не можете редактировать этого пользователя';
        }

        if( !empty( $errors )) {
            return $widgets->errorList( $errors );
        }

        $builder = new TableBuilder();
        $builder->firstRowWidth = '40%';
        $builder->secondRowWidth = '60%';
        $model = new MembersModel( $this->app );
        $form = new FormCreate();

        if( empty( $values )) {
            $form->createFromRow( $userRow, $model->getGroupOption() );
        } else {
            $form->createRows( $values, $model->getGroupOption() );
        }

        $form->action = $this->parameters->buildURL( null, 'saveEdit' );
        $form->method = 'post';

        return $widgets->tableHeader( 'Редактирование пользователя' )
            . $form->buildTable( $builder, 'Сохранить' )
        ;
    }

    /**
     * @return string
     */
    function saveEditAction() {
        $form = new FormCreate();
        $pass = $this->app->in('pass');

        if( empty( $pass )) {
            $form->removeItemByName('pass');
        }

        $processing = new FormProcessing( $form->getItemsArray() );
        $processing->process( $this->app->in() );
        $errors = $processing->getErrors();
        $values = $processing->getValues();
        $widgets = new WidgetsView();
        $model = new MembersModel( $this->app );

        if( empty( $errors ) && ! $model->hasAccessToGroup( $values['group'] )) {
            $errors[] = 'Вы не можете назначить эту группу';
        }

        $userTable = new Table();

        try {
            $userRow = $userTable->findById( $this->parameters->idx );
        } catch( \Exception $e ) {
            $errors[] = $e->getMessage();
        }

        if( empty( $errors ) && ! $model->hasAccessToGroup( $userRow['mgroup'] )) {
            $errors[] = 'Вы не можете редактировать этого пользователя';
        }

        if( empty( $userRow )) {
            $errors[] = 'Пользователь не найден';
        }

        if( ! empty( $errors )) {
            return $widgets->errorList( $errors ) . $this->editAction( $values );
        }

        $pass_salt = \App\User\Password::generatePasswordSalt();
        $pass_hash = \App\User\Password::generateCompiledPasshash( $pass_salt, md5( $values['pass'] ));

        $userRow['pass_hash'] = $pass_hash;
        $userRow['pass_salt'] = $pass_salt;
        $userRow['login'] = $values['login'];
        $userRow['dname'] = $values['name'];
        $userRow['email'] = $values['email'];
        $userRow['mgroup'] = $values['group'];
        $userRow->save();

        return $widgets->messageBox('Изменения сохранены');
    }

    /**
     * @return string
     */
    function deleteAction() {
        $userTable = new Table();
        $errors = [];
        $widgets = new WidgetsView();
        $model = new MembersModel( $this->app );

        try {
            $userRow = $userTable->findById( $this->parameters->idx );
        } catch( \Exception $e ) {
            $errors[] = $e->getMessage();
        }

        if( empty( $userRow )) {
            $errors[] = 'Пользователь не найден';
        }

        if( empty( $errors ) && ! $model->hasAccessToGroup( $userRow['mgroup'] )) {
            $errors[] = 'Вы не можете удалить этого пользователя';
        }

        if( empty( $errors ) && $userRow['id'] == $this->app->getUser()->getId() ) {
            $errors[] = 'Вы не можете удалить себя';
        }

        if( ! empty( $errors )) {
            return $widgets->errorList( $errors );
        }

        $userRow->delete();

        return $widgets->messageBox('Пользователь удален');
    }

    function deleteAllAction() {
        $widgets = new WidgetsView();
        $searchForm = new FormSearch( $this->app->in(), $this->app->db() );
        $searchForm->process();

        if( ! $searchForm->isSucceed() ) {
            return $widgets->errorList( $searchForm->getErrors() );
        }

        $query = $searchForm->getQuery()->query();
        $errors = [];
        $userIDs = [];
        $model = new MembersModel( $this->app );
        $usersFound = 0;

        while( $row = $query->fetch( \Zend_Db::FETCH_ASSOC )) {
            $usersFound++;

            if( ! $model->hasAccessToGroup( $row['mgroup'] )) {
                $errors[] = 'Вы не можете удалить пользователя '.$row['dname'].'('.$row['login'].'):'.$row['id'];
            }
            else if( $row['id'] == $this->app->getUser()->getId() ) {
                $errors[] = 'Вы не можете удалить себя';
            }
            else {
                $userIDs[] = $row['id'];
            }
        }

        if( ! empty( $errors )) {
            return $widgets->errorList( $errors );
        }

        if( empty( $userIDs )) {
            return $widgets->error('Пользователей не найдено');
        }

        $usersApproved = count( $userIDs );
        $userTable = new Table();
        $usersDeleted = $userTable->delete('id IN ('.join(',', $userIDs).')');
        $result = [
            "Запрошено: {$usersFound} пользователей",
            "Найдено: {$usersApproved} пользователей",
            "Удалено: {$usersDeleted} пользователей",
        ];

        return $widgets->messageBox( join('<br>', $result ));
    }
}