<?php
/**
 * Created by PhpStorm.
 * User: georgericardo
 * Date: 01/12/2018
 * Time: 16:36
 */
include_once('router/Request.php');
include_once('router/Router.php');
include_once('controller/indexControllerRequest.php');
include_once('model/DbDAO.php');
include_once('controller/errosHttp.php');
include_once('model/erros.php');
//insira o seu server http
define("server", "{$_SERVER['HTTP_HOST']}");
//define a quantidade de clientes por pagina em caso de paginacao
define('paginator', 5);

use model\erros\erros;
use controller\errosHttp\errosHttp;

class Application
{
    private $saida = "";

    protected function resgisterServices()
    {
        //inicia objeto para receber possiveis erros
        $errosModel = new erros\erros();
        $router = new \Router\Router(new \Request\Request(), $errosModel);
        $controller = new controller\indexControllerRequest(new ClientesDAO(), $errosModel);
        //verifica metodo
        $router->getMetodo();
        //verifica uri e pega os parametros
        $router->checkUri();
        //verifica se tem body
        $router->checkBody();
        $this->saida = errosHttp\errosHttp::outputError($errosModel);
        if (!empty($this->saida["errors"])) {
            echo json_encode($this->saida[1]);
            http_response_code($this->saida[0]);
        } else {
            //pega tudo
            $this->{$parametersUri} = $router->uri;
            //joga o metodo selecionado, uri e query parameters na controller
            $controller->getParameters($this->{$parametersUri}, function ($result) {
                echo json_encode($result);
            });
        }
    }

    public function launchApplication()
    {
        $this->resgisterServices();
    }
}

$request = new Application();
$request->launchApplication();