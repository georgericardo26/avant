<?php
/**
 * Created by PhpStorm.
 * User: georgericardo
 * Date: 01/12/2018
 * Time: 19:20
 */

namespace Router;
use IRequest\IRequest;

class Router
{

    private $request;
    public $uri = array();
    private $metodo;
    private $metodosSuportados = array(
        "GET",
        "POST",
        "PUT",
        "DELETE",
        "PATCH"
    );

    function __construct(IRequest $request)
    {
        $this->request = $request;
    }

    public function getMetodo(){

        $this->metodo = $this->request->requestMethod;

        //verifica se metodo existe
        if(!in_array($this->metodo, $this->metodosSuportados)){

            $this->metodoInvalido();
        }

        //seta headers da page
        $this->setMetodoHeader($this->metodo);

        //insere informacao no array que sera enviado para controller
        $this->uri["metodo"] = $this->metodo;

    }

    //seta os headers do request
    protected function setMetodoHeader($metodo){


        switch ($metodo){

            case "GET":
                $this->setHeaders("GET");

            break;

            case "POST":
                $this->setHeaders("POST");

            break;

            case "PUT":

                $this->setHeaders("PUT");

                break;

            case "PATCH":

                $this->setHeaders("PATCH");

                break;

            case "DELETE":

                $this->setHeaders("DELETE");

                break;

            default:

               $this->requestDefault();

        }

    }

    //verifica a integridade da url passada e gera o array
    function checkUri() {

       if($this->request->checkUrlValidate($this->request->httpHost, $this->request->requestUri)){

           parse_str($this->formatarRota($this->request->queryString), $saidaUri);
           if(empty($saidaUri)){
               $this->requestDefault();
           }

           $this->uri["uri"] = $saidaUri;

           //pega a url e consulta a propriedade query para saber se existe parametro page
           $url = parse_url("http://".$this->request->httpHost.$this->request->requestUri);
            $url = explode("=", urldecode($url["query"]));

            //verifica se existe uma chave chamada page
           if(!empty($url) && in_array("page", $url)){

               $this->uri["uri"]["page"] = $url[1];

           }

       }

       else {

           header("HTTP/1.1 400 Requisição invalida");
       }

       return false;

    }

    //verifica se tem body
    public function checkBody(){

        if($this->metodo === "POST" || $this->metodo === "PUT" || $this->metodo === "PATCH" || $this->metodo === "DELETE"){

                $this->uri["body"] = $this->request->getBody();
        }

    }


    //seta headers
    private function setHeaders($metodo){

        header("Access-Control-Allow-Origin: *");
        header('Content-Type: application/json');
        if($metodo === "GET"){
            header("Access-Control-Allow-Headers: access");
            header("Access-Control-Allow-Methods: GET");
            header("Access-Control-Allow-Credentials: true");

        }

        if($metodo === "POST" || $metodo === "PUT" || $metodo === "PATCH" || $metodo === "DELETE"){
            header("Content-Type: application/json; charset=UTF-8");
            header("Access-Control-Allow-Methods: POST");
            header("Access-Control-Max-Age: 3600");
            header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

         }


    }

    private function formatarRota($route)
    {
        //remove as barras a direita
        $result = rtrim($route, '/');

        if($result == ''){
            return '/';
        }

        return $result;
    }

    private function metodoInvalido(){

        header("{$this->request->serverProtocol} 405 Method Not Allowed");
    }

    private function requestDefault(){

        header("{$this->request->serverProtocol} 404 Not Found");
    }




    //destroi o objeto
    function __destruct()
    {
        // TODO: Implement __destruct() method.
    }

}