<?php

class UsuarioController extends Zend_Controller_Action
{

    public function init()
    {
        /* Initialize action controller here */
    }

    public function logarAction()
    {   
        $this->_helper->layout()->disableLayout();
        
        if($this->getRequest()->isPost())
        {
            $usuarioDao = new Application_Model_Usuarios();
            $usuario = $this->getRequest()->getParam("login");
            $senha = $this->getRequest()->getParam("senha");
            if($usuario != ""){
                $dados = array("usuario" => $usuario, "senha" => md5($senha));
                if($usuarioDao->logar($dados)){
                    $perfilDao = new Application_Model_Perfil();
                    $usuario = $usuarioDao->buscarLogin($usuario);
                    if($usuario["ativo"] == 1){
                        $dados = $perfilDao->findById($usuario["perfil_idperfil"]);

                        $session = new Zend_Session_Namespace();
                        $session ->setExpirationSeconds( 86400 );
                        $session->perfil = $dados["perfil"];
                        $session->login = $usuario['usuario'];
                        $session->idusuario = $usuario["idusuario"];
                        $session->nome = $usuario["nome"];
                        
                        
                        
                        $_SESSION["logadosys"] = $usuario["idusuario"];
                        
                        return $this->_redirect("/Index/painel");
                    }
                }
            }
            
            $this->view->resp = "<span id='validate'>Usuario invalido!</span>";
        }
        
    }
    
    public function indexAction()
    {
        $perfilDao = new Application_Model_Perfil();
        $usuarioDao = new Application_Model_Usuarios();
        if($this->getRequest()->isPost())
        {
            
            $login = $this->getRequest()->getParam("login", "");
            $senha = $this->getRequest()->getParam("senha", "");
            $email = $this->getRequest()->getParam("email", "");
            $nome = $this->getRequest()->getParam("nome", "");
            $perfil = $this->getRequest()->getParam("perfil", 0);
            
            $idusuario = $this->getRequest()->getParam("id", 0);
            
            $dados = array(
                "nome" => $nome, 
                "usuario" => $login, 
                "senha" => md5($senha),
                "email" => $email,
                "perfil_idperfil" => $perfil
            );
            
            if($idusuario == 0)
            {
                if($usuarioDao->insert($dados))
                {
                    $pag = "http://www.cotiinformatica.com.br/admbeta/";
                    $envioEmail = new Util_EnvioEmail();
                    $envioEmail->setAssunto("Novo Usuário Cadastrado");
                    $envioEmail->setMensagem("<h3>COTI INFORMÁTICA</h3>"
                            . "<p>".$dados["nome"].", voce acaba de ser cadastrado na COTI INFORMÁTICA.
                                <br />
                                Para acessar o sistema entre com os dados abaixo:
                                <br /><br />
                                Usuário: ".$dados["usuario"]."<br />
                                Senha: ".$senha."<br />
                                </p>"
                            . "<a href='".$pag."'>Acesse o site</a> e confira as novidades");
                    $envioEmail->setRemtente("contato@cotiinformatica.com.br");

                    $envioEmail->setDestinatario($dados["email"]);
                    $envioEmail->enviarMSG();
                    $this->view->resp = "Usuário cadastrado com sucesso!";
                }
                else
                {
                    $this->view->resp = "Não pode cadastrar o usuário!";
                }
            }
            else
            {
                if($senha == "")
                    unset($dados["senha"]);
                
                if($usuarioDao->update($dados, "idusuario = " . $idusuario))
                {
                    $this->view->resp = "Usuário editado com sucesso!";
                    $pag = "http://www.cotiinformatica.com.br/admbeta/";
                    $envioEmail = new Util_EnvioEmail();
                    $envioEmail->setAssunto("Usuário Editado");
                    $envioEmail->setMensagem("<h3>COTI INFORMÁTICA</h3>"
                            . "<p>".$dados["nome"].", voce acaba de ser cadastrado na COTI INFORMÁTICA.
                                <br />
                                Para acessar o sistema entre com os dados abaixo:
                                <br /><br />
                                Usuário: ".$dados["usuario"]."<br />
                                Senha: ".$senha."<br />
                                </p>"
                            . "<a href='".$pag."'>Acesse o site</a> e confira as novidades");
                    $envioEmail->setRemtente("contato@cotiinformatica.com.br");

                    $envioEmail->setDestinatario($dados["email"]);
                    $envioEmail->enviarMSG();
                }
                else
                    $this->view->resp = "Usuário não pode ser editado!";
                
                $login = "";
                $perfil = 0;
            }
            $this->view->login = $login;
            $this->view->nome = $nome;
            $this->view->idperfil = $perfil;
            $this->view->email = $email;
            
            unset($this->view->idusuario);
        }
        
        $this->view->listaPerfil = $perfilDao->listar();
        $this->view->listaUsuario = $usuarioDao->listar();
    }

    public function modificarAction()
    {
        $usuarioDao = new Application_Model_Usuarios();
        $idUsuario = $this->getRequest()->getParam("params", 0);
        if($idUsuario == 0)
        {
            $this->view->resp = "Não existe o id informado!";
            $this->indexAction();
            $this->render('Index');
        }
        else
        {
            $user = $usuarioDao->buscarId($idUsuario);
            if($user != null){
                if($user["ativo"] == 1)
                    $user["ativo"] = 0;
                else
                    $user["ativo"] = 1;
                $usuarioDao->update($user, "idusuario = " . $user["idusuario"]);
               $this->view->resp = "Status do usuário modificado!";
            }else
                $this->view->resp = "Área não encontrada!";
            $this->indexAction();
            $this->render('Index');
        }
    }

    public function editarAction()
    {
        $usuarioDao = new Application_Model_Usuarios();
        $idUsuario = $this->getRequest()->getParam("params", 0);
        if($idUsuario == 0)
        { 
            $this->view->resp = "Não existe o id informado!";
            $this->indexAction();
            $this->render('Index');
        }
        else
        {
            $user = $usuarioDao->buscarId($idUsuario);
            if($user != null){
                $this->view->nome = $user["nome"];
                $this->view->login = $user["usuario"];
                $this->view->idperfil = $user["perfil_idperfil"];
                $this->view->idusuario = $user["idusuario"];
                $this->view->email = $user["email"];
            }else
                $this->view->resp = "Usuário não encontrado!";
            $this->indexAction();
            $this->render('Index');
        }
    }
    
    public function sairAction()
    {
        Zend_Session::start();
        $auth = Zend_Auth::getInstance();
        $auth->clearIdentity();
        Zend_Session::destroy();
        $this->_redirect("/index/index");

    }
    
    public function esqueceusenhaAction()
    {
        $this->_helper->layout()->disableLayout();
        if($this->getRequest()->isPost())
        {
            $login = $this->getRequest()->getParam("login", "");
            $email = $this->getRequest()->getParam("email", "");
            $usuarioDao = new Application_Model_Usuarios();
            $usuario = $usuarioDao->buscarLoginEmail($login, $email);
            
            if($usuario == null)
            {
                $this->view->resp = "Login / e-mail não encontrado!";
            }
            else
            {
                $novasenha = rand(10000,99999);
                $usuarioDao->update(array('senha' => md5($novasenha)), "idusuario = " . $usuario["idusuario"]);
                $pag = "http://www.cotiinformatica.com.br/admbeta/";
                $envioEmail = new Util_EnvioEmail();
                $envioEmail->setAssunto("Nova Senha Gerada ## COTI INFORMÁTICA");
                $envioEmail->setMensagem("<h3>COTI INFORMÁTICA</h3>"
                        . "<p>Porra o ".$usuario["nome"].", não lembra nem sua senha vai lembrar de codigo.
                            <br />
                            Vou te dar essa moral, acessa ai o sistema com as informações abaixo e nao me 
                            incomoda mais nao!
                            <br /><br />
                            Usuário: ".$usuario["usuario"]."<br />
                            Senha: ".$novasenha."<br />
                            </p>"
                        . "<a href='".$pag."'>Acesse o sistema ai</a>");
                $envioEmail->setRemtente("contato@cotiinformatica.com.br");

                $envioEmail->setDestinatario($email);
                $envioEmail->enviarMSG();
                $this->view->resp = "Uma nova senha foi enviada para o seu e-mail!";
            }
            
        }
    }
    
    public function alterarsenhaAction()
    {
        $senhaatual = $this->getRequest()->getParam("senhaatual", "");
        $novasenha = $this->getRequest()->getParam("novasenha", "");
        $confirmarsenha = $this->getRequest()->getParam("confirmarsenha", "");
        
    }
    
}

