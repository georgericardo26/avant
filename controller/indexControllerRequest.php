<?php
/**
 * Created by PhpStorm.
 * User: georgericardo
 * Date: 02/12/2018
 * Time: 15:06
 */

namespace controller;

use model\errosHttp;

include('filterControllerRequest.php');
include('../model/errosHttp.php');

class indexControllerRequest
{
    private $db;
    private $pageInterval = 10;

    function __construct(\ClientesDAO $clientesDAO)
    {
        $this->db = $clientesDAO;
        $this->{$filter} = new filterControllerRequest();
    }

    public function getParameters($parameters, $callback){

        switch ($parameters["metodo"]){

            case "GET":

              $result = $this->get($parameters);

                if($result){

                    $callback($result);

                }
                break;

            case "POST":

                $result = $this->post($parameters);

                if($result){
                    //verifica se a resposta vem como array
                    if(is_array($result)){

                        $callback(json_encode($result));

                    }
                    //se nao manda a resposta do mesmo jeito
                    else {
                        $callback(json_encode($result));

                    }

                }
                else{

                    //errosHttp::erros(503);
                    http_response_code(503);
                    $callback(array("message" => "nao foi possivel inserir esta informacao"));
                }

                break;

            case "PUT":

               $result = $this->put($parameters);

               if($result){
                   header('Content-Type: application/json');
                    //verifica se a resposta vem como array
                    if(is_array($result)){


                        $callback(array("message: " => "informacoes alteradas com sucesso"));

                    }
                    //se nao manda a resposta do mesmo jeito
                    else {
                        $callback(json_encode(array("message: " => "informacoes alteradas com sucesso")));

                    }

                }
                else {
                    //errosHttp::erros(503);
                    http_response_code(503);
                    $callback(array("message" => "nao foi possivel inserir esta informacao"));
                }

                break;

            case "PATCH":

                $result = $this->patch($parameters);

                if($result){

                    $callback(array("message: " => "cliente atualizado com sucesso"));
                }
                else {
                    //errosHttp::erros(503);
                    http_response_code(503);
                    $callback(array("message" => "nao foi possivel atualizar esta informacao"));
                }


                break;

            case "DELETE":

                $result = $this->delete($parameters);

                if($result){

                        $callback(array("message: " => "cliente deletado com sucesso"));
                }
                else {
                    //errosHttp::erros(503);
                    http_response_code(503);
                    $callback(array("message" => "nao foi possivel deletar esta informacao"));
                }

                break;

        }

    }

   private function get($parameters){

     $result = $this->{$filter}->checkFilterGet($parameters);
     if($result) {
         //verifica se e array o return
        if(is_array($result)){

            //se a chave retornada for name, pesquisa pelo nome do cliente
            if(array_key_exists("name", $result)){

                //consulta apenas o cliente requisitado
                return $this->db->pesquisar("", array("nome" => $result["name"]));
            }


            else if(array_key_exists("id", $result)){

                //consulta apenas o cliente requisitado
                return $this->db->pesquisar("", array("id" => $result["id"]));
            }
            //se a chave retornada for page, pesquisa pelo numero da paginacao
            else if(array_key_exists("page", $result)){

                $offset = ($result["page"] - 1) * $this->pageInterval;

                $limit = $result["page"] * $this->pageInterval;

                //consulta tudo da tabela clientes com paginacao
                return $this->db->pesquisar("", array(), "id", $limit, $offset);

            }

            else {

                return $this->{$filter}->error();
            }

        }



         else if(is_int($result)) {

             //consulta tudo da tabela clientes
             return $this->db->pesquisar("", "");
         }


      }

     return $filter->error();

  }

   private function post($parameters){

       $result = $this->{$filter}->checkFilterPost($parameters);
       if($result) {
           //verifica se e array o return
           if(is_array($result)){
               //envia o body request para enviar no banco
               return $this->db->inserir($result);
           }
           else  {
               //retorna erro
               errosHttp::erros(503);
               return false;
           }


       }

       //retorna erro
       return $this->{$filter}->error();

   }

   private function put($parameters){


       $result = $this->{$filter}->checkFilterPut($parameters);

       if($result) {
           //verifica se e array o return
           if(is_array($result)){

               //envia o body request para enviar no banco
              return $this->db->atualizar($result[0], $result[0]["id"]);
           }
           else  {
               //retorna erro
               errosHttp::erros(503);
               return false;
           }


       }

       //retorna erro
       return $this->{$filter}->error();
   }

   private function delete($parameters){

        $result = $this->{$filter}->checkFilterDelete($parameters);
        //verifica se trouxe id
       if($result) {
           //envia o body request para enviar no banco
              return $this->db->deletar($result["id"]);
       }

       //retorna erro
       return $this->{$filter}->error();

   }

   private function patch($parameters){


       $result = $this->{$filter}->checkFilterPatch($parameters);

       if($result) {
           //verifica se e array o return
           if(is_array($result)){

               //envia o body request para enviar no banco
               return $this->db->atualizarPatch($result, $result["id"]);
           }
           else  {
               //retorna erro
               errosHttp::erros(503);
               return false;
           }


       }

       //retorna erro
       return $this->{$filter}->error();
   }

}