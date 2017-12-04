<?php

class IndexController extends Zend_Controller_Action
{

    public function init()
    {
        /* Initialize action controller here */
    }

    public function indexAction()
    {
        return $this->_redirect("/Usuario/logar");
    }

    public function painelAction()
    {
        $session = new Zend_Session_Namespace();
        $this->view->session = $session;
    }
    
    public function alterarsenhaAction()
    {
        $this->_helper->layout->disableLayout();
        $id = $this->getRequest()->getParam("id", "");
        $novasenha = $this->getRequest()->getParam("novasenha", "");
        if($novasenha == "")
        {
            echo "Nova senha não pode ser vazio!";
        }
        else
        {
            $usuarioDao = new Application_Model_Usuarios();
            $usuario = $usuarioDao->buscarId($id);
            if($usuario == null)
            {
                echo "Usuário não existe!";
            }
            else
            {
                $usuarioDao->update(array('senha' => md5($novasenha)), "idusuario = " . $id);
                echo "Senha alterada com sucesso!";
            }
        }
    }

}

