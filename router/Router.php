<?php
/**
 * Created by PhpStorm.
 * User: georgericardo
 * Date: 01/12/2018
 * Time: 19:20
 */

namespace Router;

include('../controller/errosHttp.php');

use controller\errosHttp\errosHttp;
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

    private $errosModelObject;

    function __construct(IRequest $request, $errosModel)
    {
        $this->request = $request;
        //seta erro false;
        $GLOBALS["error"] = false;
        $this->errosModelObject = $errosModel;
    }

    public function getMetodo()
    {
        $this->metodo = $this->request->requestMethod;
        //verifica se metodo existe
        if (!in_array($this->metodo, $this->metodosSuportados)) {
            $this->metodoInvalido();
        }
        //seta headers da page
        $this->setMetodoHeader($this->metodo);
        //insere informacao no array que sera enviado para controller
        $this->uri["metodo"] = $this->metodo;
    }

    //seta os headers do request
    protected function setMetodoHeader($metodo)
    {
        switch ($metodo) {
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
    function checkUri()
    {
        if ($this->request->checkUrlValidate($this->request->httpHost, $this->request->requestUri)) {
            $this->uri["uri"] = $this->formatarRota($this->request->redirectUrl);
            if (!$this->uri["uri"]) {
                $this->uriInvalida();
            } else {
                //pega a url e consulta a propriedade query para saber se existe parametro page
                $url = parse_url("http://" . $this->request->httpHost . $this->request->requestUri);
                //verifica se existe query parametros e joga no array
                parse_str($url["query"], $this->uri["query"]);
            }
        } else {
            errosHttp\errosHttp::setaErro($this->errosModelObject, "400", "invalid URL");
            $GLOBALS["error"] = true;
        }
    }

    //verifica se tem body
    public function checkBody()
    {
        $resultGetBody = '';
        if ($this->request->requestMethod == 'POST' || $this->request->requestMethod == 'PUT' || $this->request->requestMethod == 'PATCH') {
            $resultGetBody = $this->request->getBody();
            if ($resultGetBody === "error") {
                errosHttp\errosHttp::setaErro($this->errosModelObject, "405", "This method not accept body");
                $GLOBALS["error"] = true;
            } else {
                if (empty($resultGetBody)) {
                    errosHttp\errosHttp::setaErro($this->errosModelObject, "422", "request body is missing");
                    $GLOBALS["error"] = true;
                } else {
                    $this->uri["body"] = $resultGetBody;
                }
            }
        }
    }

    //seta headers
    private function setHeaders($metodo)
    {
        header("Access-Control-Allow-Origin: *");
        header('Content-Type: application/json');
        if ($metodo === "GET") {
            header("Access-Control-Allow-Headers: access");
            header("Access-Control-Allow-Methods: GET");
            header("Access-Control-Allow-Credentials: true");
        }
        if ($metodo === "POST" || $metodo === "PUT" || $metodo === "PATCH" || $metodo === "DELETE") {
            header("Content-Type: application/json; charset=UTF-8");
            header("Access-Control-Allow-Methods: POST");
            header("Access-Control-Max-Age: 3600");
            header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
        }
    }

    private function formatarRota($route)
    {
        //remove as barras a direita
        $result = rtrim($route, "/");
        $result = ltrim($result, "/");
        if ($result == '') {
            return false;
        }
        return explode("/", $result);
    }

    private function metodoInvalido()
    {
        header('Content-Type: application/json');
        errosHttp\errosHttp::setaErro($this->errosModelObject, "405",
            "{$this->request->serverProtocol} - Metodo Not Allowed");
        $GLOBALS["error"] = true;
    }

    private function requestDefault()
    {
        header('Content-Type: application/json');
        $GLOBALS["error"] = true;
        errosHttp\errosHttp::setaErro($this->errosModelObject, "400",
            "{$this->request->serverProtocol} The parameters wrong");
    }

    private function uriInvalida()
    {
        header('Content-Type: application/json');
        $GLOBALS["error"] = true;
        errosHttp\errosHttp::setaErro($this->errosModelObject, "406", "Not Acceptable Resource");
    }

    //destroi o objeto
    function __destruct()
    {
        // TODO: Implement __destruct() method.
    }

}