<?php

/*
 * Classe para prove todos os metodos em comum entre todas as instruções SQL(Insert, Update, Delete, Select)
 */

abstract class SQLInstruction
{
    //armazena a sql
    protected $sql;
    //armazena o objeto criteria
    protected $criteria;

    //Array de colunas a serem retornadas
    public $columns;
    public $values;

    /*
     *Defini a tabela que será utilizada na sql
     * @param $entity
     */
    final public function setEntity($entity)
    {
        $this->entity = $entity;
    }

    /*
     *Retorna a tabela que será utilizada na sql
     */
    final public function getEntity()
    {
        return $this->entity;
    }

    /*
     *Recebe um objeto criteria que tem todas informações necessaria para seleção de dados
     * @param $criteria
     */
    public function setCriteria($criteria)
    {
        $this->criteria = $criteria;

    }

    /*
     *Metodo que passara os valores para o objeto pstm de acordo com os indices
     * @param $pstm
     */
    final protected function whereBindValue($pstm)
    {
        if (!empty($this->criteria)) {
            $expressions = $this->criteria->getExpressions();
            //verifica se o $expressions é um array
            if (is_array($expressions)) {
                //percorre o $expressions
                foreach ($expressions as $expression) {
                    $expression->whereBindValue($pstm);
                }
            }
        }
    }

    /*
     *Metodo que devera existir nas suas classes filhas
     */
    abstract public function getInstruction();

    /*
     *Metodo que devera existir nas suas classes filhas
     */
    abstract public function execute($conn);
}