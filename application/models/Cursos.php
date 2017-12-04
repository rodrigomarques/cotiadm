<?php

class Application_Model_Cursos extends Zend_Db_Table_Abstract
{

    protected $_name = "curso";

    public function consultarCurso($dados = array())
    {
        
        $select = $this->select()
                    ->setIntegrityCheck(false)
                    ->from(array("c" => "curso"), array("c.curso", "c.ementa", "c.cargahoraria", "c.posicao", "c.resumo", "c.status", 
                        "a.area", "a.idarea", 'c.idcurso', 'c.incompany', 'c.tag', 'c.title' ,'c.txtseo', 'c.urlpagseguro'))
                    ->join(array("a" => "area"), "a.idarea = c.area_idarea");
        if(isset($dados["curso"]) && $dados["curso"] != "")
            $select = $select->where ("curso LIKE ?", array($dados["curso"] . "%"));
        if(isset($dados["status"]) && $dados["status"] != "")
            $select = $select->where ("status = ?", array($dados["status"]));
        if(isset($dados["area"]) && $dados["area"] != "")
            $select = $select->where ("idarea = ?", array($dados["area"]));
        if(isset($dados["nomearea"]) && $dados["nomearea"] != "")
            $select = $select->where ("area = ?", array($dados["nomearea"]));
        
        if(isset($dados["order"]) && $dados["order"] == 1)
        $select = $select->order("curso ASC")
                        ->order("posicao ASC");
        else
        $select = $select->order("posicao ASC")
                        ->order("curso ASC");
        return $this->fetchAll($select)->toArray();
    }
    
    public function buscarId($id)
    {
        
        $select = $this->select()
                    ->setIntegrityCheck(false)
                    ->from(array("c" => "curso"), array("c.curso", "c.ementa", "c.cargahoraria", "c.posicao", "c.resumo", "c.status", 
                        "a.area", "a.idarea", 'c.idcurso', 'c.incompany', 'c.tag', 'c.txtseo', 'c.title', 'c.urlpagseguro'))
                    ->join(array("a" => "area"), "a.idarea = c.area_idarea")
                    ->where("idcurso = ?", $id);
        
        $select = $select->order("posicao ASC")
                        ->order("curso ASC");
        return $this->fetchRow($select);
    }
    
    public function buscarTag($tag)
    {
        $select = $this->select()
                    ->setIntegrityCheck(false)
                    ->from(array("c" => "curso"), array("c.curso", "c.ementa", "c.cargahoraria", "c.posicao", "c.resumo", "c.status", 
                        "a.area", "a.idarea", 'c.idcurso', 'c.incompany', 'c.txtseo', 'c.title'))
                    ->join(array("a" => "area"), "a.idarea = c.area_idarea")
                    ->where("tag = ?", $tag);
        
        $select = $select->order("posicao ASC")
                        ->order("curso ASC");
        return $this->fetchRow($select);
    }
    
    public function consultarCursoSite($dados = array())
    {
        
        $select = $this->select()
                    ->setIntegrityCheck(false)
                    ->from(array("c" => "curso"), array("c.curso", "c.ementa", "c.cargahoraria", "c.posicao", "c.resumo", "c.status", 
                        "a.area", "a.idarea", 'c.idcurso', 'c.tag', 'c.txtseo', 'c.title', 'c.urlpagseguro'))
                    ->join(array("a" => "area"), "a.idarea = c.area_idarea");
        if(isset($dados["status"]) && $dados["status"] != "")
            $select = $select->where ("status = ?", array($dados["status"]));
        if(isset($dados["area"]) && $dados["area"] != "")
            $select = $select->where ("idarea = ?", array($dados["area"]));
        if(isset($dados["nomearea"]) && $dados["nomearea"] != "")
            $select = $select->where ("area = ?", array($dados["nomearea"]));
        
        $select->where('c.incompany = 0');
        
        $select = $select->order("idarea ASC")
                        ->order("curso ASC")
                        ->order("posicao ASC")
                        ->order("curso ASC");
        return $this->fetchAll($select)->toArray();
    }
    
}

