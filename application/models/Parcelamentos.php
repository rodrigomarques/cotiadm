<?php

class Application_Model_Parcelamentos extends Zend_Db_Table_Abstract
{

    protected $_name = "parcelamento";
    
   public function listarPorPagamento($idPagamento)
   {
       $select = $this->select()
                        ->where("pagamento_idpagamento = ?", $idPagamento);
       return $this->fetchAll($select)->toArray();
   }
   
   public function buscarPorChave($nparcela, $idpagamento)
   {
       $select = $this->select()
                        ->where("pagamento_idpagamento = ?", $idpagamento)
                        ->where("nparcela = ?", $nparcela)
                        ;
       return $this->fetchRow($select);
   }
   
   
}

