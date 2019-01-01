<?php
/**
 * Created by PhpStorm.
 * User: georgericardo
 * Date: 29/12/2018
 * Time: 17:38
 */
namespace model\erros\erros;
class erros {

    public $erros = array(
        "302" => "Found",
        "400" => "Bad Request",
        "401" => "Unauthorized",
        "403" => "Forbidden",
        "404" => "Not Found",
        "405" => "Method Not Allowed",
        "406" => "Not Acceptable",
        "415" => "Unsupported Media Type",
        "422" => "Unprocessable Entitity",
        "429" => "Too Many Requests",
        "500" => "Internal Server Error",
        "503" => "Service Unavailable"
    );
    public $currentError = array("errors" => array());

    public function setaErro($statusCode, $message)
    {
        if (array_key_exists($statusCode, $this->erros)) {
             $this->currentError["errors"][] = array(
                 "status" => $statusCode,
                 "source" => "http://avant:8888".$_SERVER["REQUEST_URI"],
                 "title" => $this->erros[$statusCode],
                 "detail" => $message
             );

         }
         else {
             $this->currentError["errors"][] = array(
                 "status" => "500",
                 "source" => "http://avant:8888".$_SERVER["REQUEST_URI"],
                 "title" => $this->erros["500"],
                 "detail" => ""
             );


         }


    }

    public function setErrorDefault(){

           $this->currentError["errors"][] = array(
               "status" => "500",
               "source" => "http://avant:8888".$_SERVER["REQUEST_URI"],
               "title" => $this->erros["500"],
               "detail" => ""
           );
           return $this->currentError;
       }


}