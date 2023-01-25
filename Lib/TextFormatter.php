<?php

namespace Lib;

use Sys\Config\ConfigManager;
use Sys\Language\LanguageManager;

class TextFormatter {
    /**
     * Convert bytes into kb, mb, etc
     * @param	integer	size in bytes
     * @return	string	Human size
     */
    function formatSize( $bytes ) {
        if( $bytes >= 1048576 ) {
            return round( $bytes / 1048576 * 100 ) / 100 .'&nbsp;'. $this->lang['sf_mb'];
        }
        else if( $bytes  >= 1024 ) {
            return round( $bytes / 1024 * 100 ) / 100 .'&nbsp;'. $this->lang['sf_k'];
        }
        else {
            return $bytes .'&nbsp;'. $this->lang['sf_bytes'];
        }
    }

    /**
     * @param int $date Unix timestamp
     * @param ConfigManager $conf
     * @param LanguageManager $lang
     * @param int $offset
     * @param string $method LONG, SHORT, JOINED, TINY
     * @param int $not_relative
     * @param int $full_relative
     * @return string formatter time string
     */
    function formatDate(
        $date,
        ConfigManager $conf,
        LanguageManager $lang,
        $offset,
        $method = '',
        $not_relative = 0,
        $full_relative = 0
    ) {
        if( ! $date ) {
            return '--';
        }

        if( empty( $method )) {
            $method = 'LONG';
        }

        $today_time     = null;
        $yesterday_time = null;

        if( $conf->getVar('time_use_relative' )) {
            $today_time     = gmdate('d,m,Y', ( time() + $offset ));
            $yesterday_time = gmdate('d,m,Y', (( time() - 86400 ) + $offset ));
        }

        $time_options = array(
            'JOINED' => $conf->getVar('clock_joined'),
            'SHORT'  => $conf->getVar('clock_short'),
            'LONG'   => $conf->getVar('clock_long'),
            'TINY'   => $conf->getVar('clock_tiny'),
            'DATE'   => $conf->getVar('clock_date'),
        );

        //-----------------------------------------
        // Full relative?
        //-----------------------------------------

        if( $conf->getVar('time_use_relative') == 3 ) {
            $full_relative = 1;
        }

        //-----------------------------------------
        // FULL Relative
        //-----------------------------------------

        if( $full_relative and ( $not_relative != 1 )) {
            $diff = time() - $date;

            if( $diff < 3600 ) {
                if( $diff < 60 ) {
                    if( $diff <= 1 ) {
                        $s = 'секунду назад';
                    }
                    elseif( $diff < 5 ) {
                        $s = '%s секунды назад';
                    } else {
                        $s = '%s секунд назад';
                    }

                    return sprintf( $s, $diff );
                }
                elseif( $diff < 60 * 2 ) {
                    return 'минуту назад';
                }
                elseif( $diff < 60 * 5 ) {
                    return sprintf( '%s минуты назад', intval( $diff / 60 ));
                }
                else {
                    return sprintf( '%s минут назад', intval( $diff / 60 ));
                }
            }
            else if( $diff < 7200 ) {
                return $lang['time_less_hour'];
            }
            else if( $diff < 86400 ) {
                return sprintf( $lang['time_hours_ago'], intval($diff / 3600 ));
            }
            else if( $diff < 172800 ) {
                return $lang['time_less_day'];
            }
            else if( $diff < 604800 ) {
                return sprintf( $lang['time_days_ago'], intval($diff / 86400 ));
            }
            else if ( $diff < 1209600 ) {
                return $lang['time_less_week'];
            }
            else if ( $diff < 3024000 ) {
                return sprintf( $lang['time_weeks_ago'], intval($diff / 604900 ));
            }
            else {
                return gmdate( $time_options[ $method ], ( $date + $offset ));
            }
        }
        else if ( $conf->getVar('time_use_relative') and ( $not_relative != 1 )) {
            //-----------------------------------------
            // Yesterday / Today
            //-----------------------------------------

            $this_time = gmdate('d,m,Y', ( $date + $offset ));

            //-----------------------------------------
            // Use level 2 relative?
            //-----------------------------------------

            if( $conf->getVar('time_use_relative' ) == 2 ) {
                $diff = time() - $date;

                if( $diff < 3600 ) {
                    if( $diff < 120 ) {
                        return $lang['time_less_minute'];
                    } else {
                        return sprintf( $lang['time_minutes_ago'], intval($diff / 60) );
                    }
                }
            }

            if( $this_time == $today_time ) {
                return str_replace(
                    '{--}',
                    $lang['time_today'],
                    gmdate( $conf->getVar('time_use_relative_format' ), ( $date + $offset ))
                );
            }
            else if( $this_time == $yesterday_time ) {
                return str_replace(
                    '{--}',
                    $lang['time_yesterday'],
                    gmdate( $conf->getVar('time_use_relative_format' ), ( $date + $offset ))
                );
            } else {
                return gmdate( $time_options[ $method ], ( $date + $offset ));
            }
        } else {
            return gmdate( $time_options[ $method ], ( $date + $offset ));
        }
    }
}