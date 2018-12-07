<?php
/*
 * Classe para manipulação de uma instrução de UPDATE no banco de dados
 */
class SQLUpdate extends SQLInstruction {
    /*
     *Atribui valores as colunas que serão modificadas no banco de dados
     * monta um array indexado pelo nome da coluna
     * @param $column
     * @param $value
     */
    public function setRowData($column, $value) {
        $this -> values[$column] = $value;
    }

    /*
     *Retorna a instrução de UPDATE em forma de string
     */
    public function getInstruction() {
        $this -> sql = "UPDATE {$this->getEntity()} SET ";
        //monta os pares
        if ($this -> values) {
            foreach ($this->values as $column => $value) {
                //monta uma string com os novos valores das colunas sendo os nomes das colunas com dois pontos na frente
                //esses nomes servirão como identificadores
                //depois serão substituidos pelos respectivos valores
                $set[] = "{$column} = :{$column}";
            }
        }
        $this -> sql .= implode(', ', $set);

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

        //Vincula um valor correspondente a um nome que foi reservado na instrução SQL que foi usado para preparar a declaração
        foreach ($this -> values as $key => $value) {
            $pstm -> bindValue(":{$key}", $value);
        }

        //Chamando o metodo que passara os valores para o $pstm
        $this -> whereBindValue($pstm);

        //Executa a instrução que foi preparada
        return $pstm -> execute();
    }
}