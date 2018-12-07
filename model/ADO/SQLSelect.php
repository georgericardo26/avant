<?php
/*
 * Classe para manipulação de uma instrução de SELECT no banco de dados
 */
include_once ('SQLInstruction.php');
class SQLSelect extends SQLInstruction {


    /*
     *Adiciona uma coluna a ser retornada pelo SELECT
     * @param $columns
     */
    public function addColumn($column) {
        //adciona a coluna ao array
        $this -> columns[] = $column;
    }

    /*
     *Retorna a instrução de SELECT em forma de string
     */
    public function getInstruction() {
        $this -> sql = "SELECT ";

        //monta a string com o nome das colunas
        $this -> sql .= implode(', ', $this -> columns);

        //adiciona a clausula FROM, o nome da tabela
        $this -> sql .= " FROM {$this->entity} ";

        //Retorna a clausula WHERE do objeto Criteria
        if ($this -> criteria) {
            $expression = $this -> criteria -> dump();
            if ($expression) {
                $this -> sql .= " WHERE " . $expression;
            }

            //obtem as propriedades do criteria
            $group = $this -> criteria -> getProperty('group');
            $order = $this -> criteria -> getProperty('order');
            $limit = $this -> criteria -> getProperty('limit');
            $offset = $this -> criteria -> getProperty('offset');
            
            //agrupar o resultado
            if($group){
                $this -> sql .= " GROUP BY {$group} ";
            }
            
            //obtem a ordenação do SELECT
            if ($order) {
                $this -> sql .= " ORDER BY {$order} ";
            }

            if ($limit) {
                //valor será substituido depois
                $this -> sql .= " LIMIT :limit ";
            }

            if ($offset) {
                //valor será substituido depois
                $this -> sql .= " OFFSET :offset ";
            }
        }

        return $this -> sql;
    }

    /*
     *Executa a instrução SQL
     *@param $conn
     */
    public function execute($conn, $extraBindValue = null) {
        //Prepara o banco de dados para receber a instrução sql e recebe o objeto PDOStatement
        $pstm = $conn -> prepare($this -> getInstruction());

        //Chamando o metodo que passara os valores para o $pstm
        $this -> whereBindValue($pstm);

        //obtem as propriedades do criteria
        if ($this -> criteria) {
            $limit = $this -> criteria -> getProperty('limit');
            $offset = $this -> criteria -> getProperty('offset');

            if ($limit) {
                //substitui o valor de acordo com o indice
                $pstm -> bindValue(":limit", intval($limit), PDO::PARAM_INT);
            }

            if ($offset) {
                //substitui o valor de acordo com o indice
                $pstm -> bindValue(":offset", intval($offset), PDO::PARAM_INT);
            }
        }
        
        //$extraBindValue, caso nao der para adicionar pelo criteria
        if(is_array($extraBindValue) && empty($extraBindValue) === false){
            foreach($extraBindValue as $key => $value){
                //substitui o valor de acordo com o indice
                $pstm -> bindValue("{$key}", $value);
            }
        }
        
        //Executa a instrução que foi preparada
        $pstm -> execute();

        //Definindo o modo padrão de busca, para retorna um array indexado pela posição
        //pelo nome da coluna
        return $pstm -> fetchAll(PDO::FETCH_ASSOC);
    }
}