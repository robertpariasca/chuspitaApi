<?php

require_once '../data/Conexion.class.php';

class Oficina extends Conexion {

    private $idempresa;
    private $idoficina;
    private $desoficina;
    private $direccion;
    private $estado;


    public function getIdempresa(){
        return $this->idempresa;
    }

    public function getIdoficina(){
        return $this->idoficina;
    }

    public function getDesoficina(){
        return $this->desoficina;
    }

    public function getDireccion(){
        return $this->direccion;
    }

    public function getEstado(){
        return $this->estado;
    }

    public function setIdempresa($idempresa){
        $this->idempresa = $idempresa;
    }

    public function setIdoficina($idoficina){
        $this->idoficina = $idoficina;
    }
 
    public function setDesoficina($desoficina){
        $this->desoficina = $desoficina;
    }
    
    public function setDireccion($direccion){
        $this->direccion = $direccion;
    }
    
    public function setEstado($estado){
        $this->estado=$estado;
    }

    public function listar() {
       
        try {
            $sql = "
                    select 
                        idempresa,
                        idoficina,
                        desoficina,
                        direccion,
                        estado
                        
                    from 
                        se_oficina   
                    order by 
                        desoficina
                ";
            $sentencia = $this->dblink->prepare($sql);
            $sentencia->execute();
            $resultado = $sentencia->fetchAll(PDO::FETCH_ASSOC);
            return $resultado;
        } catch (Exception $exc) {
            throw $exc;
        }
    }  
    public function listarCombo() {
       
        try {
            $sql = "
                    select 
                        idoficina,
                        desoficina                        
                    from 
                        se_oficina
                    where
                        idempresa=:p_idempresa
                    order by 
                        desoficina
                ";
            $sentencia = $this->dblink->prepare($sql);
            $sentencia->bindParam(":p_idempresa",  $this->getIdempresa());
            $sentencia->execute();
            $resultado = $sentencia->fetchAll(PDO::FETCH_ASSOC);
            return $resultado;
        } catch (Exception $exc) {
            throw $exc;
        }
    }
}
