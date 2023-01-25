<?php

namespace Sys\Input;

class InputCleaner {
    /**
     * Применяется для базовой очистки переменных в глобальных массивах пришедших из вне.
     *
     * @param Array &$data Глобальный массив передается по ссылке, все изменения отзазятся на нем.
     * @param int $iteration Используется для внутреннего подсчета количества итераций.
     */
    public static function cleanGlobals( &$data, $iteration = 0 ) {
        // Если отправить очень многомерный массив, типа &foo[][][][][][].... то
        // это может положить сервер, поэтому не более 10 итераций.

        if( $iteration >= 10 ) {
            return $data;
        }

        if( count( $data )) {
            foreach( $data as $k => $v ) {
                if ( is_array( $v )) {
                    self::cleanGlobals( $data[ $k ], $iteration+1 );
                } else {
                    $v = preg_replace( '/\\\0/' , '&#92;&#48;'		, $v );
                    $v = preg_replace( '/\\x00/', '&#92;x&#48;&#48;', $v );
                    $v = str_replace( '%00'     , '%&#48;&#48;'		, $v );
                    $v = str_replace( '../'     , '&#46;&#46;/'		, $v );
                    $v = self::txtStripSlashes( $v );

                    $data[ $k ] = $v;
                }
            }
        }
    }

    /**
     * Чистит ключи и значения всего глобального массива, а результат записывает
     * в другой массив.
     *
     * @param array $data Глобальный массив подлежащий чистке
     * @param array $input Массив в который будут помещаться очищенные элементы
     * @param int $iteration Используется для внутреннего подсчета количества итераций.
     *
     * @return array Массив переданный в $input плюс содержащий все элементы из массива $data
     */
    public static function parseGlobals( &$data, $input=array(), $iteration = 0 ) {
        if( $iteration >= 10 ) {
            return $input;
        }

        if( count( $data )) {
            foreach( $data as $k => $v ) {
                if ( is_array( $v )) {
                    $input[ $k ] = self::parseGlobals( $data[ $k ], array(), $iteration+1 );
                } else {
                    $k = self::parseCleanKey( $k );
                    $v = self::parseCleanValue( $v );

                    $input[ $k ] = $v;
                }
            }
        }

        return $input;
    }

    /**
     * "Очищает" ключ входящей переменой от того чего там быть не должно
     * @param string $key Ключ
     * @return string Очищенный ключ
     */
    public static function parseCleanKey( $key ) {
        if( $key == "" ) {
            return "";
        }

        $key = htmlspecialchars( urldecode( $key ));
        $key = str_replace( ".."           , ""  , $key );
        $key = preg_replace( "/\_\_(.+?)\_\_/"  , ""  , $key );
        $key = preg_replace( "/^([\w\.\-\_]+)$/", "$1", $key );

        return $key;
    }

    /**
     * "Очищает" значение входящей переменной от того чего там быть не должно
     * @param string $val Значение
     * @return string Очищенное значение
     */
    public static function parseCleanValue( $val ) {
        if( $val == "" ) {
            return "";
        }

        $val = str_replace( "&#032;"		, " "			  , $val );
        $val = str_replace( "&#8238;"		, ''			  , $val );
        $val = str_replace( "&"				, "&amp;"         , $val );
        $val = str_replace( "<!--"			, "&#60;&#33;--"  , $val );
        $val = str_replace( "-->"			, "--&#62;"       , $val );
        $val = preg_replace( "/<script/i"	, "&#60;script"   , $val );
        $val = str_replace( ">"				, "&gt;"          , $val );
        $val = str_replace( "<"				, "&lt;"          , $val );
        $val = str_replace( '"'				, "&quot;"        , $val );
        $val = str_replace( "\n"			, "<br />"        , $val );
        $val = str_replace( "$"				, "&#036;"        , $val );
        $val = str_replace( "\r"			, ""              , $val );
        $val = str_replace( "!"				, "&#33;"         , $val );
        $val = str_replace( "'"				, "&#39;"         , $val );
        $val = preg_replace("/&amp;#([0-9]+);/s", "&#\\1;", $val );
        $val = preg_replace( "/&#(\d+?)([^\d;])/i", "&#\\1;\\2", $val );

        return $val;
    }

    /**
     * Удаляет экранирующие слеши выставленные magic_quotes, если включена
     * @param	string	Строка со слешами
     * @return	string	Строка без лишних слешей
     */
    public static function txtStripSlashes( $t ) {
        if( @get_magic_quotes_gpc() ) {
            $t = stripslashes($t);
            $t = preg_replace( "/\\\(?!&amp;#|\?#)/", "&#092;", $t );
        }

        return $t;
    }

    /**
     * Чистит e-mail от некорректных символов и проверяет на правильность
     *
     * @param string	$email адрес e-mail
     * @return mixed E-mail адрес без некорректных символов или False если он не правильный
     */
    public static function cleanEmail( $email = '' ) {
        $email = trim( $email );
        $email = str_replace( " ", "", $email );

        if( substr_count( $email, '@' ) > 1 ) {
            return false;
        }

        $email = preg_replace( "#[\;\#\n\r\*\'\"<>&\%\!\(\)\{\}\[\]\?\\/\s]#", "", $email );

        if( preg_match( "/^.+\@(\[?)[a-zA-Z0-9\-\.]+\.([a-zA-Z]{2,4}|[0-9]{1,4})(\]?)$/", $email )) {
            return $email;
        } else {
            return false;
        }
    }

    /**
     * Очищает строку от символов недопустимых при поиске строки в базе данных
     *
     */
    public static function cleanSearchQuery( $search, $moreThenTree = false ) {
        $search = str_replace( "&amp;"         , "&", $search );
        $search = str_replace( "&#39;"         , "", $search );
        $search = str_replace( "&#33;"         , "", $search );
        $search = str_replace( "&#036;"        , "", $search );
        $search = str_replace( "<br />"        , "", $search );
        $search = str_replace( "&quot;"        , "", $search );
        $search = str_replace( "&lt;"          , "", $search );
        $search = str_replace( "&gt;"          , "", $search );
        $search = str_replace( "&#60;script"   , "", $search );
        $search = str_replace( "--&#62;"       , "", $search );
        $search = str_replace( "&#60;&#33;--"  , "", $search );
        $search = str_replace( "&#092;"        , "", $search );

        // Обрезаем строку до первых 64 символов
        $search = substr( $search, 0, 64 );

        // Удаляем ненормальные символы
        $search = preg_replace( "/[^\w\x7F-\xFF\s\%\&\+\-\*\/]/", "", $search );

        // Поиск по двум и более буквам
        $search = trim( preg_replace( "/\s(\S{1})\s/", " ", ereg_replace(" +", "  "," $search " )));

        // Экранируем служебные символы
        $search = str_replace( "%", '\%', $search );
        $search = str_replace( "_", '\_', $search );
        $search = str_replace( "&", "\&", $search );
        $search = str_replace( "+", "\+", $search );
        $search = str_replace( "-", "\-", $search );
        $search = str_replace( "*", "\*", $search );
        $search = str_replace( "/", "\/", $search );

        // Удаляем оставшиеся двойные пробелы
        $search = ereg_replace( " +", " ", $search );

        return $search;
    }
}