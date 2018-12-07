<?php
/**
 * Created by PhpStorm.
 * User: georgericardo
 * Date: 02/12/2018
 * Time: 15:16
 */

namespace model;
/**
 * Represent the Connection
 */
class Connection {

    /**
     * Connection
     * @var type
     */
    private static $conn;

    /**
     * Connect to the database and return an instance of \PDO object
     * @return \PDO
     * @throws \Exception
     */
    public function connect() {

        //conecta com o banco
        $dbConfig = parse_ini_file( 'config/db.ini', true );

        if ($dbConfig  === false) {
             throw new \Exception("Erro ao ler o arquivo .ini");
        }

        // connect to the postgresql database
        $conStr = sprintf("pgsql:host=%s;port=%d;dbname=%s;user=%s;password=%s",
            $dbConfig['host'],
            $dbConfig['port'],
            $dbConfig['dbname'],
            $dbConfig['user'],
            $dbConfig['pass']);

        $pdo = new \PDO($conStr) or die('erro no banco de dados');
        $pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
        return $pdo;
    }

    /**
     * return an instance of the Connection object
     * @return type
     */
    public static function get() {
        if (null === static::$conn) {
            static::$conn = new static();
        }

        return static::$conn;
    }

    protected function __construct() {

    }

    private function __clone() {

    }

    private function __wakeup() {

    }

}