<?php

class Application_Model_Usuarios extends Zend_Db_Table_Abstract
{

    protected $_name = "usuario";
    
    public function listar()
    {
        $select = $this->select()
                    ->setIntegrityCheck(false)
                    ->from(array("u" => 'usuario'), array('u.usuario', 'u.senha', 'u.ativo', 'u.idusuario', 'u.email', 'u.nome',
                                'p.idperfil', 'p.perfil'))
                    ->join(array('p' => 'perfil'), 'p.idperfil = u.perfil_idperfil');
        return $this->fetchAll($select, "u.usuario ASC")->toArray();
    }
    
    public function listarProfessor()
    {
        $select = $this->select()
                    ->setIntegrityCheck(false)
                    ->from(array("u" => 'usuario'), array('u.usuario', 'u.senha', 'u.ativo', 'u.idusuario', 'u.email','u.nome',
                                'p.idperfil', 'p.perfil'))
                    ->join(array('p' => 'perfil'), 'p.idperfil = u.perfil_idperfil')
                    ->where("p.perfil = 'Professor' ")
                    ->where('u.ativo = 1');
        return $this->fetchAll($select, "u.usuario ASC")->toArray();
    }
    
    public function buscarId($id)
    {
        return $this->fetchRow("idusuario = " . $id)->toArray();
    }

    public function logar(array $dados){

            $authAdapter = new Zend_Auth_Adapter_DbTable();
            
            $authAdapter->setTableName('usuario')
                        ->setIdentityColumn('usuario')
                        ->setCredentialColumn('senha');

            $authAdapter->setIdentity($dados["usuario"]);
            $authAdapter->setCredential($dados['senha']);

            $auth = Zend_Auth::getInstance();
            $result = $auth->authenticate($authAdapter);

            if($result->isValid()){

                $user = $authAdapter->getResultRowObject();
                /*Criar sessao com os dados do usuzrio*/
                $auth->getStorage()->write($user);

                return true;
            }else{
                return false;
            }
        
    }
    
    public function buscarLogin($login){
        $select = $this->select()->where("usuario = ?", $login);
        return $this->fetchRow($select);
    }
    
    public function buscarLoginEmail($login = "", $email = ""){
        $select = $this->select()->where("usuario = ?", $login)
                            ->where("email = ? ", $email)
                            ->where('ativo = 1');
        return $this->fetchRow($select);
    }
    
}

