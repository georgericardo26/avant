<?php
include_once ('Connection.php');
/*
 * Classe para gerenciamento de transações
 */
class Transaction {
    //Conexão ativa
    private static $conn;

    /*
     *Abre uma transação e uma conexão com o banco de dados
     * Abre uma conexão e armazena na propriedade estatica $conn
     */
    public static function open() {
        //verifica se não já está preenchida
        if (empty(self::$conn)) {
            self::$conn = Connection::connect();
            //inicia a transação
            self::$conn -> beginTransaction();
        }

    }

    /*
     *Retorna a conexão ativa da transação
     */
    public static function get() {
        return self::$conn;
    }

    /*
     *Desfaz todas operações realizadas na transação
     */
    public static function rollback() {
        if (self::$conn) {
            self::$conn -> rollback();
            self::$conn = NULL;
        }
    }

    /*
     *Aplica todas as operações necessarias e retorna o resultado
     */
    public static function commit() {
        if (self::$conn) {
            return self::$conn -> commit();
        }
    }

    /*
     *Fecha transação
     */
    public static function close() {
        if (self::$conn) {
            self::$conn = NULL;
        }
    }
    
    /*
     * Retorna o ultimo id inserido
     */
    public static function lastInsertId(){
        if (self::$conn) {
            return self::$conn -> lastInsertId();
        }
    }
}
