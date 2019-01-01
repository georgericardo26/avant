<?php
/**
 * Created by PhpStorm.
 * User: georgericardo
 * Date: 02/12/2018
 * Time: 15:06
 */

namespace controller;

include('filterControllerRequest.php');
include_once('errosHttp.php');

use controller\errosHttp\errosHttp;

class indexControllerRequest
{
    private $db;
    private $pageInterval = 10;
    private $errosModelObject;
    private $saida;
    private $erros;
    private $id;

    function __construct(\ClientesDAO $clientesDAO, $errosModel)
    {
        $this->db = $clientesDAO;
        $this->{$filter} = new filterControllerRequest($errosModel);
        $this->errosModelObject = $errosModel;
    }

    public function getParameters($parameters, $callback)
    {
        switch ($parameters["metodo"]) {
            case "GET":
                $this->get($parameters);
                if (empty($this->erros[1]["errors"])) {
                    if (empty($this->saida["result"])) {
                        http_response_code(204);
                    }
                    $callback($this->saida);
                } else {
                    $this->erros = errosHttp\errosHttp::outputError($this->errosModelObject);
                    $callback($this->erros[1]);
                    http_response_code($this->erros[0]);
                }
                break;
            case "POST":
                $this->post($parameters);
                $this->erros = errosHttp\errosHttp::outputError($this->errosModelObject);
                if (empty($this->erros[1]["errors"])) {
                    $callback($this->saida);
                    http_response_code(201);
                } else {
                    $this->erros = errosHttp\errosHttp::outputError($this->errosModelObject);
                    $callback($this->erros[1]);
                    http_response_code($this->erros[0]);
                }
                break;
            case "PUT":
                $this->put($parameters);
                $this->erros = errosHttp\errosHttp::outputError($this->errosModelObject);
                if (empty($this->erros[1]["errors"])) {
                    $callback($this->saida);
                    http_response_code(200);
                } else {
                    $this->erros = errosHttp\errosHttp::outputError($this->errosModelObject);
                    $callback($this->erros[1]);
                    http_response_code($this->erros[0]);
                }
                break;
            case "PATCH":
                $this->patch($parameters);
                $this->erros = errosHttp\errosHttp::outputError($this->errosModelObject);
                if (empty($this->erros[1]["errors"])) {
                    $callback($this->saida);
                    http_response_code(200);
                } else {
                    $this->erros = errosHttp\errosHttp::outputError($this->errosModelObject);
                    $callback($this->erros[1]);
                    http_response_code($this->erros[0]);
                }
                break;
            case "DELETE":
                $this->delete($parameters);
                $this->erros = errosHttp\errosHttp::outputError($this->errosModelObject);
                if (empty($this->erros[1]["errors"])) {
                    header("Entity: {$this->id}");
                    http_response_code(204);
                } else {
                    $this->erros = errosHttp\errosHttp::outputError($this->errosModelObject);
                    $callback($this->erros[1]);
                    http_response_code($this->erros[0]);
                }
                break;
        }
    }

    private function get($parameters)
    {
        $result = $this->{$filter}->checkFilterGet($parameters);
        $this->erros = errosHttp\errosHttp::outputError($this->errosModelObject);
        //primeiro verifica se nao contem erro na rota
        if (empty($this->erros[1]["errors"])) {
            //verifica se retorna resultado
            if ($result) {
                //verifica se a pesquisa e por id
                if ($result["id"] != null) {
                    $this->saida = $this->db->pesquisar(array(), array("id" => $result["id"]));
                } //se nao for entao entra aqui
                else {
                    //verifica se existe query parametros
                    if (!empty($result["query"])) {
                        $search = $result["query"]["search"] !== null || !empty($result["query"]["search"]) ? array("nome" => $result["query"]["search"]) : null;
                        $order = $result["query"]["order"] !== null || !empty($result["query"]["order"]) ? $result["query"]["order"] : null;
                        $offset = $result["query"]["offset"] !== null || !empty($result["query"]["offset"]) ? $result["query"]["offset"] : null;
                        $limit = $result["query"]["limit"] !== null || !empty($result["query"]["limit"]) ? $result["query"]["limit"] : null;
                        $page = $result["query"]["page"] !== null || !empty($result["query"]["page"]) ? $result["query"]["page"] : null;
                        $limitPage = paginator;
                        $this->saida = $this->db->pesquisar(array(), $search, $order, $limit, $offset, intval($page),
                            $limitPage);
                        if (!$this->saida) {
                            $GLOBALS["erros"] = true;
                            errosHttp\errosHttp::setaErro($this->errosModelObject, "400",
                                "Problem to retrieve objects from database");
                        }
                    } else {
                        //se nao busque por todos os resultados
                        $this->saida = $this->db->pesquisar(array(), "");
                    }
                }
            }
        }
    }

    private function post($parameters)
    {
        $result = $this->{$filter}->checkFilterPost($parameters);
        $this->erros = errosHttp\errosHttp::outputError($this->errosModelObject);
        //primeiro verifica se nao contem erro na rota
        if (empty($this->erros[1]["errors"])) {
            //envia o body request para enviar no banco
            $this->saida = $this->db->inserir($result);
            if (!$this->saida) {
                $GLOBALS["erros"] = true;
                errosHttp\errosHttp::setaErro($this->errosModelObject, "400", "Problem to insert values database");
            }
        }
    }

    private function put($parameters)
    {
        $result = $this->{$filter}->checkFilterPut($parameters);
        $this->erros = errosHttp\errosHttp::outputError($this->errosModelObject);
        //primeiro verifica se nao contem erro na rota
        if (empty($this->erros[1]["errors"])) {
            //envia o body request para enviar no banco
            $this->saida = $this->db->atualizar($result["body"], $result["id"]);
            if (!$this->saida) {
                $GLOBALS["erros"] = true;
                errosHttp\errosHttp::setaErro($this->errosModelObject, "400", "Problem to insert values database");
            }
        }
    }

    private function delete($parameters)
    {
        $result = $this->{$filter}->checkFilterDelete($parameters);
        $this->erros = errosHttp\errosHttp::outputError($this->errosModelObject);
        //primeiro verifica se nao contem erro na rota
        if (empty($this->erros[1]["errors"])) {
            //envia o body request para enviar no banco
            $this->saida = $this->db->deletar($result["id"]);
            $this->id = $result["id"];
            if (!$this->saida) {
                $GLOBALS["erros"] = true;
                errosHttp\errosHttp::setaErro($this->errosModelObject, "400", "Problem to insert values database");
            }
        }
    }

    private function patch($parameters)
    {
        $result = $this->{$filter}->checkFilterPatch($parameters);
        $this->erros = errosHttp\errosHttp::outputError($this->errosModelObject);
        //primeiro verifica se nao contem erro na rota
        if (empty($this->erros[1]["errors"])) {
            //envia o body request para enviar no banco
            $this->saida = $this->db->atualizarPatch($result["body"], $result["id"]);
            if (!$this->saida) {
                $GLOBALS["erros"] = true;
                errosHttp\errosHttp::setaErro($this->errosModelObject, "400", "Problem to insert values database");
            }
        }
    }

}