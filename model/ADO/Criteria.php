<?php
/*
 * Classe para definição de criterios
 */
include_once('Expression.php');

class Criteria extends Expression
{
    //Armazena lista de expressões
    private $expressions;
    //Armazena lista de operadores
    private $operators;
    //Propriedades do criterio
    private $properties;

    /*
     *Adiciona uma expresão ao criterio
     * @param $expression
     * @param $operator
     */
    public function add(Expression $expression, $operator = self::AND_OPERATOR)
    {
        //primeira vez não precisa de operador logico
        if (empty($this->expressions)) {
            $operator = "";
        }
        //agrega os valores as suas listas
        $this->expressions[] = $expression;
        $this->operators[] = $operator;
    }

    /*
     *Retorna a expressão final
     */
    public function dump()
    {
        //concatena a lista de expressões
        if (is_array($this->expressions)) {
            $result = "";
            foreach ($this->expressions as $i => $expression) {
                $operator = $this->operators[$i];
                //concatena o operador com a expressão
                $result .= $operator . ' ' . $expression->dump() . ' ';
            }
            $result = trim($result);
            return "({$result})";
        }
    }

    /*
     *Armazena o valor de uma propriedade
     * @param $property
     * @param $value
     */
    public function setProperty($property, $value)
    {
        $this->properties[$property] = $value;
    }

    /*
     * Retorna o valor de uma propriedade
     * @param $property
     */
    public function getProperty($property)
    {
        //verifica se é array
        //verifica se a propriedade existe no array
        if (is_array($this->properties) && array_key_exists($property, $this->properties)) {
            return $this->properties[$property];
        } else {
            return null;
        }
    }

    /*
     *Retorna todas as expressões do objeto
     */
    public function getExpressions()
    {
        return $this->expressions;
    }

    /*
     *Retorna a expressão do objeto atual
     */
    public function getExpression()
    {
        if (is_array($this->expressions)) {
            foreach ($this->expressions as $expression) {
                return $expression->getExpression();
            }
        }
    }

    /*
     *Retorna a variavel do objeto atual
     */
    public function getVariavel()
    {
        if (is_array($this->expressions)) {
            foreach ($this->expressions as $expression) {
                return $expression->getVariavel();
            }
        }
    }

    /*
     *Retorna o operador do objeto atual
     */
    public function getOperator()
    {
        if (is_array($this->expressions)) {
            foreach ($this->expressions as $expression) {
                return $expression->getOperator();
            }
        }
    }

    /*
     *Retorna o valor do objeto atual
     */
    public function getValue()
    {
        if (is_array($this->expressions)) {
            foreach ($this->expressions as $expression) {
                return $expression->getValue();
            }
        }
    }

    /*
     *Metodo que passara os valores para o objeto pstm de acordo com os indices
     * @param $pstm
     */
    public function whereBindValue($pstm)
    {
        if (is_array($this->expressions)) {
            foreach ($this->expressions as $expression) {
                $expression->whereBindValue($pstm);
            }
        }
    }
}