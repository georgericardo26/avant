<?php

/*
 * Classe para prove todos os metodos em comum nos DAO's
 */
abstract class DAO {
    /*
     * Metodo para definir a paginacao
     */
    final protected function paginacao($entity, $criteria, $limit, $extraBindValue = null){
        //array que armazenara o resultado
        $paginacao = array();
        
        //cria uma instrucao de select
        $sqlCount = new SQLSelect();
        //define a tabela      
        $sqlCount -> setEntity($entity);
        
        //copiar o $criteria para uma nova variavel
        $criteriaCount = clone $criteria;
        
        //define a coluna COUNT
        if(is_null($criteriaCount -> getProperty('group')) === false){
            $sqlCount -> addColumn("COUNT(DISTINCT " .$criteriaCount -> getProperty('group') .") AS total");
            
            //zerar groupby para nao da erro
            $criteriaCount -> setProperty('group', NULL);
        }else{
            $sqlCount -> addColumn("COUNT(*) AS total");
        }
        
        //seta o criteria
        $sqlCount -> setCriteria($criteriaCount);
        
        // executa e pega o resultado
        $numeroLinhas = $sqlCount -> execute(Transaction::get(), $extraBindValue);

        //verificar se retornou da forma correta
        if(is_array($numeroLinhas) && count($numeroLinhas) == 1){
            //total de linhas
            $paginacao['total'] = $numeroLinhas[0]['total'];
            
            //total de paginas
            $paginacao['numeroPaginas'] = ceil($paginacao['total'] / $limit);
            
            //passar o valor de cada pagina
            $posicao = 0;
            for ($i = 1; $i <= $paginacao['numeroPaginas']; $i++) {
                $paginacao['paginas'][$i] = $posicao;
                $posicao += $limit;
            }
        }
        
        //retornar os valores
        return $paginacao;
    }

    abstract public function pesquisar($columns, $where, $orderby = null, $limit = null, $offset = null);
    
    abstract public function inserir($values);
    
    abstract public function atualizar($values, $where);
    
    abstract public function deletar($where);
}