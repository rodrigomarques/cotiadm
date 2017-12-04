<?php
class Util_EnvioEmail {
    
    private $remtente;
    private $destinatario;
    private $assunto;
    private $mensagem;
    
    public function getRemtente() {
        return $this->remtente;
    }

    public function setRemtente($remtente) {
        $this->remtente = $remtente;
    }

    public function getDestinatario() {
        return $this->destinatario;
    }

    public function setDestinatario($destinatario) {
        $this->destinatario = $destinatario;
    }

    public function getAssunto() {
        return $this->assunto;
    }

    public function setAssunto($assunto) {
        $this->assunto = $assunto;
    }

    public function getMensagem() {
        return $this->mensagem;
    }

    public function setMensagem($mensagem) {
        $this->mensagem = $mensagem;
    }

    public function enviarMSG(){
        
        $email_headers = implode ( "\n" , array ( "From: " . $this->getRemtente() , "Reply-To: " . 
            $this->getRemtente(), "Subject: " . $this->getAssunto(), "Return-Path: " . $this->getRemtente(),
            "MIME-Version: 1.0","X-Priority: 3","Content-Type: text/html; charset=UTF-8" ) 
        );

	if (@mail ($this->getDestinatario(), $this->getAssunto(), nl2br($this->getMensagem()), $email_headers))
            return true;
        else
            return false;
    }
    
}

?>
