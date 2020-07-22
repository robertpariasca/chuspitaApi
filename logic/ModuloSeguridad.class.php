<?php

require_once '../data/Conexion.class.php';

class Modulo extends Conexion {

    private $idmodulo;
    private $desmodulo;
    private $estado;

    public function getIdmodulo(){
        return $this->idmodulo;
    }

    public function getDesmodulo(){
        return $this->desmodulo;
    }

    public function getEstado(){
        return $this->estado;
    }

    public function setIdmodulo($idmodulo){
        $this->idmodulo = $idmodulo;
    }

    public function setDesmodulo($desmodulo){
        $this->desmodulo = $desmodulo;
    }

    public function setEstado($estado){
        $this->estado = $estado;
    }

    public function listar() {
       
        try {
            $sql = "
                    select 
                        idmodulo,
                        desmodulo,
                        estado
                    from 
                        se_modulo
                    order by 
                        desmodulo
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
