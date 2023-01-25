<?php

namespace App\Task;

use App\AppInterface;
use Data\Task\Row;

class DailyStats implements TaskInterface {

    function name() {
        return 'Stats Daily Archiving';
    }

    function run( Row $task, AppInterface $app ) {
        $time_now = time();

        //-----------------------------------------
        // Получаем список пользователей, посетивших
        // сайт после завершения, но до выполнения
        // этой задачи.
        //-----------------------------------------

        $q = $app->db()->select()
            ->from('daily_users')
            ->where('first_visit > "'.$task['task_next_run'].'"')
            ->query()
        ;

        //-----------------------------------------
        // Параметры которые надо перенести в
        // новую статистику
        //-----------------------------------------

        $_hits 		= 0;
        $_hosts		= 0;
        $_users 	= 0;
        $_guests 	= 0;
        $_bots 		= 0;

        while( $r = $q->fetch() ) {
            $_hosts++;
            $_hits += $r['hits'];

            if( $r['is_bot'] ) {
                $_bots++;
            }
            elseif( $r['mgroup'] == $app->getVar('guest_group') ) {
                $_guests++;
            }
            else {
                $_users++;
            }
        }

        //-----------------------------------------
        // Что исключить из сохраняемой статистики.
        // # -1 хит этой задачи
        //-----------------------------------------

        $_set_unsave = [
            "hits"   => new \Zend_Db_Expr("hits-{$_hits}-1"),
            "hosts"  => new \Zend_Db_Expr("hosts-{$_hosts}"),
            "guests" => new \Zend_Db_Expr("guests-{$_guests}"),
            "users"  => new \Zend_Db_Expr("users-{$_users}"),
            "bots"   => new \Zend_Db_Expr("bots-{$_bots}"),
        ];

        //-----------------------------------------
        // Что включить в новую статистику
        //-----------------------------------------

        $_insert_save = array(
            'start_time'	=> $time_now,
            'active'		=> 1,
            'hits'			=> $_hits,
            'hosts'			=> $_hosts,
            'guests'		=> $_guests,
            'users'			=> $_users,
            'bots'			=> $_bots,
        );

        //-----------------------------------------
        // Получаем список остальных пользователей,
        // который и надо сохранить.
        //-----------------------------------------

        $q = $app->db()->select()
            ->from('daily_users')
            ->where('first_visit <= "'.$task['task_next_run'].'"')
            ->query()
        ;

        $users = array();

        while( $r = $q->fetch() ) {
            $users[] = $r;
        }

        //-----------------------------------------
        // Сериализуем массив и записываем в БД
        //-----------------------------------------

        if( count( $users )) {
            $s = addslashes( serialize( $users ));

            $app->db()->update('daily_stats', array_merge( $_set_unsave, [
                'visitors'  => "'.$s.'",
                'end_time'  => "'.$time_now.'",
                'active'    => 0,
            ]),'active = 1' );
        }

        //-----------------------------------------
        // Открываем новую статистику, добавляем
        // сохраненные данные
        //-----------------------------------------

        $app->db()->insert( 'daily_stats', $_insert_save );

        //-----------------------------------------
        // Очищаем список посещений пользователей
        //-----------------------------------------

        $app->db()->delete('daily_users', 'first_visit <= "'.$task['task_next_run'].'"');
    }
}