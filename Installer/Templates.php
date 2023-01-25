<?php

class Templates {

    function pageHTML( $content, $styles = '' ) { return <<<EOF
<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>CMS://installation</title>
	<link rel="stylesheet" type="text/css" href="/assets/install/installer.css" />
	<style>{$styles}</style>
</head>
<body>
	{$content}
</body>
</html>
EOF;
    }

    function formHTML( $host, $user, $pass, $name, $username, $password ) { return <<<EOF
<form method="post">    
    <div class="table-form">
        <h2>RAMPAGECMS//installer</h2>

        <fieldset>
            <legend>Database connection</legend>
            
            <div class="row">
                <span>host</span>
                <input type="text" name="db_host" value="{$host}" />
            </div>
            <div class="row">
                <span>name</span>
                <input type="text" name="db_name" value="{$name}" />
            </div>
            <div class="row">
                <span>user</span>
                <input type="text" name="db_user" value="{$user}" />
            </div>
            <div class="row">
                <span>password</span>
                <input type="password" name="db_password" value="{$pass}" />
            </div>
        </fieldset>
        
        <fieldset>
            <legend>Website administrator</legend>
            
            <div class="row">
                <span>username</span>
                <input type="text" name="username" value="{$username}" />
            </div>
            <div class="row">
                <span>password</span>
                <input type="password" name="password" value="{$password}" />
            </div>
        </fieldset>
        <input type="submit" name="install" value="Install" />
    </div>
</form>
EOF;
    }

    /**
     * @param string[] $list
     * @param string $class [success, error]
     * @return string
     */
    function messages( $list, $class ) {
        return '<div class="msg '.$class.'">'.join('<br/>', $list ).'</div>';
    }

    /**
     * @param string[] $success
     * @param string[] $errors
     * @return string
     */
    function messagesConsole( $success, $errors ) {
        $success = empty( $success ) ? '' : $this->messages( $success, 'success' );
        $errors = empty( $errors ) ? '' : $this->messages( $errors, 'error' );
        $datetime = date("D M j G:i:s T Y");

        return <<<EOF
<div class="messagesConsole">
    <div class="system">
        {$datetime}<br/>
        CMS:Installer $ install 
    </div>
    {$success}
    {$errors}
</div>
EOF;
    }
}