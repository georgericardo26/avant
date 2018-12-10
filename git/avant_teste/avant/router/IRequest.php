<?php
/**
 * Created by PhpStorm.
 * User: georgericardo
 * Date: 01/12/2018
 * Time: 16:44
 */

namespace IRequest;


interface IRequest
{

    public function getBody();

    public function checkUrlValidate($http, $uri);

}