<?php

class Application_Model_Contatos extends Zend_Db_Table_Abstract
{

    protected $_name = "contatosite";
    
    public function buscarInteresse(array $params)
    {
        $select = $this->select()
                    ->setIntegrityCheck(false)
                    ->distinct()
                    ->from(array('cs' => 'contatosite'), array('cs.idcontatosite', 'cs.nome', 'cs.email', 
                        'cs.telefone', 'cs.turno', 'cs.dias', 'i.curso_idcurso', 'i.idinteresse','c.curso',
                        'statusinteresse' => 'i.status'))
                    ->joinLeft(array('i' => 'interesse'), "i.contatosite_idcontatosite = cs.idcontatosite")
                    ->joinLeft(array('c' => 'curso'), "i.curso_idcurso = c.idcurso")
                    ;
        
        if(isset($params['aluno']) && $params['aluno'] != "%")
            $select->where ('nome LIKE ?', $params['aluno']);
        
        if(isset($params['email']) && $params['email'] != "%")
            $select->where ('email LIKE ?', $params['email']);
        
        if(isset($params['turno']) && $params['turno'] != "")
            $select->where ('turno = ?', $params['turno']);
        
        if(isset($params['curso']) && $params['curso'] != "")
            $select->where ('curso_idcurso = ?', $params['curso']);
        
        if(isset($params['status']) && $params['status'] != "")
            $select->where ('i.status = ?', $params['status']);
        
        if(isset($params['dias']) && $params['dias'] != ""){
            foreach($params['dias'] as $d)
                $select->where('dias LIKE ?', '%'.$d.'%');
        }
        
        $select = $select
                        ->order("idcontatosite DESC");
        
        return $this->fetchAll($select)->toArray();
    }
    
    public function listarPorId($id){
        return $this->fetchRow("idcontatosite = " . $id);
    }

    public function listarPorEmail($email, $idcurso){
        $select = $this->select()
                    ->setIntegrityCheck(false)
                    ->distinct()
                    ->from(array('cs' => 'contatosite'), array('cs.idcontatosite', 'cs.nome', 'cs.email', 
                        'cs.telefone', 'cs.turno', 'cs.dias', 'i.curso_idcurso', 'i.idinteresse','c.curso',
                        'statusinteresse' => 'i.status'))
                    ->joinLeft(array('i' => 'interesse'), "i.contatosite_idcontatosite = cs.idcontatosite")
                    ->joinLeft(array('c' => 'curso'), "i.curso_idcurso = c.idcurso")
                    ;
        
        
        $select->where ('email = ?', $email);
        
        $select->where ('curso_idcurso = ?', $idcurso);

        return $this->fetchAll($select)->toArray();
    }
}

