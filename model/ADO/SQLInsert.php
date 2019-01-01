<?php
/*
 * Classe para manipulação de uma instrução de INSERT no banco de dados
 */
include_once('SQLInstruction.php');

class SQLInsert extends SQLInstruction
{
    /*
     *Atribui valores as colunas que serão inseridas no banco de dados
     * monta um array indexado pelo nome da coluna
     * @param $column
     * @param $value
     */
    public function setRowData($data)
    {
        //  $this -> columns[$column] = $value;
        $this->columns = ["nome", "cpf", "nascimento"];
        $this->values = $data;
    }

    /*
     *Não existe nessa classe, então lançara um erro
     */
    public function setCriteria($criteria)
    {
        //lança o erro
        throw new Exception("Não pode chamar setCriteira em " . __CLASS__);
    }

    /*
     *Retorna a instrução de INSERT em forma de string
     */
    public function getInstruction()
    {
        $this->sql = 'INSERT INTO ' . $this->getEntity();
        $this->sql .= '("nome", "cpf", "nascimento")';
        $this->sql .= 'VALUES ';
        foreach ($this->values as $key) {
            $this->sql .= "('{$key['nome']}', '{$key['cpf']}', '{$key['nascimento']}'),";
        }
        $countStr = strlen($this->sql);
        $this->sql = substr($this->sql, 0, $countStr - 1);
        /* $this -> sql = "INSERT INTO {$this->getEntity()} (";
         //monta uma string contendo os nomes das colunas
         $columns = implode(', ', array_keys($this -> columns));
         //monta uma string contendo os nomes das colunas com dois pontos na frente
         //esses nomes servirão como identificadores
         //depois serão substituidos pelos respectivos valores
         $values = implode(', :', array_keys($this -> columns));
         $this -> sql .= $columns . ') ';
         $this -> sql .= "VALUES (:{$values})";*/
        return $this->sql;
    }

    /*
     *Executa a instrução SQL
     *@param $conn
     */
    public function execute($conn)
    {
        //Prepara o banco de dados para receber a instrução sql e recebe o objeto PDOStatement
        $pstm = $conn->prepare($this->getInstruction());
        //Vincula um valor correspondente a um nome que foi reservado na instrução SQL que foi usado para preparar a declaração
        /* foreach ($this -> columns as $key => $value) {
             $pstm -> bindValue(":{$key}", $value);
         }*/
        //Executa a instrução que foi preparada
        return $pstm->execute();
    }
}