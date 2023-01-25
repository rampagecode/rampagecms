<?php

namespace App\Task;

use App\AppInterface;

class TaskManager {
    /**
     * @var AppInterface
     */
    var $app;
    var $type      = 'internal'; // Внутренний запуск - по запросу в ручную из скрипта
    var $time_now  = 0;
    var $date_now  = [];
    var $cron_key  = '';
    var $task_dir  = '';

    private $run_day;
    private $run_minute;
    private $run_hour;
    private $run_month;
    private $run_year;

    public function __construct( AppInterface $app ) {
        $this->app = $app;

        if( $_REQUEST['i'] == 'cron' && $_REQUEST['x'] ) {
            $this->type     = 'cron';
            $this->cron_key = substr( trim( stripslashes( $_REQUEST['x'] )), 0, 32 );
        }

        $this->time_now = time();
        $this->task_dir = dirname(__FILE__);

        $this->date_now['minute']      = intval( gmdate( 'i', $this->time_now ) );
        $this->date_now['hour']        = intval( gmdate( 'H', $this->time_now ) );
        $this->date_now['wday']        = intval( gmdate( 'w', $this->time_now ) );
        $this->date_now['mday']        = intval( gmdate( 'd', $this->time_now ) );
        $this->date_now['month']       = intval( gmdate( 'm', $this->time_now ) );
        $this->date_now['year']        = intval( gmdate( 'Y', $this->time_now ) );
    }

    function runTask() {
        if( $this->type == 'internal' ) {
            // Получаем задачу стоящую первой в очереди на
            // выполнение, т.е. время которой уже пришло.

            $task = $this->app->db()->select()
                ->from('task_manager')
                ->where( 'task_enabled = 1 AND task_next_run <= '.$this->time_now )
                ->order('task_next_run ASC')
                ->limit(1, 0)
                ->query()
                ->fetch()
            ;

            $this->app->log( 'Внутренний вызов задачи - через картинку' );
        } else {
            // Крон - запуск конкретной задачи по переданному ключу

            $task = $this->app->db()->select()
                ->from('task_manager')
                ->where( "task_cronkey = '".$this->cron_key."'" )
                ->query()
                ->fetch()
            ;

            $this->app->log( 'Вызов задачи Кроном' );
        }

        if( $task['task_id'] ) {
            $this->app->log( "Задача найдена - '{$task['task_title']}' " );

            if( $task['task_locked'] > 0 ) {
                $this->app->log( 'Эта задача заблокирована' );

                // Задачи блокируются на время выполнения.
                // Если блокировка стоит уже более 30 минут,
                // значит что-то не так - разблокируем.

                if( $task['task_locked'] < $this->time_now - 1800 ) {
                    $this->app->log( 'ВНИМАНИЕ! Задача была заблокирована более чем 30 минут.' );
                    $this->app->log( 'Задача разблокирована. Выполнение завершено.' );

                    $new_date = $this->calcNextRunTime( $task );

                    $this->app->db()->update( 'task_manager', array(
                        'task_next_run' => $new_date,
                        'task_locked' => 0
                    ), 'task_id=' . $task['task_id'] );

                    $this->saveNextRunTime();
                }

                return;
            }

            // Устанавливаем время следующего запуска,
            // Блокируем на время выполнения,
            // Подключаем модуль текущей задачи
            // и выполняем его.

            $new_date = $this->calcNextRunTime( $task );

            $this->app->db()->update( 'task_manager', array(
                'task_next_run' => $new_date,
                'task_locked' => $this->time_now
            ), 'task_id=' . $task['task_id'] );

            $this->saveNextRunTime();
            $filepath = $this->task_dir . $task['task_file'] . '.php';

            if( file_exists( $filepath )) {
                require_once $filepath;

                if( class_exists( $task['task_file'] )) {
                    $taskJob = new $task['task_file']();

                    if ($taskJob instanceof TaskInterface) {
                        $taskJob->run( $task, $this->app );

                        $this->logTaskJob( $task, $taskJob->name() );
                        $this->unlockTask( $task );
                    } else {
                        $this->app->log( 'Класс задачи не соответствует интерфейсу: ' . $task['task_file'] );
                    }
                } else {
                    $this->app->log( 'Класс задачи не найден: ' . $task['task_file'] );
                }

                $this->app->log( 'Управление было передано модулю задачи.' );
            } else {
                $this->app->log( 'ВНИМАНИЕ! Модуль задачи не найден по адресу: ' . $this->task_dir . $task['task_file'] );
            }
        } else {
            $this->app->log( 'Задача не найдена' );
        }
    }

    /**
     * Разблокировка задачи
     * @param $task
     * @return void
     */
    private function unlockTask($task = array() ) {
        if( ! $task['task_id'] ) {
            return;
        }

        $this->app->db()->update(
            'task_manager',
            [ 'task_locked' => 0 ],
            'task_id=' . $task['task_id']
        );
    }

    /**
     * Кэшируем новое время выполнения следующей задачи
     * @return void
     */
    private function saveNextRunTime() {
        $cache = $this->app->config()->getCache();
        $cache->cacheTasks();
        $cache->save();
    }

    /**
     * Generate next_run unix timestamp
     * @param array $task
     * @return false|int
     */
    private function calcNextRunTime( $task ) {
        $day_set       = 1;
        $min_set       = 1;
        $day_increment = 0;

        $this->run_day    = $this->date_now['wday'];
        $this->run_minute = $this->date_now['minute'];
        $this->run_hour   = $this->date_now['hour'];
        $this->run_month  = $this->date_now['month'];
        $this->run_year   = $this->date_now['year'];

        if( $task['task_week_day'] == -1 AND $task['task_month_day'] == -1 ) {
            $day_set = 0;
        }

        if( $task['task_minute'] == -1 ) {
            $min_set = 0;
        }

        if( $task['task_week_day'] == -1 ) {
            if( $task['task_month_day'] != -1 ) {
                $this->run_day = $task['task_month_day'];
                $day_increment = 'month';
            } else {
                $this->run_day = $this->date_now['mday'];
                $day_increment = 'anyday';
            }
        } else {
            $this->run_day = $this->date_now['mday'] + ( $task['task_week_day'] - $this->date_now['wday'] );
            $day_increment = 'week';
        }

        if( $this->run_day < $this->date_now['mday'] ) {
            switch( $day_increment ) {
                case 'month':
                    $this->_add_month();
                    break;

                case 'week':
                    $this->_add_day(7);
                    break;

                default:
                    $this->_add_day();
                    break;
            }
        }

        if( $task['task_hour'] == -1 ) {
            $this->run_hour = $this->date_now['hour'];
        } else {
            if( ! $day_set AND ! $min_set ) {
                $this->_add_hour( $task['task_hour'] );
            } else {
                $this->run_hour = $task['task_hour'];
            }
        }

        if( $task['task_minute'] == -1 ) {
            $this->_add_minute();
        } else {
            if( $task['task_hour'] == -1 AND ! $day_set ) {
                $this->_add_minute( $task['task_minute'] );
            } else {
                $this->run_minute = $task['task_minute'];
            }
        }

        if( $this->run_hour <= $this->date_now['hour'] AND $this->run_day == $this->date_now['mday'] ) {
            if( $task['task_hour'] == -1 ) { // Every hour...
                if( $this->run_hour == $this->date_now['hour'] AND $this->run_minute <= $this->date_now['min'] ) {
                    $this->_add_hour();
                }
            } else {
                if( ! $day_set AND ! $min_set ) {
                    $this->_add_hour( $task['task_hour'] );
                }
                elseif( ! $day_set ) {
                    $this->_add_day();
                } else {
                    switch( $day_increment ) {
                        case 'month':
                            $this->_add_month();
                            break;

                        case 'week':
                            $this->_add_day(7);
                            break;

                        default:
                            $this->_add_day();
                            break;
                    }
                }
            }
        }

        return gmmktime(
            $this->run_hour,
            $this->run_minute,
            0,
            $this->run_month,
            $this->run_day,
            $this->run_year
        );
    }

    /**
     * Add to the log file
     * @param array $task
     * @param string $desc
     * @return void
     */
    private function logTaskJob( $task, $desc ) {
        if( ! $task['task_log'] ) {
            return;
        }

        $this->app->db()->insert('task_logs', [
            'log_title' => $task['task_title'],
            'log_date'  => $this->time_now,
            'log_ip'    => $this->app->getIpAddr(),
            'log_desc'  => $desc
        ]);
    }

    /**
     * Add on a month for the next run time..
     * @return void
     */
    private function _add_month() {
        if( $this->date_now['month'] == 12 ) {
            $this->run_month = 1;
            $this->run_year++;
        } else {
            $this->run_month++;
        }
    }

    /**
     * Add on a day for the next run time
     * @param int $days
     * @return void
     */
    private function _add_day( $days = 1 ) {
        if( $this->date_now['mday'] >= ( gmdate( 't', $this->time_now ) - $days )) {
            $this->run_day = ( $this->date_now['mday'] + $days ) - date( 't', $this->time_now );
            $this->_add_month();
        } else {
            $this->run_day += $days;
        }
    }

    /**
     * Add on an hour for the next run time
     * @param int $hour
     * @return void
     */
    private function _add_hour( $hour = 1 ){
        if( $this->date_now['hour'] >= ( 24 - $hour )) {
            $this->run_hour = ( $this->date_now['hour'] + $hour ) - 24;
            $this->_add_day();
        } else {
            $this->run_hour += $hour;
        }
    }

    /**
     * Add on a minute
     * @param int $mins
     * @return void
     */
    private function _add_minute( $mins = 1 ){
        if( $this->date_now['minute'] >= ( 60 - $mins )) {
            $this->run_minute = ( $this->date_now['minute'] + $mins ) - 60;
            $this->_add_hour();
        } else {
            $this->run_minute += $mins;
        }
    }
}