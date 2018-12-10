<?php
/**
 * Created by PhpStorm.
 * User: georgericardo
 * Date: 05/12/2018
 * Time: 22:16
 */

namespace model;


class errosHttp
{

    //seta os erros no cabecalho
    static function erros($code){

        http_response_code($code);
        header("erro com o codigo: ".$code);
    }

}