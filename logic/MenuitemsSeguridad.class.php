<?php

require_once '../data/Conexion.class.php';

class Menuitems extends Conexion {

    private $idmodulo;
    private $codmenu;
    private $codmenuitem;
    private $desmenuitem;
    private $tipoactividad;

    public function getIdmodulo(){
        return $this->idmodulo;
    }

    public function getCodmenu(){
        return $this->codmenu;
    }

    public function getCodmenuitem(){
        return $this->codmenuitem;
    }

    public function getDesmenuitem(){
        return $this->desmenuitem;
    }

    public function getTipoactividad(){
        return $this->tipoactividad;
    }

    public function setIdmodulo($idmodulo){
        $this->idmodulo = $idmodulo;
    }

    public function setCodmenu($codmenu){
        $this->codmenu = $codmenu;
    }
    
    public function setCodmenuitem($codmenuitem){
        $this->codmenuitem = $codmenuitem;
    }

    public function setDesmenuitem($desmenuitem){
        $this->desmenuitem = $desmenuitem;
    }

    public function setTipoactividad($tipoactividad){
        $this->tipoactividad = $tipoactividad;
    }

    public function listar() {
       
        try {
            $sql = "
                    select 
                        idmodulo,
                        codmenu,
                        codmenuitem,
                        desmenuitem,
                        tipoactividad
                    from 
                        se_menuitems
                    order by 
                        desmenuitem
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
