<?php

namespace Module\Login\Frontend;

class LoginView {
    function errorMessage( $error ) {
        if (empty( $error )) {
            return '<div style="height: 20px;"></div>';
        } else {
            return '<div style="color: #de0a46; margin-bottom: 6px; width: 207px; padding-left: 2px; padding-top: 6px;">'
                . $error
                . '</div>'
            ;
        }
    }

    /**
     * Меню для входа на сайт для главного раздела и конференции
     */
    function login_form( $error = '', $referer ) { return <<<EOF
{$this->errorMessage($error)}
<form method="post" id="enter-form" action="">
        <label for="login">Логин <input type="text" name="login" id="login" /></label>
        <label for="passw">Пароль <input type="password" name="passw" id="passw" /></label>        
        
        <input type="submit" value="Войти" />
        <input type="hidden" name="a" value="login" />
        <input type="hidden" name="referer" value="{$referer}" />
        <br/>
        <a href="/remember/">Забыли пароль?</a>    
</form>
EOF;
    }

    /**
     *  Меню пользователя для главного раздела и конференции
     */
    function user_menu( $name, $msg, $logoutURL, $referer ) { return <<<EOF
<div class="login_form">
    <form method="post" action="">
        <label for="login">
            {$name} | <a href="[~34]" title="Настройки">Настройки</a> | <a href="{$logoutURL}" title="Выйти">Выйти</a>
        </label>
        <label for="passw" class="second">
            
        </label>

        <input type="hidden" name="a" value="login">
        <input type="hidden" name="referer" value="{$referer}">
    </form>
</div>
EOF;
    }
}
