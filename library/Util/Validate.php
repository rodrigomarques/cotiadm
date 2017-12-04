<?php

class Util_Validate {
    
    public function validarTexto($texto){
        return preg_match("/^[A-Za-z á-úÁ-ÚâÂêÊãõçÇ]{3,50}$/", $texto);
    }
    
    public function validarNumero($texto){
        return preg_match("/^[0-9]+$/", $texto);
    }
    
    public function validarEmail($texto){
        return preg_match("/^[a-z_.0-9-]+@[a-z_.0-9-]+\.[a-z]{2,3}$/", $texto);
    }
    
    public function validarData($texto){
        return preg_match("/^[A-Za-z á-úÁ-ÚâÂêÊãõçÇ]{3,50}$/", $texto);
    }
    
    public function validarCpf($texto){
        return preg_match("/^[0-9]{11}$/", $texto);
    }
    
}

?>
