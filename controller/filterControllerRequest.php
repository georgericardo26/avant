<?php
/**
 * Created by PhpStorm.
 * User: georgericardo
 * Date: 04/12/2018
 * Time: 09:55
 */

namespace controller;


class filterControllerRequest
{

    private $var;
    private $int;


    function checkFilterGet($parameters) {

        if(count($parameters["uri"]) === 0){
            // set response code - 400 bad request
             return false;
        }

        if(count($parameters["uri"]) === 1){

            if(array_key_exists("acao", $parameters["uri"]) && !is_null($parameters["uri"]["acao"]) && !empty($parameters["uri"]["acao"]) ){

                return 1;
            }

        }
        if(count($parameters["uri"]) === 2){

                //verifica se existe as chaves mencionadas
                if(array_key_exists("acao", $parameters["uri"]) && array_key_exists("customer", $parameters["uri"])){

                    //verifica se os valores estao preenchidos
                    if( (!is_null($parameters["uri"]["acao"])  && !empty($parameters["uri"]["acao"])) && (!is_null($parameters["uri"]["customer"]) && !empty($parameters["uri"]["customer"])) ){


                        $this->{$valor} = $parameters["uri"]["customer"];


                        if(filter_var($this->{$valor}, FILTER_SANITIZE_NUMBER_INT)){

                            //consulta tudo da tabela clientes
                            return array("id" =>  intval($this->{$valor}));
                        }
                        else{

                            //consulta tudo da tabela clientes
                            return array("name" =>  $this->{$valor});
                        }

                    }

                }

                //verifica se existe as chaves mencionadas
                else if (array_key_exists("acao", $parameters["uri"]) && array_key_exists("page", $parameters["uri"])) {


                    if(!is_null($parameters["uri"]["acao"]) && !empty($parameters["uri"]["acao"])){

                        if(!is_null($parameters["uri"]["page"]) && !empty($parameters["uri"]["page"])){

                            //verifica se e int a variavel page
                            if(filter_var($parameters["uri"]["page"], FILTER_VALIDATE_INT)){


                                //consulta tudo da tabela clientes
                                return array("page" =>  $parameters["uri"]["page"]);

                            }

                            return false;

                        }

                        else {

                            return false;
                        }

                    }

                    else {

                        return false;
                    }
                }

            else {
                return false;
            }



        }


    }

    function checkFilterPost($parameters){

        if(count($parameters["uri"]) === 0){
            // set response code - 400 bad request
            return false;
        }

        if(count($parameters["uri"]) === 1){

            $indices = array();

            if(array_key_exists("acao", $parameters["uri"]) && !is_null($parameters["uri"]["acao"]) && !empty($parameters["uri"]["acao"]) ){


               if(array_key_exists("body", $parameters) && !empty($parameters["body"])){

                   foreach ($parameters["body"]->create as $key){

                       $array["nome"] = $key->nome;
                       $array["cpf"] = $key->cpf;
                       $array["nascimento"] = $key->nascimento;

                       array_push($indices, $array);

                   }

                   return $indices;

               }

               return false;

            }

        }

        else {
            return false;
        }

    }

    function checkFilterPut($parameters){

        if(count($parameters["uri"]) === 0){
            // set response code - 400 bad request
            return false;
        }

        if(count($parameters["uri"]) === 1) {

            $indices = array();

            if(array_key_exists("acao", $parameters["uri"]) && !is_null($parameters["uri"]["acao"]) && !empty($parameters["uri"]["acao"]) ){


                if(array_key_exists("body", $parameters) && !empty($parameters["body"])){

                    if(array_key_exists("put", $parameters["body"])){

                        //preenche o array
                            $array["id"] = $parameters["body"]->put->id;
                            $array["nome"] = $parameters["body"]->put->nome;
                            $array["cpf"] = $parameters["body"]->put->cpf;
                            $array["nascimento"] = $parameters["body"]->put->nascimento;

                            array_push($indices, $array);

                         return $indices;

                    }

                }

                return false;

            }

        }

        else {
            return false;
        }
    }

    function checkFilterDelete($parameters){

        if(count($parameters["uri"]) === 0){
            // set response code - 400 bad request
            return false;
        }

        if(count($parameters["uri"]) === 1) {

            $indices = array();

            if(array_key_exists("acao", $parameters["uri"]) && !is_null($parameters["uri"]["acao"]) && !empty($parameters["uri"]["acao"]) ){


                if(array_key_exists("body", $parameters) && !empty($parameters["body"])){

                    if(array_key_exists("delete", $parameters["body"])){

                        if(isset($parameters["body"]->delete->id)){

                            //pega id
                            return $parameters["body"]->delete->id;

                        }

                        return false;

                    }

                }

                return false;

            }

        }

        else {
            return false;
        }

    }

    function checkFilterPatch($parameters){

        if(count($parameters["uri"]) === 0){
            // set response code - 400 bad request
            return false;
        }

        if(count($parameters["uri"]) === 1) {

            $indices = array();

            if(array_key_exists("acao", $parameters["uri"]) && !is_null($parameters["uri"]["acao"]) && !empty($parameters["uri"]["acao"]) ){


                if(array_key_exists("body", $parameters) && !empty($parameters["body"])){

                    if(array_key_exists("patch", $parameters["body"])){

                        if(isset($parameters["body"]->patch->id)) {

                            //preenche o array
                            $array["id"] = $parameters["body"]->patch->id;
                            $array["nome"] = isset($parameters["body"]->patch->nome) ? $parameters["body"]->patch->nome : "";
                            $array["cpf"] = isset($parameters["body"]->patch->cpf) ? $parameters["body"]->patch->cpf : "";
                            $array["nascimento"] = isset($parameters["body"]->patch->nascimento) ? $parameters["body"]->patch->nascimento : "";

                            $indices[$array["id"]];

                            foreach ($array as $key => $value){

                                $indices[$key] = $value;
                            }

                            return $indices;
                        }

                        return false;

                    }

                }

                return false;

            }

        }

        else {
            return false;
        }
    }


}