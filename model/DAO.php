<?php
/*
 * Classe para prove todos os metodos em comum nos DAO's
 */

abstract class DAO
{
    /*
     * Metodo para definir a paginacao e preencher as metatags
     */
    final protected function paginacao(
        $entity,
        $criteria,
        $limit,
        $extraBindValue = null,
        $page = null,
        $limitePage = null
    ) {
        //array que armazenara o resultado
        $paginacao = array();
        $arraySaida = array();
        //cria uma instrucao de select
        $sqlCount = new SQLSelect();
        //define a tabela      
        $sqlCount->setEntity($entity);
        //copiar o $criteria para uma nova variavel
        $criteriaCount = clone $criteria;
        //define a coluna COUNT
        if (is_null($criteriaCount->getProperty('group')) === false) {
            $sqlCount->addColumn("COUNT(DISTINCT " . $criteriaCount->getProperty('group') . ") AS total");
            //zerar groupby para nao da erro
            $criteriaCount->setProperty('group', null);
        } else {
            $sqlCount->addColumn("COUNT(*) AS total");
        }
        //seta o criteria
        $sqlCount->setCriteria($criteriaCount);
        // executa e pega o resultado
        $numeroLinhas = $sqlCount->execute(Transaction::get(), $extraBindValue);
        //verificar se retornou da forma correta
        if (is_array($numeroLinhas) && count($numeroLinhas) == 1) {
            //total de linhas
            $totallinhas = $numeroLinhas[0]['total'];
            //total de paginas
            $numeroPaginas = $totallinhas / $limitePage;
            $numeroPaginas = ceil($numeroPaginas);
            //passar o valor de cada pagina
            $posicao = 0;
            for ($i = 1; $i <= $numeroPaginas; $i++) {
                $paginacao['paginas'][$i] = $posicao;
                $posicao += $limitePage;
            }
        }
        echo $page;
        $arraySaida = array(
            array(
                "Current" => (is_null($page) || $page === 0) ? "false" : "http://" . server . "/api/customers?page=" . $page,
                "First" => "http://" . server . "/api/customers?page=" . array_keys($paginacao['paginas'])[0],
                "Next" => ($page == $numeroPaginas || is_null($page) || $page === 0) ? "false" : "http://" . server . "/api/customers?page=" . ($page + 1),
                "Previous" => ($page === 1 || is_null($page) || $page === 0) ? "false" : "http://" . server . "/api/customers?page=" . ($page - 1),
                "Last" => "http://" . server . "/api/customers?page=" . count(array_keys($paginacao['paginas']))
            ),
            $paginacao['paginas']
        );
        //retornar os valores
        return $arraySaida;
    }

    protected function totalPages(
        $entity,
        $criteria,
        $limit = null,
        $extraBindValue = null,
        $page = null,
        $limitePage = null
    ) {
        //array que armazenara o resultado
        $paginacao = array();
        //cria uma instrucao de select
        $sqlCount = new SQLSelect();
        //define a tabela
        $sqlCount->setEntity($entity);
        //copiar o $criteria para uma nova variavel
        $criteriaCount = clone $criteria;
        //define a coluna COUNT
        $sqlCount->addColumn("COUNT(*) AS total");
        //seta o criteria
        $sqlCount->setCriteria($criteriaCount);
        // executa e pega o resultado
        $numeroLinhas = $sqlCount->execute(Transaction::get(), $extraBindValue);
        //verificar se retornou da forma correta
        if (is_array($numeroLinhas) && count($numeroLinhas) == 1) {
            //total de linhas
            $paginacao['total'] = $numeroLinhas[0]['total'];
            //total de paginas
            $paginacao['numeroPaginas'] = ceil($paginacao['total'] / $limitePage);
        }
        //retornar os valores
        return $paginacao['numeroPaginas'];
    }

    protected function count($entity, $criteria, $limit, $extraBindValue = null, $page = null)
    {
        //array que armazenara o resultado
        $paginacao = array();
        //cria uma instrucao de select
        $sqlCount = new SQLSelect();
        //define a tabela
        $sqlCount->setEntity($entity);
        //copiar o $criteria para uma nova variavel
        $criteriaCount = clone $criteria;
        //define a coluna COUNT
        $sqlCount->addColumn("COUNT(*) AS total");
        //seta o criteria
        $sqlCount->setCriteria($criteriaCount);
        // executa e pega o resultado
        $numeroLinhas = $sqlCount->execute(Transaction::get(), $extraBindValue);
        //retornar os valores
        return $numeroLinhas[0]["total"];
    }

    protected function queryPostAndPut($entity, $criteria, $lastid, $limit)
    {
        try {
            //reabre a transação
            //criando array que sera o resultado
            $resultado = array();
            //abre a transação
            Transaction::open();
            //cria uma instrucao de select
            $sql = new SQLSelect();
            //define a tabela
            $entity = "cliente";
            $sql->setEntity($entity);
            $criteriaCount = clone $criteria;
            //define as colunas que serão retornadas
            $sql->addColumn("*");
            //criando o criteria
            $criteria->add(new Filter("id", "<=", $lastid));
            $criteria->setProperty("order", "-id");
            $criteria->setProperty("limit", $limit);
            $sql->setCriteria($criteria);
            //executa a instrução SQL e pega o resultado
            $resultado['result'] = $sql->execute(Transaction::get());
            function walk_recursive(array $array, int $pos = 0)
            {
                foreach ($array as $k => $v) {
                    if (is_array($v)) {
                        $array[$k]["link"] = "http://" . server . "/api/customers/" . $array[$k]["id"];
                    }
                }
                return $array;
            }

            //seta a propriedade do link para cada objeto criado
            $resultado['result'] = walk_recursive($resultado['result']);
            //verifica se retorna somente um objeto
            if (count($resultado['result']) === 1) {
                $resultado['result'] = $resultado['result'][0];
            }
            //aplica as alterações
            Transaction::commit();
            //fecha a transação
            Transaction::close();
            return $resultado['result'];
        } catch (\exception $e) {
            echo $e->getMessage();
        }
    }

    abstract public function pesquisar($columns, $where, $orderby = null, $limit = null, $offset = null);

    abstract public function inserir($values);

    abstract public function atualizar($values, $where);

    abstract public function deletar($where);
}