<?php

namespace App\Download;

use App;
use App\AppException;
use App\User\User;
use Data\Document\Row;
use Data\Document\Table;

class DownloadManager {
    private $docDir;

    public function __construct( $docDir ) {
        $this->docDir = $docDir;
    }

    /**
     * @param int $docId
     * @param User $user
     * @return Row
     * @throws AppException
     */
	function findDocument( $docId, User $user ) {
		$docTable = new Table();

        try {
            $docRow = $docTable->findById( $docId );
        } catch( \Zend_Db_Table_Exception $e ) {
            throw new AppException("Произошла ошибка при попытке доступа к файлу");
        }

        if( empty( $docRow )) {
            throw new AppException("Запрашиваемый вами файл не найден");
        }

        $hasAccess = $docRow->checkGroupAccess( $user->getGroup() );

        if( ! $hasAccess ) {
            throw new AppException("У вас нет доступа для скачивания этого файла");
        }

        if( ! file_exists( $this->docDir . $docRow->getSystemName() )) {
            throw new AppException('Запрашиваемый вами файл отсутствует');
        }

        return $docRow;
	}

    function sendDocument( Row $doc ) {
        $fileName = $this->docDir . $doc->getSystemName();
        $fileSize = filesize ( $fileName );
        $fileTime = filemtime( $fileName );

        if( $fd = fopen( $fileName, 'rb' )) {
            if( $_SERVER['HTTP_RANGE'] ) {
                // Докачка файла
                preg_match( "/bytes=(\d+)-/", $_SERVER['HTTP_RANGE'], $matches );
                $offset = intval( $matches[1] );
                fseek( $fd, $offset );

                $this->http206( $doc->getPublicNameWithExtension(), $fileTime, $offset, $fileSize );
            } else {
                // Новое скачивание
                $this->http200( $doc->getPublicNameWithExtension(), $fileTime, $fileSize );
            }

            // Отсылаем файл кусками 8192
            while( ! feof( $fd )) {
                echo fread( $fd, 8192 );
            }

            fclose( $fd );
        }
    }

    /**
     * Составляем HTTP-заголовок для скачивания файла
     * @param $fileName
     * @param $fileTime
     * @param $contentSize
     * @return void
     */
    function http200( $fileName, $fileTime, $contentSize ) {
        header( 'HTTP/1.1 200 OK' );
        header( 'Date: ' . $this->getGMTDateTime() );
        header( 'Expires: 0' );
        header( 'Last-Modified: ' . $this->getGMTDateTime( $fileTime ) );
        header( 'Cache-Control: None' );
        header( 'Pragma: no-cache' );
        header( 'Accept-Ranges: bytes' );
        header( 'Content-Disposition: attachment; filename="' . $fileName . '"' );
        header( 'Content-Length: ' . $contentSize );
        header( 'Proxy-Connection: close' );

        if( strpos( strtolower( $fileName ),".xls" ) !== false ) {
            header('Content-Type: application/vnd.ms-excel;'); // This should work for IE & Opera
            header("Content-type: application/x-msexcel");     // This should work for the rest
        } else {
            header( 'Content-Type: application/octet-stream' );
        }
    }

    /**
     * HTTP-заголовок для докачки файла
     * @param $fileName
     * @param $fileTime
     * @param $fileSize
     * @param $offset
     * @return void
     */
    function http206( $fileName, $fileTime, $fileSize, $offset ) {
        $p1 = $offset;
        $p2 = $fileSize - 1;
        $p3 = $fileSize;
        $cs = $fileSize - $offset;

        header( 'HTTP/1.1 206 Partial Content' );
        header( 'Date: ' . $this->getGMTDateTime() );
        header( 'Expires: 0');
        header( 'Last-Modified: ' . $this->getGMTDateTime( $fileTime ));
        header( 'Cache-Control: None' );
        header( 'Pragma: no-cache' );
        header( 'Accept-Ranges: bytes' );
        header( 'Content-Disposition: attachment; filename="' . $fileName . '"' );
        header( 'Content-Type: application/octet-stream' );
        header( 'Content-Range: bytes ' . $p1 . '-' . $p2 . '/' . $p3 );
        header( 'Content-Length: ' . $cs );
        header( 'Proxy-Connection: close' );
    }

    /**
     * Форматируем дату для HTTP-заголовка
     * @param $time
     * @return string
     */
    function getGMTDateTime( $time = NULL ) {
        $gmtDate = date( 'O' );
        $offset = '';

        if( $gmtDate[0] == '+' ) {
            $offset = '-';
        } else {
            $offset = '+';
        }

        $offset .= $gmtDate[1] . $gmtDate[2];

        if( !$time ) {
            $time = Time();
        }

        return ( date( 'D, d M Y H:i:s', $time + $offset * 3600 ) . ' GMT' );
    }
}