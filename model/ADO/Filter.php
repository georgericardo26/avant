<?php
/*
 * Classe para definição de filtros de seleção
 */

class Filter extends Expression
{
    //Variavel
    private $variavel;
    //Operador
    private $operator;
    //Valor
    private $value;

    /*
     *Instancia novo filtro
     * @param $variavel
     * @param $operator
     * @param $value
     */
    public function __construct($variavel, $operator, $value)
    {
        //armazena os valores
        $this->variavel = $variavel;
        $this->operator = $operator;
        $this->value = $value;
    }

    /*
     *Retorna o filtro em forma de expressão
     */
    public function dump()
    {
        //Pega o identificador do objeto
        $id = spl_object_hash($this);
        // verifica se é array, que sera no caso de IN ou BETWEEN no operador
        if (is_array($this->value)) {
            //verifica se o operador é IN
            if ($this->operator == "IN") {
                //cria o array
                $arr = array();
                //percorre o $this->value, forma a expressão e armazena no array
                foreach ($this->value as $i => $v) {
                    $arr[] = ":{$id}cri{$i}";
                }
                //cria uma string separada por virgula
                $in = implode(", ", $arr);
                //retorna a expressão de acordo com IN
                return "{$this->variavel} {$this->operator} ($in)";
            } // ou BETWEEN
            else {
                if ($this->operator == "BETWEEN") {
                    //retorna a expressão de acordo com BETWEEN
                    return "{$this->variavel} {$this->operator} :{$id}cri1 AND :{$id}cri2";
                }
            }
        } //senão concatena a expressão normal
        else {
            //concatena a expressão
            return "{$this->variavel} {$this->operator} :{$id}cri";
        }
    }

    /*
     *Retorna o objeto atual
     */
    public function getExpression()
    {
        return $this;
    }

    /*
     *Retorna o valor de $variavel
     */
    public function getVariavel()
    {
        return $this->variavel;
    }

    /*
     *Retorna o valor de $operator
     */
    public function getOperator()
    {
        return $this->operator;
    }

    /*
     *Retorna o valor de $value
     */
    public function getValue()
    {
        return $this->value;
    }

    /*
     *Metodo que passara os valores para o objeto pstm de acordo com os indices
     * @param $pstm
     */
    public function whereBindValue($pstm)
    {
        //Pega o identificador do objeto
        $id = spl_object_hash($this->getExpression());
        // verifica se é array, que sera no caso de IN ou BETWEEN no operador
        if (is_array($this->getValue())) {
            //verifica se o operador é IN
            if ($this->getOperator() == "IN") {
                //percorre o array
                foreach ($this->getValue() as $i => $v) {
                    //passa os valores para o objeto $pstm
                    $pstm->bindValue(":{$id}cri{$i}", $v);
                }
            } // ou BETWEEN
            else {
                if ($this->getOperator() == "BETWEEN") {
                    //passa os valores para o objeto $pstm
                    $pstm->bindValue(":{$id}cri1", $this->getValue()[0]);
                    $pstm->bindValue(":{$id}cri2", $this->getValue()[1]);
                }
            }
        } //senão passa os valores para o objeto $pstm
        else {
            $pstm->bindValue(":{$id}cri", $this->getValue());
        }
    }
}