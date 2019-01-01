<?php
/**
 * Created by PhpStorm.
 * User: georgericardo
 * Date: 01/12/2018
 * Time: 16:45
 */

namespace Request;

use IRequest\IRequest;

include_once('IRequest.php');

class Request implements IRequest
{
    private $server = array();

    function __construct()
    {
        //funcao de inicializacao para setar todas as chaves do array SERVER
        $this->InitSelf();
    }

    //inicia e chama a funcao que faz a conversao de string de snakeCase para camelCase das chaves do $_SERVER
    private function InitSelf()
    {
        foreach ($_SERVER as $key => $value) {
            $this->{$this->toCamelCase($key)} = $value;
        }
        //ok aqui
    }

    //metodo para transformar string em CameCase
    function toCamelCase($string)
    {
        $result = strtolower($string);
        preg_match_all('/_[a-z]/', $result, $saida);
        foreach ($saida[0] as $match) {
            $c = str_replace('_', '', strtoupper($match));
            $result = str_replace($match, $c, $result);
        }
        return $result;
    }

    //verifica se e uma url valida
    public function checkUrlValidate($host, $uri)
    {
        $this->{$reqTotal} = "http://{$host}" . $uri;
        if (filter_var($this->{$reqTotal}, FILTER_VALIDATE_URL)) {
            return true;
        }
        return false;
    }

    //pega o body do request
    public function getBody()
    {
        $result = '';
        $requestMetodo = $_SERVER['REQUEST_METHOD'];
        //verifica se sao esses verbos que foi usado
        if ($requestMetodo == 'POST' || $requestMetodo == 'PUT' || $requestMetodo == 'PATCH') {
            //obtem os dados inseridos
            $result = json_decode(file_get_contents("php://input"));
            if (empty($result)) {
                return "";
            } else {
                return $result;
            }
        } else {
            return "error";
        }
    }

}