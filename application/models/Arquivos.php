<?php

class Application_Model_Arquivos extends Zend_Db_Table_Abstract
{

    protected $_name = "arquivo";

    public function listar($dados = array()){
        $select = $this->select()
                        ->setIntegrityCheck(false)
                        ->from(array('a' => 'arquivo'))
                        ->joinLeft(array('c' => 'curso'), 'a.curso_idcurso = c.idcurso');
         return $this->fetchAll($select)->toArray();
    }
    
}

