<?php
/*
 * Classe para manipulação de uma instrução de DELETE no banco de dados
 */
class SQLDelete extends SQLInstruction {
    /*
     *Retorna a instrução de DELETE em forma de string
     */
    public function getInstruction() {
        $this -> sql = "DELETE FROM {$this->getEntity()} ";

        //Retorna a clausula WHERE do objeto Criteria
        if ($this -> criteria) {
            $expression = $this -> criteria -> dump();
            if ($expression) {
                $this -> sql .= " WHERE " . $expression;
            }
        }

        return $this -> sql;
    }

    /*
     *Executa a instrução SQL
     *@param $conn
     */
    public function execute($conn) {
        //Prepara o banco de dados para receber a instrução sql e recebe o objeto PDOStatement
        $pstm = $conn -> prepare($this -> getInstruction());

        //Chamando o metodo que passara os valores para o $pstm
        $this -> whereBindValue($pstm);

        //Executa a instrução que foi preparada
        return $pstm -> execute();
    }
}