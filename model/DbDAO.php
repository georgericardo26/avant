<?php
include_once('DAO.php');
include_once('ADO/Transaction.php');
include_once('ADO/SQLInstruction.php');
include_once('ADO/SQLSelect.php');
include_once('ADO/Criteria.php');
include_once('ADO/Filter.php');
include_once('ADO/SQLInsert.php');
include_once('ADO/SQLUpdate.php');
include_once('ADO/SQLDelete.php');

/*
 * Classe com metodos para executar acoes no banco de dados
 * Table - cargos
 */

class ClientesDAO extends DAO
{

    private $result = array();

    public function pesquisar(
        $columns = array(),
        $where,
        $orderby = null,
        $limit = null,
        $offset = null,
        $page = null,
        $limitPage = null
    ) {
        try {
            //criando array que sera o resultado
            $resultado = array();
            //abre a transação
            Transaction::open();
            //cria uma instrucao de select
            $sql = new SQLSelect();
            //define a tabela
            $entity = "cliente";
            $sql->setEntity($entity);
            //define as colunas que serão retornadas
            if (empty($columns)) {
                $sql->addColumn("*");
            } else {
                foreach ($columns as $column) {
                    $sql->addColumn($column);
                }
            }
            //criando o criteria
            $criteria = new Criteria();
            $resultadoPaginacao = $this->paginacao($entity, $criteria, $limit, "", $page,
                ($limitPage === null) ? 5 : $limitPage);
            //metatags
            //se vier com condicional, nao mostrar total de paginas
            if ((!is_array($where) && empty($where))) {
                $resultado['meta']["totalPages"] = $this->totalPages($entity, $criteria, $limit, "", $page,
                    ($limitPage === null) ? 5 : $limitPage);
            }
            //metatags
            $resultado['meta']["offset"] = ($offset === null) ? 0 : $offset;
            $resultado['meta']["limit"] = ($limit === null) ? "false" : $limit;
            $resultado['meta']["totalCount"] = $this->count($entity, $criteria, $limit, "", $page);
            //se tiver condicional, offset e limit, tambem nao mostrar os links de paginacao
            if (!is_array($where) && empty($where) && is_null($offset) && is_null($limit)) {
                $resultado["links"] = $resultadoPaginacao[0];
            }
            //verifica outros query parametros
            if (is_array($where) && !empty($where)) {
                $key = array_keys($where);
                $value = array_values($where);
                $value = explode(" ", $value[0]);
                if (filter_var($value[0], FILTER_SANITIZE_NUMBER_INT)) {
                    $criteria->add(new Filter($key[0], "=", $value[0]));
                } else {
                    $criteria->add(new Filter($key[0], "ILIKE", "%{$value[0]}%"));
                }
            }
            //orderby
            if (!is_null($orderby)) {
                $criteria->setProperty("order", $orderby);
            } else {
                if (is_null($orderby)) {
                    //seta a ordem basica
                    $criteria->setProperty("order", "id");
                }
            }
            //limit e offset
            if (!is_null($limit)) {
                $criteria->setProperty("limit", $limit);
            }
            if (!is_null($offset)) {
                $criteria->add(new Filter("id", ">=", $offset));
                // $criteria->setProperty("offset", $offset);
            }
            //verifica se tem paginacao
            if (!is_null($page) && !empty($page)) {
                $criteria->add(new Filter("id", ">", $resultadoPaginacao[1][$page]));
                $criteria->setProperty("limit", $limitPage);
                //verifica quantidade de itens por pagina
                if (!is_null($limitPage)) {
                    $criteria->setProperty("limit", $limitPage);
                }
            }
            $sql->setCriteria($criteria);
            //executa a instrução SQL e pega o resultado
            $resultado['result'] = $sql->execute(Transaction::get());
            //adiciona a quantidade retornada neste request
            $resultado['meta']["requestCount"] = count($resultado['result']);
            //se vier somente um resultado, nao usar array
            if (count($resultado['result']) === 1) {
                $resultado['result'] = $resultado['result'][0];
                $resultado['meta']["requestCount"] = 1;
            }
            //aplica as alterações
            Transaction::commit();
            function walk_recursive(array $array, int $pos = 0)
            {
                foreach ($array as $k => $v) {

                    if (is_array($v)) {
                        $array[$k]["link"] = "http://".server."/api/customers/" . $array[$k]["id"];
                    }

                }
                return $array;
            }

            $arr['meta'] = $resultado['meta'];
            $arr["links"] = $resultado["links"];
           $arrWalk = walk_recursive($resultado['result']);

            $arr['result'] = walk_recursive($resultado['result']);
            //fecha a transação
            Transaction::close();
            if (!$resultado['result']) {
                return false;
            }
            return $arr;
        } catch (Exception $e) {
            echo $e->getMessage();
            //desfaz todas as operacoes realizadas na transacao
            Transaction::rollback();
            return false;
        }
    }

    public function inserir($data)
    {
        try {
            //abre a transação
            Transaction::open();
            //cria uma instrucao de insert
            $sql = new SQLInsert();
            //define a tabela
            $sql->setEntity("cliente");
            //obtem a quantidade de objetos a serem inseridos
            $qtdObject = count($data);
            //atribui o valor de cada coluna
            $sql->setRowData($data);
            //executa a instrucao SQL
            $sql->execute(Transaction::get());
            //ultimo id inserido
            $lastInsertId = Transaction::lastInsertId();
            //aplica as alteracoes e pega o resultado
            $resultado = Transaction::commit();
            //fecha a transação
            Transaction::close();
            //verifica se tudo ocorreu bem, se sim retorna true, senão false
            if (!$resultado) {
                return false;
            } else {
                //retorna os objetos criados
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
                //define as colunas que serão retornadas
                $sql->addColumn("*");
                //criando o criteria
                $criteria = new Criteria();
                $criteria->add(new Filter("id", "<=", $lastInsertId));
                $criteria->setProperty("order", "-id");
                $criteria->setProperty("limit", $qtdObject);
                $sql->setCriteria($criteria);
                //executa a instrução SQL e pega o resultado
                $resultado['result'] = $sql->execute(Transaction::get());
                function walk_recursive(array $array, int $pos = 0)
                {
                    foreach ($array as $k => $v) {
                        if (is_array($v)) {
                            $array[$k]["link"] = "http://".server."/api/customers/" . $array[$k]["id"];
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
            }
        } catch (Exception $e) {
            echo $e->getMessage();
            //desfaz todas as operacoes realizadas na transacao
            Transaction::rollback();
            return false;
        }
    }

    public function atualizar($values, $where)
    {
        try {
            //abre a transação
            Transaction::open();
            //cria uma instrucao de update
            $sql = new SQLUpdate();
            //define a tabela
            $sql->setEntity("cliente");
            //adicionando linhas que serão alteradas
            foreach ($values as $key => $value) {
                $sql->setRowData($key, $value);
            }
            //criando o criteria
            $criteria = new Criteria();
            $criteria->add(new Filter("id", "=", $where));
            $sql->setCriteria($criteria);
            //executa a instrucao SQL
            $sql->execute(Transaction::get());
            //aplica as alteracoes e pega o resultado
            $resultado = Transaction::commit();
            //fecha a transação
            Transaction::close();
            //verifica se tudo ocorreu bem, se sim retorna true, senão false
            if (!$resultado) {
                return false;
            } else {
                //retorna os objetos criados
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
                //define as colunas que serão retornadas
                $sql->addColumn("*");
                //criando o criteria
                $criteria = new Criteria();
                $criteria->add(new Filter("id", "=", $where));
                $sql->setCriteria($criteria);
                //executa a instrução SQL e pega o resultado
                $resultado['result'] = $sql->execute(Transaction::get());
                function walk_recursive(array $array, int $pos = 0)
                {
                    foreach ($array as $k => $v) {
                        if (is_array($v)) {
                            $array[$k]["link"] = "http://".server."/api/customers/" . $array[$k]["id"];
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
            }
        } catch (Exception $e) {
            // echo $e -> getMessage();
            //desfaz todas as operacoes realizadas na transacao
            Transaction::rollback();
            return false;
        }
    }

    public function atualizarPatch($values, $where)
    {
        try {
            //abre a transação
            Transaction::open();
            //cria uma instrucao de update
            $sql = new SQLUpdate();
            //define a tabela
            $sql->setEntity("cliente");
            //adicionando linhas que serão alteradas
            foreach ($values as $key => $value) {
                $sql->setRowData($key, $value);
            }
            //criando o criteria
            $criteria = new Criteria();
            $criteria->add(new Filter("id", "=", $where));
            $sql->setCriteria($criteria);
            //executa a instrucao SQL
            $sql->execute(Transaction::get());
            //aplica as alteracoes e pega o resultado
            $resultado = Transaction::commit();
            //fecha a transação
            Transaction::close();
            //verifica se tudo ocorreu bem, se sim retorna true, senão false
            if (!$resultado) {
                return false;
            }  else {
                //retorna os objetos criados
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
                //define as colunas que serão retornadas
                $sql->addColumn("*");
                //criando o criteria
                $criteria = new Criteria();
                $criteria->add(new Filter("id", "=", $where));
                $sql->setCriteria($criteria);
                //executa a instrução SQL e pega o resultado
                $resultado['result'] = $sql->execute(Transaction::get());
                function walk_recursive(array $array, int $pos = 0)
                {
                    foreach ($array as $k => $v) {
                        if (is_array($v)) {
                            $array[$k]["link"] = "http://".server."/api/customers/" . $array[$k]["id"];
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
            }
        } catch (Exception $e) {
            // echo $e -> getMessage();
            //desfaz todas as operacoes realizadas na transacao
            Transaction::rollback();
            return false;
        }
    }

    public function deletar($where)
    {
        try {
            //abre a transação
            Transaction::open();
            //cria uma instrucao de delete
            $sql = new SQLDelete();
            //define a tabela
            $sql->setEntity("cliente");
            //criando o criteria
            $criteria = new Criteria();
            $criteria->add(new Filter("id", "=", $where));
            $sql->setCriteria($criteria);
            //executa a instrucao SQL
            $sql->execute(Transaction::get());
            //aplica as alteracoes e pega o resultado
            $resultado = Transaction::commit();
            //fecha a transação
            Transaction::close();
            //verifica se tudo ocorreu bem, se sim retorna true, senão false
            if (!$resultado) {
                return false;
            } else {
                return true;
            }
        } catch (Exception $e) {
            // echo $e -> getMessage();
            //desfaz todas as operacoes realizadas na transacao
            Transaction::rollback();
            return false;
        }
    }

}

