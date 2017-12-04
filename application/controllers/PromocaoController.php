<?php

class PromocaoController extends Zend_Controller_Action
{

    private $valores = array('A', 'B' , 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M'
        , 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'Y', 'X', 'Z');
    
    public function init()
    {
        if(Zend_Auth::getInstance()->hasIdentity() != 1){
            /*Se o usuario não estiver logado, mandar para index*/
            $this->_redirect('/index/');
        }
        
    }

    public function indexAction()
    {

        $promocaoDao = new Application_Model_Promocoes();
        $this->view->dados = $promocaoDao->listar();
        
        
    }
    
    public function novoAction()
    {
        
        $codigo = $this->valores[rand(0, count($this->valores) - 1)].
                $this->valores[rand(0, count($this->valores) - 1)].
                $this->valores[rand(0, count($this->valores) - 1)].
                $this->valores[rand(0, count($this->valores) - 1)].
                $this->valores[rand(0, count($this->valores) - 1)].
                date('mY');
        
        if($this->getRequest()->isPost())
        {
            $util = new Util_Utilitarios();
            $codigo = $this->getRequest()->getParam("codigo");
            $valor = $this->getRequest()->getParam("valor");
            $inicio = $this->getRequest()->getParam("dtinicio", "");
            $fim = $this->getRequest()->getParam("dtfim", "");
            $obs = $this->getRequest()->getParam("obs");
            
            $inicio = $util->converterDataEntrada($inicio);
            if($fim != "")
                $fim = $util->converterDataEntrada ($fim);
            else $fim = null;
            
            $data = array(
                'codigo' => $codigo,
                'valordesconto' => $valor,
                'datainicio' => $inicio,
                'datafim' => $fim,
                'status' => 1,
                'obs' => $obs
            );
            
            $promocaoDao = new Application_Model_Promocoes();
            if($promocaoDao->insert($data))
                $this->view->resp = "Promoção cadastrada com sucesso!";
            else
                $this->view->resp = "Promoção não pode ser cadastrada!";
            
        }
        
        $this->view->codigo = $codigo;
        $this->view->dtinicio = $dtInicio;
    }
    
    public function statusAction()
    {
        
        $id = $this->getRequest()->getParam("params", 0);
        $promocaoDao = new Application_Model_Promocoes();
        $promocao = $promocaoDao->buscarId($id);
        if($promocao != null)
        {
            $promocao["status"] = ($promocao["status"] == 1)?0:1;
            $promocaoDao->update(array('status' => $promocao["status"]), "idpromocao = " . $id);
            $this->view->resp = "Promoção editada com sucesso!";
        }
        
        $this->indexAction();
        $this->render("index");
    }
   
    public function verificarAction()
    {
        $this->_helper->layout->disableLayout();
       $this->_helper->viewRenderer->setNoRender(true);
        $codigo = $this->getRequest()->getParam("codigo", "");
        $promocaoDao = new Application_Model_Promocoes();
        $promocao = $promocaoDao->buscarCodigo(array("codigo" => $codigo));
        if(count($promocao) > 0)
        {
            echo "Desconto: " . $promocao[0]["valordesconto"];
            echo "<br />";
            echo "Obs: " . $promocao[0]["obs"];
            echo "<input type='hidden' name='txtpromocao' id='txtpromocao' value='".$promocao[0]["idpromocao"]."' />";
        }
        else
        {
            echo "Não existem promoções com o codigo informado ou a promoção esta indisponível!";
        }
    }
    
}


