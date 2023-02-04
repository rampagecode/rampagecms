<?php

require_once '../App/User/Password.php';

class Installer {

    /**
     * @var string
     */
    private $host;

    /**
     * @var string
     */
    private $user;

    /**
     * @var string
     */
    private $password;

    /**
     * @var string
     */
    private $database;

    /**
     * @var string[]
     */
    public $messages = [];

    /**
     * @var string[]
     */
    public $errors = [];

    /**
     * @var string
     */
    private $confPath;

    /**
     * @var string
     */
    private $dumpPath;

    /**
     * @var mysqli
     */
    private $dbInstance;

    /**
     * @var string
     */
    private $userName;

    /**
     * @var string
     */
    private $userPass;

    /**
     * @var bool
     */
    private $dbExists = false;

    /**
     * @param string $host
     * @param string $user
     * @param string $password
     * @param string $database
     */
    public function __construct( $host, $user, $password, $database, $userName, $userPass ) {
        $this->user = $user;
        $this->host = $host;
        $this->password = $password;
        $this->database = $database;
        $this->userName = $userName;
        $this->userPass = $userPass;
    }

    /**
     * @return string
     */
    function confTemplate() { return <<<EOF
; RAMPAGECMS DB connection config
;       http://cms.therampage.org
; -------------------------------

hostname = $this->host
username = $this->user
password = $this->password
database = $this->database
EOF;
    }

    /**
     * @param string $confPath
     * @param string $dumpPath
     * @return bool
     */
    public function install( $confPath, $dumpPath ) {
        $this->confPath = $confPath;
        $this->dumpPath = $dumpPath;
        $this->dbInstance = @mysqli_connect( $this->host, $this->user, $this->password );

        if( ! $this->dbInstance ) {
            $this->errors[] = 'Connection failed';
            $this->errors[] = mysqli_connect_error();
            return false;
        }

        $this->messages[] = 'STEP 1: Connection succeed';

        if( $this->prepare() ) {
            $this->beginTransaction();

            try {
                $this->createConfig();
                $this->messages[] = 'STEP 2: Config file saved';

                $this->write();
                $this->messages[] = 'STEP 3: Database created';

                $this->createUser();
                $this->messages[] = 'STEP 4: User created';

                $this->commit();
                $this->messages[] = 'STEP 5: Changes saved';

                return true;
            } catch( Exception $e ) {
                $this->errors[] = $e->getMessage();
                $this->errors[] = 'Fatal Error: undo all changes';
                $this->rollback();
                return false;
            }
        } else {
            return false;
        }
    }

    /**
     * @return bool
     */
    private function prepare() {
        $this->dbExists = mysqli_select_db( $this->dbInstance, $this->database );

        if( ! file_exists( $this->dumpPath )) {
            $this->errors[] = 'DB dump file not found';
            return false;
        }

        if( empty( $this->userName )) {
            $this->errors[] = 'Administrator username can not be empty';
            return false;
        }

        if( empty( $this->userPass )) {
            $this->errors[] = 'Administrator password can not be empty';
            return false;
        }

        return true;
    }

    /**
     * @return void
     * @throws Exception
     */
    private function createConfig() {
        if( ! file_put_contents( $this->confPath, $this->confTemplate() )) {
            throw new Exception('Unable to save DB conf file');
        }
    }

    /**
     * @throws Exception
     */
    private function write() {
        $db = $this->dbInstance;

        mysqli_set_charset( $db, 'utf8mb4' );

        if( !$this->dbExists && !mysqli_query( $db, "CREATE DATABASE $this->database" )) {
            throw new Exception('Unable to create a database');
        }

        $this->dbExists = mysqli_select_db( $db, $this->database );

        if( !$this->dbExists ) {
            throw new Exception('Database was not created');
        }

        $file = fopen( $this->dumpPath, "r" );
        $query = "";

        while( ! feof( $file )) {
            $str = trim( fgets( $file ));

            if( ! empty( $str )) {
                $query .= $str;
            } else {
                if( ! empty( $query )) {
                    if( ! mysqli_query( $db, $query )) {
                        throw new Exception( 'DB error: '.mysqli_error( $db ));
                    }
                }

                $query = "";
            }
        }
    }

    /**
     * @return void
     * @throws Exception
     */
    private function createUser() {
        $pass_salt = App\User\Password::generatePasswordSalt();
        $pass_hash = App\User\Password::generateCompiledPasshash( $pass_salt, md5( $this->userPass ));
        $data = [
            //'id' => '',
            'login' => $this->userName,
            'dname' => $this->userName,
            'pass_hash' => $pass_hash,
            'pass_salt' => $pass_salt,
            'mgroup' => 4,
            //'email' => '',
            'reg_time' => time(),
            //'ip_address' => '',
            //'time_offset' => '',
            //'last_visit' => '',
            //'last_activity' => '',
            //'dst_in_use' => '',
            //'login_key' => '',
            //'login_key_expire' => '',
        ];

        $keys = join(', ', array_map( function( $value ) { return "`{$value}`"; }, array_keys( $data )));
        $values = join(', ', array_map( function( $value ) { return "'{$value}'"; }, array_values( $data )));
        $query = "INSERT INTO `members` ({$keys}) VALUES ({$values})";

        if( ! mysqli_query( $this->dbInstance, $query )) {
            $this->errors[] = 'Unable to create a user';
            throw new Exception( mysqli_error( $this->dbInstance ));
        }

        $query = "INSERT INTO `members_extra` (`member_id`) VALUES ('1')";

        if( ! mysqli_query( $this->dbInstance, $query )) {
            $this->errors[] = 'Unable to create a user extra';
            throw new Exception( mysqli_error( $this->dbInstance ));
        }
    }

    /**
     * @return void
     */
    private function beginTransaction() {
        if( function_exists('mysqli_begin_transaction' )) {
            mysqli_begin_transaction( $this->dbInstance, MYSQLI_TRANS_START_READ_WRITE );
        } else {
            mysqli_query( $this->dbInstance, "START TRANSACTION READ WRITE");
        }
    }

    /**
     * @return void
     */
    private function rollback() {
        if( function_exists('mysqli_rollback' )) {
            mysqli_rollback( $this->dbInstance );
        } else {
            mysqli_query( $this->dbInstance, "ROLLBACK");
        }

        if( ! mysqli_query( $this->dbInstance, "DROP DATABASE $this->database" )) {
            $this->errors[] = 'Unable to delete the database';
        }

        if( ! unlink( $this->confPath )) {
            $this->errors[] = 'Unable to delete config file at '.$this->confPath;
        }
    }

    /**
     * @return void
     */
    private function commit() {
        if( function_exists('mysqli_commit' )) {
            mysqli_commit( $this->dbInstance );
        } else {
            mysqli_query( $this->dbInstance, "ROLLBACK");
        }
    }
}