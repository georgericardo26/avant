<?php

/*
 * Classe para gerencia de expressões
 */

abstract class Expression
{
    //Operadores logicos
    const AND_OPERATOR = "AND";
    const OR_OPERATOR = "OR";

    /*
     *Metodos que devera existir nas suas classes filhas
     */
    abstract public function dump();

    abstract public function getExpression();

    abstract public function getVariavel();

    abstract public function getOperator();

    abstract public function getValue();

    abstract public function whereBindValue($pstm);
}