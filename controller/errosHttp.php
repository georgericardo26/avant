<?php
/**
 * Created by PhpStorm.
 * User: georgericardo
 * Date: 29/12/2018
 * Time: 17:38
 */

namespace controller\errosHttp\errosHttp;

use model\erros\erros\erros;

class errosHttp
{

    public static $objeto;

    public static function setaErro(erros $erros, string $statusCode, string $message)
    {
        $erros->setaErro($statusCode, $message);
    }

    //consulta se existe erro no array
    public static function consultaListaErros(erros $erros)
    {
        return $erros->currentError;
    }

    public static function outputError(erros $erros)
    {
        $listaErros = self::consultaListaErros($erros);
        $saidaErros = array();
        $listaSaida = '';
        $tiposaida = '';
        if (count($listaErros) > 1) {
            foreach ($listaErros["errors"] as $key => $val) {
                foreach ($key as $key2 => $val2) {
                    if (strpos($val2, "4")) {
                        $tiposaida = "400";
                    }
                }
            }
            if ($tiposaida === "400") {
                $saidaErros[0] = intval($tiposaida);
            } else {
                $saidaErros[0] = intval(intval($listaErros["errors"][0]["status"]));
            }
            $saidaErros[1] = $listaErros;
            return $saidaErros;
        } else {
            if (count($listaErros) === 1) {
                $saidaErros[0] = intval($listaErros["errors"][0]["status"]);
                $saidaErros[1] = $listaErros;
                return $saidaErros;
            } else {
                $saidaErros[0] = 500;
                $saidaErros[1] = $erros->setErrorDefault();
                return $saidaErros;
            }
        }
    }

}




