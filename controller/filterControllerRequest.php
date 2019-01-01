<?php
/**
 * Created by PhpStorm.
 * User: georgericardo
 * Date: 04/12/2018
 * Time: 09:55
 */

namespace controller;

include_once('errosHttp.php');
include_once('checkFilterFunctions.php');

use controller\errosHttp\errosHttp;

class filterControllerRequest
{
    private $var;
    private $int;
    private $arrayParameters = array();
    private $errosModelObject;

    public function __construct($errosModel)
    {
        $this->errosModelObject = $errosModel;
    }

    public function checkFilterGet($parameters)
    {
        if (empty($parameters["uri"])) {
            // set response code - 400 bad request
            $GLOBALS["erros"] = true;
            errosHttp\errosHttp::setaErro($this->errosModelObject, "400", "URI Request Invalid");
            return false;
        } else {
            //se tiver 2 parametros
            if (count($parameters["uri"]) === 2) {
                //verifica se os parametros estao corretos
                if ($parameters["uri"][0] === "api" && $parameters["uri"][1] === "customers") {
                    $this->arrayParameters["check"] = true;
                    //verifica se tem query
                    if (!empty($parameters["query"])) {
                        switch (count($parameters["query"])) {
                            case 1:
                                $res = array_key_exists("page", $parameters["query"]) || array_key_exists("search",
                                        $parameters["query"]) || array_key_exists("order", $parameters["query"]);
                                if ($res) {
                                    switch (array_keys($parameters["query"])[0]) {
                                        case "page":
                                            if (!checkPage($parameters["query"]["page"])) {
                                                $GLOBALS["erros"] = true;
                                                errosHttp\errosHttp::setaErro($this->errosModelObject, "422",
                                                    "Query parameter format invalid, the parameter format for this query is STRING");
                                                return false;
                                            }
                                            break;
                                        case "search":
                                            if (!checkSearch($parameters["query"]["search"])) {
                                                $GLOBALS["erros"] = true;
                                                errosHttp\errosHttp::setaErro($this->errosModelObject, "422",
                                                    "Query parameter format invalid, the parameter format for this query is STRING");
                                                return false;
                                            }
                                            break;
                                        case "order":
                                            if (filter_var($parameters["query"]["order"], FILTER_SANITIZE_STRING)) {
                                                if (!checkOrder($parameters["query"]["order"])) {
                                                    $GLOBALS["erros"] = true;
                                                    errosHttp\errosHttp::setaErro($this->errosModelObject, "422",
                                                        "Query parameter format invalid, the parameter informed for this query don't matching with our attributes");
                                                    return false;
                                                }
                                            } else {
                                                $GLOBALS["erros"] = true;
                                                errosHttp\errosHttp::setaErro($this->errosModelObject, "422",
                                                    "Query parameter format invalid, the parameter format for this query is STRING");
                                                return false;
                                            }
                                            break;
                                    }
                                    $this->arrayParameters["query"][array_keys($parameters["query"])[0]] = $parameters["query"][array_keys($parameters["query"])[0]];
                                } else {
                                    $GLOBALS["erros"] = true;
                                    errosHttp\errosHttp::setaErro($this->errosModelObject, "406",
                                        "Query parameters not supported");
                                    return false;
                                }
                                break;
                            case 2:
                                $res = (combinateSearchAndOrder($parameters["query"]) || combinateOffsetAndLimit($parameters["query"]));
                                if ($res) {
                                    $this->arrayParameters["query"][array_keys($parameters["query"])[0]] = $parameters["query"][array_keys($parameters["query"])[0]];
                                    $this->arrayParameters["query"][array_keys($parameters["query"])[1]] = $parameters["query"][array_keys($parameters["query"])[1]];
                                } else {
                                    $GLOBALS["erros"] = true;
                                    errosHttp\errosHttp::setaErro($this->errosModelObject, "406",
                                        "Query parameters not supported or don't sorted");
                                    return false;
                                }
                                break;
                            case 3:
                                $res = (combinateSearchAndOffsetAndLimit($parameters["query"])) ||
                                    (combinateOffsetAndLimitAndOrder($parameters["query"]));
                                if ($res) {
                                    $this->arrayParameters["query"][array_keys($parameters["query"])[0]] = $parameters["query"][array_keys($parameters["query"])[0]];
                                    $this->arrayParameters["query"][array_keys($parameters["query"])[1]] = $parameters["query"][array_keys($parameters["query"])[1]];
                                    $this->arrayParameters["query"][array_keys($parameters["query"])[2]] = $parameters["query"][array_keys($parameters["query"])[2]];
                                } else {
                                    $GLOBALS["erros"] = true;
                                    errosHttp\errosHttp::setaErro($this->errosModelObject, "406",
                                        "Query parameters not supported or don't sorted");
                                    return false;
                                }
                                break;
                            case 4:
                                $res = (combinateSearchAndOffsetAndLimitAndOrder($parameters["query"]));
                                if ($res) {
                                    $this->arrayParameters["query"][array_keys($parameters["query"])[0]] = $parameters["query"][array_keys($parameters["query"])[0]];
                                    $this->arrayParameters["query"][array_keys($parameters["query"])[1]] = $parameters["query"][array_keys($parameters["query"])[1]];
                                    $this->arrayParameters["query"][array_keys($parameters["query"])[2]] = $parameters["query"][array_keys($parameters["query"])[2]];
                                    $this->arrayParameters["query"][array_keys($parameters["query"])[3]] = $parameters["query"][array_keys($parameters["query"])[3]];
                                } else {
                                    $GLOBALS["erros"] = true;
                                    errosHttp\errosHttp::setaErro($this->errosModelObject, "406",
                                        "Query parameters not supported or don't sorted");
                                    return false;
                                }
                                break;
                            default:
                                $GLOBALS["erros"] = true;
                                errosHttp\errosHttp::setaErro($this->errosModelObject, "406",
                                    "Query parameters not supported");
                                return false;
                        }
                        return $this->arrayParameters;
                    } //se nao tiver retorna somente o ok
                    else {
                        return $this->arrayParameters;
                    }
                }
                $GLOBALS["erros"] = true;
                errosHttp\errosHttp::setaErro($this->errosModelObject, "406", "URI requested invalid");
                return false;
            } //se tiver 3 parametros
            else {
                if (count($parameters["uri"]) === 3) {
                    if (!empty($parameters["query"])) {
                        $GLOBALS["erros"] = true;
                        errosHttp\errosHttp::setaErro($this->errosModelObject, "406",
                            "When having search by id, you can not insert query parameters");
                        return false;
                    }
                    //verifica se os parametros estao corretos
                    if ($parameters["uri"][0] === "api" && $parameters["uri"][1] === "customers") {
                        //o terceiro parametro precisa ser inteiro
                        if (!filter_var($parameters["uri"][2], FILTER_SANITIZE_NUMBER_INT)) {
                            $GLOBALS["erros"] = true;
                            errosHttp\errosHttp::setaErro($this->errosModelObject, "406",
                                "The parameter ID must be INT format");
                            return false;
                        }
                        $this->arrayParameters["check"] = true;
                        $this->arrayParameters["id"] = $parameters["uri"][2];
                        return $this->arrayParameters;
                    }
                    $GLOBALS["erros"] = true;
                    errosHttp\errosHttp::setaErro($this->errosModelObject, "406", "URI requested invalid");
                    return false;
                }
            }
            return false;
        }
    }

    public function checkFilterPost($parameters)
    {
        if (empty($parameters["uri"])) {
            // set response code - 400 bad request
            $GLOBALS["erros"] = true;
            errosHttp\errosHttp::setaErro($this->errosModelObject, "400", "URI Request Invalid");
        } else {
            if (count($parameters["uri"]) === 2) {
                if ($parameters["uri"][0] === "api" && $parameters["uri"][1] === "customers") {
                    $this->arrayParameters["check"] = true;
                    if (key_exists("create", $parameters["body"])) {
                        $parameters["body"] = (array)$parameters["body"];
                        $parameters["body"]["create"] = (array)$parameters["body"]["create"];
                        $array = array();
                        $arrayParameters = array();
                        foreach ($parameters["body"]["create"] as $key => $val) {
                            $keyArr = (array)$val;
                            //verifica se as propriedades estao corretas
                            filterCheckName(array_keys($keyArr)[0], $this->errosModelObject);
                            filterCheckCpf(array_keys($keyArr)[1], $this->errosModelObject);
                            filterCheckNascimento(array_keys($keyArr)[2], $this->errosModelObject);
                            //verifica se e string
                            filterCheckValueName(array_values($keyArr)[0], $this->errosModelObject);
                            filterCheckValueCpf(array_values($keyArr)[1], $this->errosModelObject);
                            filterCheckValueNascimento(array_values($keyArr)[2], $this->errosModelObject);
                            $this->arrayParameters["body"][] = $keyArr;
                        }
                    } else {
                        $keyArr = (array)$parameters["body"];
                        //verifica se as propriedades estao corretas
                        filterCheckName(array_keys($keyArr)[0], $this->errosModelObject);
                        filterCheckCpf(array_keys($keyArr)[1], $this->errosModelObject);
                        filterCheckNascimento(array_keys($keyArr)[2], $this->errosModelObject);
                        //verifica se e string
                        filterCheckValueName(array_values($keyArr)[0], $this->errosModelObject);
                        filterCheckValueCpf(array_values($keyArr)[1], $this->errosModelObject);
                        filterCheckValueNascimento(array_values($keyArr)[2], $this->errosModelObject);
                        $this->arrayParameters["body"][] = $keyArr;
                    }
                    return $this->arrayParameters["body"];
                } else {
                    $GLOBALS["erros"] = true;
                    errosHttp\errosHttp::setaErro($this->errosModelObject, "406", "Not Acceptable Resource");
                }
            }
        }
    }

    public function checkFilterPut($parameters)
    {
        if (empty($parameters["uri"])) {
            // set response code - 400 bad request
            $GLOBALS["erros"] = true;
            errosHttp\errosHttp::setaErro($this->errosModelObject, "400", "URI Request Invalid");
        } else {
            if (count($parameters["uri"]) === 3) {
                if ($parameters["uri"][0] === "api" && $parameters["uri"][1] === "customers") {
                    if (!filter_var($parameters["uri"][2], FILTER_SANITIZE_NUMBER_INT)) {
                        $GLOBALS["erros"] = true;
                        errosHttp\errosHttp::setaErro($this->errosModelObject, "400", "URI Request Invalid");
                    } else {
                        $this->arrayParameters["id"] = $parameters["uri"][2];
                        $err = errosHttp\errosHttp::outputError($this->errosModelObject);
                        if (empty($err[1]["errors"])) {
                            $this->arrayParameters["check"] = true;
                            $keyArr = (array)$parameters["body"];
                            if (count($keyArr) === 3) {
                                //verifica se as propriedades estao corretas
                                filterCheckName(array_keys($keyArr)[0], $this->errosModelObject);
                                filterCheckCpf(array_keys($keyArr)[1], $this->errosModelObject);
                                filterCheckNascimento(array_keys($keyArr)[2], $this->errosModelObject);
                                //verifica se e string
                                filterCheckValueName(array_values($keyArr)[0], $this->errosModelObject);
                                filterCheckValueCpf(array_values($keyArr)[1], $this->errosModelObject);
                                filterCheckValueNascimento(array_values($keyArr)[2], $this->errosModelObject);
                                $this->arrayParameters["body"] = $keyArr;
                            } else {
                                $GLOBALS["erros"] = true;
                                errosHttp\errosHttp::setaErro($this->errosModelObject, "406",
                                    "The amount properties content a number higher to 3");
                            }
                            return $this->arrayParameters;
                        }
                    }
                } else {
                    $GLOBALS["erros"] = true;
                    errosHttp\errosHttp::setaErro($this->errosModelObject, "406", "Not Acceptable Resource");
                }
            } else {
                $GLOBALS["erros"] = true;
                errosHttp\errosHttp::setaErro($this->errosModelObject, "406", "Not Acceptable Resource");
            }
        }
    }

    public function checkFilterDelete($parameters)
    {
        if (empty($parameters["uri"])) {
            // set response code - 400 bad request
            $GLOBALS["erros"] = true;
            errosHttp\errosHttp::setaErro($this->errosModelObject, "400", "URI Request Invalid");
        } else {
            //verifica parametros da uri
            if (count($parameters["uri"]) === 3) {
                //verifica parametros da uri
                if ($parameters["uri"][0] === "api" && $parameters["uri"][1] === "customers") {
                    if (!filter_var($parameters["uri"][2], FILTER_SANITIZE_NUMBER_INT)) {
                        $GLOBALS["erros"] = true;
                        errosHttp\errosHttp::setaErro($this->errosModelObject, "400", "URI Request Invalid");
                    } else {
                        $this->arrayParameters["id"] = $parameters["uri"][2];
                        $err = errosHttp\errosHttp::outputError($this->errosModelObject);
                        if (empty($err[1]["errors"])) {
                            $this->arrayParameters["check"] = true;
                            return $this->arrayParameters;
                        }
                    }
                } else {
                    $GLOBALS["erros"] = true;
                    errosHttp\errosHttp::setaErro($this->errosModelObject, "406", "Not Acceptable Resource");
                }
            } else {
                $GLOBALS["erros"] = true;
                errosHttp\errosHttp::setaErro($this->errosModelObject, "406", "Not Acceptable Resource");
            }
        }
    }

    public function checkFilterPatch($parameters)
    {
        if (empty($parameters["uri"])) {
            // set response code - 400 bad request
            $GLOBALS["erros"] = true;
            errosHttp\errosHttp::setaErro($this->errosModelObject, "400", "URI Request Invalid");
        } else {
            if (count($parameters["uri"]) === 3) {
                if ($parameters["uri"][0] === "api" && $parameters["uri"][1] === "customers") {
                    if (!filter_var($parameters["uri"][2], FILTER_SANITIZE_NUMBER_INT)) {
                        $GLOBALS["erros"] = true;
                        errosHttp\errosHttp::setaErro($this->errosModelObject, "400", "URI Request Invalid");
                    } else {
                        $this->arrayParameters["id"] = $parameters["uri"][2];
                        $err = errosHttp\errosHttp::outputError($this->errosModelObject);
                        if (empty($err[1]["errors"])) {
                            $this->arrayParameters["check"] = true;
                            $keyArr = (array)$parameters["body"];
                            if (count($keyArr) > 3) {
                                $GLOBALS["erros"] = true;
                                errosHttp\errosHttp::setaErro($this->errosModelObject, "406",
                                    "The amount properties content a number higher to 3");
                            } else {
                                switch (count($keyArr)) {
                                    case 1:
                                        switch (array_keys($keyArr)[0]) {
                                            case "id":
                                                $GLOBALS["erros"] = true;
                                                errosHttp\errosHttp::setaErro($this->errosModelObject, "406",
                                                    "Don't allowed using ID property");
                                                break;
                                            case "link":
                                                $GLOBALS["erros"] = true;
                                                errosHttp\errosHttp::setaErro($this->errosModelObject, "406",
                                                    "Don't allowed using Link property");
                                                break;
                                            case "nome":
                                                filterCheckName(array_keys($keyArr)[0], $this->errosModelObject);
                                                filterCheckValueName(array_values($keyArr)[0], $this->errosModelObject);
                                                break;
                                            case "cpf":
                                                filterCheckCpf(array_keys($keyArr)[0], $this->errosModelObject);
                                                filterCheckValueCpf(array_values($keyArr)[0], $this->errosModelObject);
                                                break;
                                            case "nascimento":
                                                filterCheckNascimento(array_keys($keyArr)[0], $this->errosModelObject);
                                                filterCheckValueNascimento(array_values($keyArr)[0],
                                                    $this->errosModelObject);
                                                break;
                                            default:
                                                $GLOBALS["erros"] = true;
                                                errosHttp\errosHttp::setaErro($this->errosModelObject, "406",
                                                    "Property not allowed");
                                        }
                                        $this->arrayParameters["body"] = $keyArr;
                                        break;
                                    case 2:
                                        switch (array_keys($keyArr)[0]) {
                                            case "id":
                                                $GLOBALS["erros"] = true;
                                                errosHttp\errosHttp::setaErro($this->errosModelObject, "406",
                                                    "Don't allowed using ID property");
                                                break;
                                            case "link":
                                                $GLOBALS["erros"] = true;
                                                errosHttp\errosHttp::setaErro($this->errosModelObject, "406",
                                                    "Don't allowed using Link property");
                                                break;
                                            case "nome":
                                                filterCheckName(array_keys($keyArr)[0], $this->errosModelObject);
                                                filterCheckValueName(array_values($keyArr)[0], $this->errosModelObject);
                                                break;
                                            case "cpf":
                                                filterCheckCpf(array_keys($keyArr)[0], $this->errosModelObject);
                                                filterCheckValueCpf(array_values($keyArr)[0], $this->errosModelObject);
                                                break;
                                            case "nascimento":
                                                filterCheckNascimento(array_keys($keyArr)[0], $this->errosModelObject);
                                                filterCheckValueNascimento(array_values($keyArr)[0],
                                                    $this->errosModelObject);
                                                break;
                                            default:
                                                $GLOBALS["erros"] = true;
                                                errosHttp\errosHttp::setaErro($this->errosModelObject, "406",
                                                    "Property not allowed");
                                        }
                                        switch (array_keys($keyArr)[1]) {
                                            case "id":
                                                $GLOBALS["erros"] = true;
                                                errosHttp\errosHttp::setaErro($this->errosModelObject, "406",
                                                    "Don't allowed using ID property");
                                                break;
                                            case "link":
                                                $GLOBALS["erros"] = true;
                                                errosHttp\errosHttp::setaErro($this->errosModelObject, "406",
                                                    "Don't allowed using Link property");
                                                break;
                                            case "nome":
                                                filterCheckName(array_keys($keyArr)[1], $this->errosModelObject);
                                                filterCheckValueName(array_values($keyArr)[1], $this->errosModelObject);
                                                break;
                                            case "cpf":
                                                filterCheckCpf(array_keys($keyArr)[1], $this->errosModelObject);
                                                filterCheckValueCpf(array_values($keyArr)[1], $this->errosModelObject);
                                                break;
                                            case "nascimento":
                                                filterCheckNascimento(array_keys($keyArr)[1], $this->errosModelObject);
                                                filterCheckValueNascimento(array_values($keyArr)[1],
                                                    $this->errosModelObject);
                                                break;
                                            default:
                                                $GLOBALS["erros"] = true;
                                                errosHttp\errosHttp::setaErro($this->errosModelObject, "406",
                                                    "Property not allowed");
                                        }
                                        $this->arrayParameters["body"] = $keyArr;
                                        break;
                                    case 3:
                                        switch (array_keys($keyArr)[0]) {
                                            case "id":
                                                $GLOBALS["erros"] = true;
                                                errosHttp\errosHttp::setaErro($this->errosModelObject, "406",
                                                    "Don't allowed using ID property");
                                                break;
                                            case "link":
                                                $GLOBALS["erros"] = true;
                                                errosHttp\errosHttp::setaErro($this->errosModelObject, "406",
                                                    "Don't allowed using Link property");
                                                break;
                                            case "nome":
                                                filterCheckName(array_keys($keyArr)[0], $this->errosModelObject);
                                                filterCheckValueName(array_values($keyArr)[0], $this->errosModelObject);
                                                break;
                                            case "cpf":
                                                filterCheckCpf(array_keys($keyArr)[0], $this->errosModelObject);
                                                filterCheckValueCpf(array_values($keyArr)[0], $this->errosModelObject);
                                                break;
                                            case "nascimento":
                                                filterCheckNascimento(array_keys($keyArr)[0], $this->errosModelObject);
                                                filterCheckValueNascimento(array_values($keyArr)[0],
                                                    $this->errosModelObject);
                                                break;
                                            default:
                                                $GLOBALS["erros"] = true;
                                                errosHttp\errosHttp::setaErro($this->errosModelObject, "406",
                                                    "Property not allowed");
                                        }
                                        switch (array_keys($keyArr)[1]) {
                                            case "id":
                                                $GLOBALS["erros"] = true;
                                                errosHttp\errosHttp::setaErro($this->errosModelObject, "406",
                                                    "Don't allowed using ID property");
                                                break;
                                            case "link":
                                                $GLOBALS["erros"] = true;
                                                errosHttp\errosHttp::setaErro($this->errosModelObject, "406",
                                                    "Don't allowed using Link property");
                                                break;
                                            case "nome":
                                                filterCheckName(array_keys($keyArr)[1], $this->errosModelObject);
                                                filterCheckValueName(array_values($keyArr)[1], $this->errosModelObject);
                                                break;
                                            case "cpf":
                                                filterCheckCpf(array_keys($keyArr)[1], $this->errosModelObject);
                                                filterCheckValueCpf(array_values($keyArr)[1], $this->errosModelObject);
                                                break;
                                            case "nascimento":
                                                filterCheckNascimento(array_keys($keyArr)[1], $this->errosModelObject);
                                                filterCheckValueNascimento(array_values($keyArr)[1],
                                                    $this->errosModelObject);
                                                break;
                                            default:
                                                $GLOBALS["erros"] = true;
                                                errosHttp\errosHttp::setaErro($this->errosModelObject, "406",
                                                    "Property not allowed");
                                        }
                                        switch (array_keys($keyArr)[2]) {
                                            case "id":
                                                $GLOBALS["erros"] = true;
                                                errosHttp\errosHttp::setaErro($this->errosModelObject, "406",
                                                    "Don't allowed using ID property");
                                                break;
                                            case "link":
                                                $GLOBALS["erros"] = true;
                                                errosHttp\errosHttp::setaErro($this->errosModelObject, "406",
                                                    "Don't allowed using Link property");
                                                break;
                                            case "nome":
                                                filterCheckName(array_keys($keyArr)[2], $this->errosModelObject);
                                                filterCheckValueName(array_values($keyArr)[2], $this->errosModelObject);
                                                break;
                                            case "cpf":
                                                filterCheckCpf(array_keys($keyArr)[2], $this->errosModelObject);
                                                filterCheckValueCpf(array_values($keyArr)[2], $this->errosModelObject);
                                                break;
                                            case "nascimento":
                                                filterCheckNascimento(array_keys($keyArr)[2], $this->errosModelObject);
                                                filterCheckValueNascimento(array_values($keyArr)[2],
                                                    $this->errosModelObject);
                                                break;
                                            default:
                                                $GLOBALS["erros"] = true;
                                                errosHttp\errosHttp::setaErro($this->errosModelObject, "406",
                                                    "Property not allowed");
                                        }
                                        $this->arrayParameters["body"] = $keyArr;
                                        break;
                                }
                                return $this->arrayParameters;
                            }
                        }
                    }
                } else {
                    $GLOBALS["erros"] = true;
                    errosHttp\errosHttp::setaErro($this->errosModelObject, "406", "Not Acceptable Resource");
                }
            } else {
                $GLOBALS["erros"] = true;
                errosHttp\errosHttp::setaErro($this->errosModelObject, "406", "Not Acceptable Resource");
            }
        }
    }

}