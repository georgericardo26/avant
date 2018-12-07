<?php
include_once ('DAO.php');
include_once ('ADO/Transaction.php');
include_once ('ADO/SQLInstruction.php');
include_once ('ADO/SQLSelect.php');
include_once ('ADO/Criteria.php');
include_once ('ADO/Filter.php');
include_once ('ADO/SQLInsert.php');
include_once ('ADO/SQLUpdate.php');
include_once ('ADO/SQLDelete.php');


/*
 * Classe com metodos para executar acoes no banco de dados
 * Table - cargos
 */
class ClientesDAO extends DAO {
    
    public function pesquisar($columns = array(), $where, $orderby = null, $limit = null, $offset = null) {
        try{
            //criando array que sera o resultado
            $resultado = array();
            
            //abre a transação
            Transaction::open();
            
            //cria uma instrucao de select
          $sql = new SQLSelect();

            //define a tabela
            $entity = "cliente";
            $sql -> setEntity($entity);

           //define as colunas que serão retornadas
         if(empty($columns)){
             $sql -> addColumn("*");
         }else{
             foreach ($columns as $column){
                 $sql -> addColumn($column);
             }
         }

         //criando o criteria
         $criteria = new Criteria();



         if (is_array($where) && count($where) > 0) {

             $key = array_keys($where);
             $value = array_values ($where);
             $value = explode(" ",$value[0]);
             if(filter_var($value[0], FILTER_SANITIZE_NUMBER_INT)){

                 $criteria -> add(new Filter($key[0], "=", $value[0]));
             }
             else {
                 $criteria -> add(new Filter($key[0], "LIKE", "%{$value[0]}%"));
             }


         }

         //para retorno da paginacao
         if(isset($limit) && !empty($limit) && $limit > 0 && is_null($offset)){
             $resultado['paginacao'] = $this->paginacao($entity, $criteria, $limit);
         }

         //orderby
         if (!is_null($orderby)) {
             $criteria -> setProperty("order", $orderby);
         }

         //limit e offset
         if (!is_null($limit)) {
             $criteria -> setProperty("limit", $limit);
         }

         if (!is_null($offset)) {
             $criteria -> setProperty("offset", $offset);
         }

         $sql -> setCriteria($criteria);

         //executa a instrução SQL e pega o resultado
         $resultado['resultado'] = $sql -> execute(Transaction::get());

         //aplica as alterações
         Transaction::commit();
         //fecha a transação
         Transaction::close();

         return $resultado;

        } catch (Exception $e) {
            //registra o texto no log
          //  Util::writeLogXML(__METHOD__ .": " .$e -> getMessage());

           echo $e -> getMessage();
            //desfaz todas as operacoes realizadas na transacao
            Transaction::rollback();
            return false;
        }
    }

    public function inserir($data) {
        try{
            //abre a transação
            Transaction::open();
            
            //cria uma instrucao de insert
            $sql = new SQLInsert();
            //define a tabela
            $sql -> setEntity("cliente");

            //atribui o valor de cada coluna

            //fazer um foreach aqui
           // foreach ($data as $key => $value){

            $sql -> setRowData($data);
              //  $sql -> setRowData($key, $value);

          //  }

            //executa a instrucao SQL
            $sql -> execute(Transaction::get());

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
                return json_encode(array(
                    "status" => "sucess",
                    "message" => "dados inseridos com sucesso do id: ".$lastInsertId
                ));
            }
        } catch (Exception $e) {
            //registra o texto no log
          //  Util::writeLogXML(__METHOD__ .": " .$e -> getMessage());
            echo $e -> getMessage();
            //desfaz todas as operacoes realizadas na transacao
            Transaction::rollback();
            return false;
        }
    }
    
    public function atualizar($values, $where) {
        try{
            //abre a transação
            Transaction::open();
            
            //cria uma instrucao de update
            $sql = new SQLUpdate();
            //define a tabela
            $sql -> setEntity("cliente");
            
            //adicionando linhas que serão alteradas
            foreach ($values as $key => $value){
                $sql -> setRowData($key, $value);

            }

            //criando o criteria
            $criteria = new Criteria();
            
            $criteria -> add(new Filter("id", "=", $where));
            
            $sql -> setCriteria($criteria);
            
            //executa a instrucao SQL
            $sql -> execute(Transaction::get());
            
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
            //registra o texto no log
           // Util::writeLogXML(__METHOD__ .": " .$e -> getMessage());
            echo $e -> getMessage();
            
            //desfaz todas as operacoes realizadas na transacao
            Transaction::rollback();
            return false;
        }
    }

    public function atualizarPatch($values, $where) {
        try{
            //abre a transação
            Transaction::open();

            //cria uma instrucao de update
            $sql = new SQLUpdate();
            //define a tabela
            $sql -> setEntity("cliente");

            //adicionando linhas que serão alteradas
            foreach ($values as $key => $value){
                $sql -> setRowData($key, $value);

            }

            //criando o criteria
            $criteria = new Criteria();

            $criteria -> add(new Filter("id", "=", $where));

            $sql -> setCriteria($criteria);

            //executa a instrucao SQL
            $sql -> execute(Transaction::get());

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
            //registra o texto no log
            // Util::writeLogXML(__METHOD__ .": " .$e -> getMessage());
            echo $e -> getMessage();

            //desfaz todas as operacoes realizadas na transacao
            Transaction::rollback();
            return false;
        }
    }

    public function deletar($where) {
        try{
            //abre a transação
            Transaction::open();
            
            //cria uma instrucao de delete
            $sql = new SQLDelete();
            //define a tabela
            $sql -> setEntity("cliente");
            
            //criando o criteria
            $criteria = new Criteria();
            
            $criteria -> add(new Filter("id", "=", $where));
            
            $sql -> setCriteria($criteria);
            
            //executa a instrucao SQL
            $sql -> execute(Transaction::get());
            
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

            echo $e -> getMessage();
            //registra o texto no log
           // Util::writeLogXML(__METHOD__ .": " .$e -> getMessage());
            
            //desfaz todas as operacoes realizadas na transacao
            Transaction::rollback();
            return false;
        }
    }


}

