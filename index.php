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




 class Application
 {
     protected function resgisterServices()
     {
         $router = new \Router\Router(new \Request\Request());
         $controller = new controller\indexControllerRequest(new ClientesDAO());


         //verifica metodo
        $router->getMetodo();

          //verifica uri e pega os parametros
          $router->checkUri();

          //verifica se tem body
          $router->checkBody();

          //pega tudo
         $this->{$parametersUri} = $router->uri;

           if($this->{$parametersUri} !== false) {

            //joga o metodo selecionado e uri na controller
           $controller->getParameters($this->{$parametersUri}, function ($result) {

               echo json_encode($result);
            });

      }



   }

     public function launchApplication(){

         $this->resgisterServices();

     }

 }

 $request = new Application();
 $request->launchApplication();