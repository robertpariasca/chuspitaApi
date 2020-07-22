<?php

require_once '../data/Conexion.class.php';

class Menu extends Conexion {

    private $idmodulo;
    private $codmenu;
    private $desmenu;

    public function getIdmodulo(){
        return $this->idmodulo;
    }

    public function getCodmenu(){
        return $this->codmenu;
    }

    public function getDesmenu(){
        return $this->desmenu;
    }

    public function setIdmodulo($idmodulo){
        $this->idmodulo = $idmodulo;
    }

    public function setCodmenu($codmenu){
        $this->codmenu = $codmenu;
    }

    public function setDesmenu($desmenu){
        $this->desmenu = $desmenu;
    }

    public function listar() {
       
        try {
            $sql = "
                    select 
                        idmodulo,
                        codmenu,
                        desmenu
                    from 
                        se_menu
                    order by 
                        desmenu
                ";
            $sentencia = $this->dblink->prepare($sql);
            $sentencia->execute();
            $resultado = $sentencia->fetchAll(PDO::FETCH_ASSOC);
            return $resultado;
        } catch (Exception $exc) {
            throw $exc;
        }
    }  
}
