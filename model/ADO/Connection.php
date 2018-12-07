<?php
/*
 * Classe para gerenciamento de conexão com banco de dados
 */
final class Connection {


    static function connect()
    {

        // ler os parametros do arquivo
        $params = array();
     /*   $params['host'] = "localhost";
        $params['port'] = "5433";
        $params['dbname'] = "avant";
        $params['user'] = "postgres";
        $params['pass'] = "30032011";
     */

        $params['host'] = "avantdb.clnmev30d5j8.us-east-1.rds.amazonaws.com";
        $params['port'] = "5432";
        $params['dbname'] = "avant";
        $params['user'] = "root";
        $params['pass'] = "30032011";


        if ($params === false) {
            http_response_code(503);
            exit;
        }
        //conecta com o banco

        $conStr = sprintf("pgsql:host=%s;port=%d;dbname=%s;user=%s;password=%s",
            $params['host'],
            $params['port'],
            $params['dbname'],
            $params['user'],
            $params['pass']);

        $pdo = new \PDO($conStr);
        $pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);

        return $pdo;
    }
}

