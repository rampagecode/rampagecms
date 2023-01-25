<?php

namespace Admin\Action\Login;

use Admin\AdminControllerInterface;
use Admin\AdminControllerParameters;
use Admin\AdminSession;
use Admin\AdminException;
use App\AppInterface;
use App\Module\ModuleControllerProtocol;
use App\Parser\ParserInterface;
use App\Parser\TemplateParser;
use App\User\User;
use App\User\UserAuth;
use Lib\ResultInterface;

class LoginController implements AdminControllerInterface, ResultInterface  {
    /**
     * @var AppInterface
     */
    private $app;

    /**
     * @var string
     */
    private $content;

    /**
     * @var string
     */
    private $logs;

    /**
     * @var ParserInterface
     */
    private $parser;

    /**
     * @var AdminSession
     */
    private $session;

    /**
     * @var AdminControllerParameters
     */
    private $params;

    /**
     * @param AppInterface $app
     */
    public function __construct( AppInterface $app ) {
        $this->app = $app;
        $this->parser = new TemplateParser( $app );
    }

    public function auto_run() {
        $name = '';
        $text = '';

        try {
            if( $this->params->act == 'do_log_in' ) {
                $user = $this->authUser();
                $name = $user->getLogin();

                if( ! $user->getGroupObject( $this->app->config() )->getAccess()->canAccessAdmin() ) {
                    throw new AdminException('У вас нет прав на доступ к панели управления' );
                }

                $this->loginUser( $user );
            } else {
                if( $this->session instanceof AdminSession ) {
                    throw new \Exception( $this->session->getValidationMessage() );
                }
            }
        } catch( \Exception $e ) {
            $text = $e->getMessage();
        }

        $view = new LoginView();
        $this->setContent(
            $view->loginPage( '', $text, $name )
        );
    }

    private function loginUser( User $user ) {
        // Генерируем новый ключ авторизации
        $user->setLoginKey( \App\User\Password::generateAutologinKey() );
        $user->setLoginKeyExpire( time() + 86400 ); // Ключ авторизации сроком на один день
        $user->commit();

        $this->app->db()->insert('admin_sessions', [
            'session_id'                => $user->getLoginKey(),
            'session_ip_address'        => $this->app->getIpAddr(),
            'session_member_name'       => $user->getLogin(),
            'session_member_id'         => $user->getId(),
            'session_member_pass_hash'  => $user->getPassHash(),
            'session_location'          => 'acp',
            'session_log_in_time'       => time(),
            'session_running_time'      => time(),
        ]);

        $this->app->redirect( $this->app->baseURL() . '?adsess='.$user->getLoginKey() );
    }

    /**
     * @return User
     */
    private function authUser() {
        $username = $this->app->in('username' );
        $password = $this->app->in('password' );
        $table = new \Data\User\Table();
        $auth = new UserAuth();
        $auth->setLogin( $username );
        $auth->setPassword( $password );
        $row = $auth->auth( $table );

        return new User( $row );
    }

    function setContent( $content ) {
        $this->content = $content;
    }

    function setParser( ParserInterface $parser ) {
        $this->parser = $parser;
    }

    function getResult() {
        $this->content = str_replace('##embed_logs##', $this->logs, $this->content );

        if( $this->parser instanceof ParserInterface ) {
            $this->content = $this->parser->parse( $this->content );
        }

        return $this->content;
    }

    function embedLog( $log ) {
        $this->logs = $log;
    }

    /**
     * @param AdminSession $session
     * @return void
     */
    function setSession( AdminSession $session ) {
        $this->session = $session;
    }

    function availableActions() {
        return [];
    }

    function setRequestParameters( AdminControllerParameters $parameters ) {
        $this->params = $parameters;
    }
}