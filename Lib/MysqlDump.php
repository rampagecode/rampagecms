<?php

namespace Lib;

use PDO;

/**
 * MySQL database dump.
 * based on:
 * @link       https://github.com/dg/MySQL-dump
 * @author     David Grudl (http://davidgrudl.com)
 * @copyright  Copyright (c) 2008 David Grudl
 * @license    New BSD License
 */
class MysqlDump {
    const NONE = 0;
    const DROP = 1;
    const CREATE = 2;
    const DATA = 4;
    const ALL = 15; // DROP | CREATE | DATA
    const MAX_SQL_SIZE = 1e6;

    /** @var array */
    public $tables = [
        '*' => self::ALL,
    ];

    /** @var \PDO */
    private $connection;

    /**
     * Connects to database.
     * @throws LibraryException
     */
    public function __construct( $connection, $charset = 'utf8' ) {
        $this->connection = $connection;

        if( $connection->connect_errno ) {
            throw new LibraryException( $connection->connect_error );
        }

        $this->tables['admin_sessions'] = self::DROP | self::CREATE;
        $this->tables['sessions'] = self::DROP | self::CREATE;
    }

    /**
     * Saves dump to the file.
     */
    public function save( $filename ) {
        $handle = fopen( $filename, 'wb' );

        if( ! $handle ) {
            throw new LibraryException("ERROR: Cannot write file '$filename'.");
        }

        $this->write( $handle );
    }


    /**
     * Writes dump to logical file.
     * @param resource
     * @throws LibraryException
     */
    public function write( $handle = null ) {
        if( $handle === null ) {
            $handle = fopen('php://output', 'wb');
        }
        elseif( ! is_resource( $handle ) || get_resource_type( $handle ) !== 'stream' ) {
            throw new LibraryException('Argument must be stream resource.');
        }

        $tables = $views = [];
        $res = $this->connection->query('SHOW FULL TABLES');

        while( $row = $res->fetch() ) {
            if( $row[1] === 'VIEW' ) {
                $views[] = $row[0];
            } else {
                $tables[] = $row[0];
            }
        }

        $tables = array_merge( $tables, $views ); // views must be last
        $this->connection->query('LOCK TABLES `' . implode('` READ, `', $tables) . '` READ');
        $this->connection->query('SELECT DATABASE()')->fetch();

        fwrite( $handle, "SET NAMES utf8;\n\n" );

        foreach( $tables as $table ) {
            $this->dumpTable( $handle, $table );
        }

        $this->connection->query('UNLOCK TABLES');
    }

    /**
     * Dumps table to logical file.
     * @param $handle
     * @param $table
     */
    public function dumpTable( $handle, $table ) {
        $mode = isset($this->tables[$table]) ? $this->tables[$table] : $this->tables['*'];

        if( $mode === self::NONE ) {
            return;
        }

        $delTable = $this->delimit( $table );
        $res = $this->connection->query("SHOW CREATE TABLE $delTable");
        $row = $res->fetch();

        fwrite( $handle, "# Dump of table {$table}\n# ------------------------------------------------------------\n\n" );

        $view = isset($row['Create View']);

        if( $mode & self::DROP ) {
            fwrite($handle, 'DROP ' . ($view ? 'VIEW' : 'TABLE') . " IF EXISTS $delTable;\n\n");
        }

        if( $mode & self::CREATE ) {
            fwrite($handle, $row[$view ? 'Create View' : 'Create Table'] . ";\n\n");
        }

        if( ! $view && ( $mode & self::DATA )) {
            $locked = false;
            $numeric = [];
            $res = $this->connection->query("SHOW COLUMNS FROM $delTable");
            $cols = [];

            while( $row = $res->fetch() ) {
                $col = $row['Field'];
                $cols[] = $this->delimit($col);
                $numeric[$col] = (bool) preg_match('#^[^(]*(BYTE|COUNTER|SERIAL|INT|LONG$|CURRENCY|REAL|MONEY|FLOAT|DOUBLE|DECIMAL|NUMERIC|NUMBER|TINYINT)#i', $row['Type']);
            }

            $cols = '(' . implode(', ', $cols) . ')';
            $size = 0;
            $useBufferedQuery = $this->connection->getAttribute( PDO::MYSQL_ATTR_USE_BUFFERED_QUERY );
            $this->connection->setAttribute(PDO::MYSQL_ATTR_USE_BUFFERED_QUERY, false );
            $res = $this->connection->query("SELECT * FROM $delTable", PDO::FETCH_ASSOC );
            $spaceBetweenCommas = ' ';
            $spaceAfterLastComma = strlen( $spaceBetweenCommas ) == 0 ? ' ' : '';

            while( $row = $res->fetch() ) {
                $s = '(';

                foreach( $row as $key => $value ) {
                    if( $value === null ) {
                        $s .= "NULL,{$spaceBetweenCommas}";
                    } elseif( $numeric[$key] ) {
                        $s .= $value . ",{$spaceBetweenCommas}";
                    } else {
                        $s .= $this->connection->quote( $value ) . ",{$spaceBetweenCommas}";
                    }
                }

                if( $size == 0 ) {
                    $s = "INSERT INTO $delTable $cols\nVALUES\n    $s{$spaceAfterLastComma}";
                } else {
                    $s = "\n,   $s{$spaceAfterLastComma}";
                }

                $len = strlen($s) - 1;
                $s[$len - 1] = ')';

                if( ! $locked ) {
                    fwrite($handle, 'LOCK TABLES ' . $delTable . " WRITE;\n");
                    fwrite($handle, '/*!40000 ALTER ' . ($view ? 'VIEW' : 'TABLE') . ' ' . $delTable . " DISABLE KEYS */;\n\n");
                    $locked = true;
                }

                fwrite($handle, $s, $len);
                $size += $len;

                if( $size > self::MAX_SQL_SIZE ) {
                    fwrite($handle, "\n;\n");
                    $size = 0;
                }
            }

            $this->connection->setAttribute(PDO::MYSQL_ATTR_USE_BUFFERED_QUERY, $useBufferedQuery );

            if( $size ) {
                fwrite($handle, "\n;\n");
            }

            if( $locked ) {
                fwrite($handle, "\n");
                fwrite($handle, '/*!40000 ALTER ' . ($view ? 'VIEW' : 'TABLE') . ' ' . $delTable . " ENABLE KEYS */;\n");
                fwrite($handle, "UNLOCK TABLES;\n");
                fwrite($handle, "\n");
            }
        }

        fwrite($handle, "\n");
    }

    private function delimit( $s ) {
        return '`' . str_replace('`', '``', $s) . '`';
    }
}