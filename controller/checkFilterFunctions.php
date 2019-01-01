<?php
/**
 * Created by PhpStorm.
 * User: georgericardo
 * Date: 27/12/2018
 * Time: 16:18
 */
include_once('errosHttp.php');

use controller\errosHttp\errosHttp;

function checkSearch($search)
{
    if (filter_var($search, FILTER_SANITIZE_NUMBER_INT)) {
        return false;
    }
    return true;
}

function checkPage($page)
{
    if (!filter_var($page, FILTER_SANITIZE_NUMBER_INT)) {
        return false;
    }
    return true;
}

function checkLimit($limit)
{
    return filter_var($limit, FILTER_SANITIZE_NUMBER_INT);
}

function checkOffset($offset)
{
    return filter_var($offset, FILTER_SANITIZE_NUMBER_INT);
}

function checkOrder($order)
{
    if (filter_var($order, FILTER_SANITIZE_STRING)) {
        if ($order === 'nome' || $order === '-nome' || $order === 'id' || $order === '-id') {
            return true;
        }
        return false;
    } else {
        $GLOBALS["erros"] = true;
        //errosHttp::setaErro("422", "Query parameter format invalid, the parameter format for this query is STRING");
        return false;
    }
}

function combinateSearchAndOrder($array)
{
    if (array_keys($array)[0] === "search" && array_keys($array)[1] === "order") {
        if (checkSearch($array["search"]) && checkOrder($array["order"])) {
            return true;
        }
        return false;
    }
    return false;
}

function combinateOffsetAndLimit($array)
{
    if (array_keys($array)[0] === "offset" && array_keys($array)[1] === "limit") {
        if (checkOffset($array["offset"]) && checkLimit($array["limit"])) {
            return true;
        }
        return false;
    }
    return false;
}

function combinateSearchAndOffsetAndLimit($array)
{
    if (array_keys($array)[0] === "search" && array_keys($array)[1] === "offset" && array_keys($array)[2] === "limit") {
        if (checkSearch($array["search"]) && checkOffset($array["offset"]) && checkLimit($array["limit"])) {
            return true;
        }
        return false;
    }
    return false;
}

function combinateOffsetAndLimitAndOrder($array)
{
    if (array_keys($array)[0] === "offset" && array_keys($array)[1] === "limit" && array_keys($array)[2] === "order") {
        if (checkOffset($array["offset"]) && checkLimit($array["limit"]) && checkOrder($array["order"])) {
            return true;
        }
        return false;
    }
    return false;
}

function combinateSearchAndOffsetAndLimitAndOrder($array)
{
    if (array_keys($array)[0] === "search" && array_keys($array)[1] === "offset" && array_keys($array)[2] === "limit" && array_keys($array)[3] === "order") {
        if (checkSearch($array["search"]) && checkOffset($array["offset"]) && checkLimit($array["limit"]) && checkOrder($array["order"])) {
            return true;
        }
        return false;
    }
    return false;
}

function filterCheckName($name, $object)
{
    if ($name !== "nome") {
        $GLOBALS["erros"] = true;
        errosHttp\errosHttp::setaErro($object, "422",
            "Property [0] wrong, this property must be called nome");
    }
}

function filterCheckValueName($valueName, $object)
{
    if (filter_var($valueName, FILTER_SANITIZE_NUMBER_INT)) {
        $GLOBALS["erros"] = true;
        errosHttp\errosHttp::setaErro($object, "422",
            "Value [0] wrong, this value must of type string");
    }
    if (preg_match('/[^a-zA-ZíáãâÁÃÂéêÉÊíîÍÎóõôÔÓÕúûÚÛçÇ \d]/', $valueName)) {
//$string contains special characters, do something.
        $GLOBALS["erros"] = true;
        errosHttp\errosHttp::setaErro($object, "422",
            "Value [0] wrong, this value must not having special characters");
    }
}

function filterCheckCpf($cpf, $object)
{
    if ($cpf !== "cpf") {
        $GLOBALS["erros"] = true;
        errosHttp\errosHttp::setaErro($object, "422",
            "Property [1] wrong, this property must be called cpf");
    }
}

function filterCheckValueCpf($valueCpf, $object)
{
    if (!filter_var($valueCpf, FILTER_SANITIZE_NUMBER_INT)) {
        $GLOBALS["erros"] = true;
        errosHttp\errosHttp::setaErro($object, "422",
            "Value [1] wrong, this value must of type INT");
    }
    if (strlen($valueCpf) !== 11) {
        $GLOBALS["erros"] = true;
        errosHttp\errosHttp::setaErro($object, "422",
            "Value [1] wrong, this value must content 11 digits");
    }
    if (substr($valueCpf, 0, 2) == 00) {
        $GLOBALS["erros"] = true;
        errosHttp\errosHttp::setaErro($object, "422",
            "Value [1] wrong, cpf is invalid format");
    }
}

function filterCheckNascimento($nascimento, $object)
{
    if ($nascimento !== "nascimento") {
        $GLOBALS["erros"] = true;
        errosHttp\errosHttp::setaErro($object, "422",
            "Property [2] wrong, this property must be called nascimento");
    }
}

function filterCheckValueNascimento($valueNascimento, $object)
{
    $data = $valueNascimento;
    $data = explode("/", $data);
    $year = strlen(intval($data[2])) === 4 ? intval($data[2]) : "";
    if (!checkdate(intval($data[1]), intval($data[0]), $year)) {
        $GLOBALS["erros"] = true;
        errosHttp\errosHttp::setaErro($object, "422",
            "Value [2] be wrong format, the valid format for date is dd/mm/yyyy");
    }
}