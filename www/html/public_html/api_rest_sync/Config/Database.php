<?php

namespace Config;
use \PDO;

class Database
{
    // Database credentials now loaded from .env.development file
    private static $dbName;
    private static $dbHost;
    private static $dbPort;
    private static $dbUsername;
    private static $dbUserPassword;

    public $conn;
    private static $cont = null;

    /**
     * Initialize database configuration from environment variables
     */
    private static function initConfig()
    {
        if (self::$dbName === null) {
            self::$dbName = $_ENV['DB_NAME'] ?? 'cursefqz_pc_byte';
            self::$dbHost = $_ENV['DB_HOST'] ?? 'localhost';
            self::$dbPort = $_ENV['DB_PORT'] ?? 3306;
            self::$dbUsername = $_ENV['DB_USERNAME'] ?? 'root';
            self::$dbUserPassword = $_ENV['DB_PASSWORD'] ?? '';
        }
    }

    // get the database connection
//    protected  function getConnection(){
//
//        $this->conn = null;
//
//        try{
//            $this->conn = new PDO("mysql:host=" . $this->host . ";dbname=" . $this->db_name, $this->username, $this->password);
//            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
//            $this->conn->exec("set names utf8");
//        }catch(PDOException $exception){
//            exit("Connection error: " . $exception->getMessage());
//        }
//
//        return $this->conn;
//    }

    public static function connect()
    {
        // Initialize configuration from .env.development
        self::initConfig();

        if ( null == self::$cont )
        {
            try
            {
                self::$cont = new PDO(
                    "mysql:host=".self::$dbHost.";port=".self::$dbPort.";dbname=".self::$dbName,
                    self::$dbUsername,
                    self::$dbUserPassword,
                    [PDO::MYSQL_ATTR_FOUND_ROWS => true]
                );
                self::$cont->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                self::$cont->exec("set names utf8");
            }
            catch(\PDOException $e)
            {
                die("Database connection failed: " . $e->getMessage());
            }
        }
        return self::$cont;
    }


}
