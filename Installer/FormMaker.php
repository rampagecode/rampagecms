<?php

class FormMaker {
    /**
     * @var Templates
     */
    private $view;

    /**
     * @param Templates $view
     */
    public function __construct(Templates $view ) {
        $this->view = $view;
    }

    /**
     * @param $text
     * @return string
     */
    function safe( $text ) {
        return htmlspecialchars( $_POST[ $text ] );
    }

    /**
     * @return string
     */
    function baseForm() {
        return $this->view->pageHTML(
            $this->view->formHTML( 'mysql', "root", "root", "rampagecms", "admin", '' )
        );
    }

    /**
     * @param string[] $errors
     * @param string[] $messages
     * @return void
     */
    function postForm( $errors = [], $messages = [] ) {
        $info = $this->view->messagesConsole( $messages, $errors );
        $form = $this->view->formHTML(
            $this->safe('db_host'),
            $this->safe('db_user'),
            $this->safe('db_password'),
            $this->safe('db_name'),
            $this->safe('username'),
            $this->safe('password')
        );

        echo $this->view->pageHTML($info.$form );
    }
}