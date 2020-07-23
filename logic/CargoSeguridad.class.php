<?php

require_once '../data/Conexion.class.php';

class Cargo extends Conexion {

    private $idempresa;
    private $idoficina;
    private $idcargo;
    private $descripcion;

    public function getIdempresa(){
        return $this->idempresa;
    }

    public function getIdoficina(){
        return $this->idoficina;
    }

    public function getIdcargo(){
        return $this->idcargo;
    }

    public function getDescripcion(){
        return $this->descripcion;
    }

    public function setIdempresa($idempresa){
        $this->idempresa = $idempresa;
    }

    public function setIdoficina($idoficina){
        $this->idoficina = $idoficina;
    }

    public function setIdcargo($idcargo){
        $this->idcargo = $idcargo;
    }
 
    public function setDescripcion($descripcion){
        $this->descripcion = $descripcion;
    }

    public function listar() {
       
        try {
            $sql = "
                    select 
                        idempresa,
                        idoficina,
                        idcargo,
                        descripcion                        
                    from 
                        se_cargo   
                    order by 
                        descripcion
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
                        idcargo,
                        descripcion                        
                    from 
                        se_cargo 
                    where
                        idempresa=:p_idempresa and idoficina=:p_idoficina 
                    order by 
                        descripcion
                ";
            $sentencia = $this->dblink->prepare($sql);
            $sentencia->bindParam(":p_idempresa",  $this->getIdempresa());
            $sentencia->bindParam(":p_idoficina",  $this->getIdOficina());
            $sentencia->execute();
            $resultado = $sentencia->fetchAll(PDO::FETCH_ASSOC);
            return $resultado;
        } catch (Exception $exc) {
            throw $exc;
        }
    }   
}
