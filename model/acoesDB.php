<?php
/**
 * Created by PhpStorm.
 * User: georgericardo
 * Date: 02/12/2018
 * Time: 22:20
 */

namespace model;
include_once ('dbModel.php');

class acoesDB extends dbModel
{

    function __construct()
    {
        //pega configuracoes do banco
        parent::__construct();
    }

    public function selectAll($table = "cliente"){

    /*   $sql = "SELECT * FROM ".$table;

       $result = $this->select($sql, array());
       if($result){
           return $result;
       }
        http_response_code(403);
        return json_encode(array("message" => "erro no banco."));*/
    }



}