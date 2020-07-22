<?php

require_once '../data/Conexion.class.php';

class Empresa extends Conexion {

    private $idempresa;
    private $desempresa;
    private $direccion;
    private $representantelegal;
    private $idtipodocidentidad;
    private $docidentidad;
    private $telefono;
    private $logo;
    private $estado;


    public function getIdempresa(){
        return $this->idempresa;
    }

    public function getDesempresa(){
        return $this->desempresa;
    }

    public function getDireccion(){
        return $this->direccion;
    }

    public function getRepresentantelegal(){
        return $this->representantelegal;
    }

    public function getIdtipodocidentidad(){
        return $this->idtipodocidentidad;
    }

    public function getDocidentidad(){
        return $this->docidentidad;
    }

    public function getTelefono(){
        return $this->telefono;
    }

    public function getLogo(){
        return $this->logo;
    }

    public function getEstado(){
        return $this->estado;
    }

    public function setIdempresa($idempresa){
        $this->idempresa = $idempresa;
    }
 
    public function setDesempresa($desempresa){
        $this->desempresa = $desempresa;
    }
    
    public function setDireccion($direccion){
        $this->direccion = $direccion;
    }
    
    public function setRepresentantelegal($representantelegal){
        $this->representantelegal = $representantelegal;
    }

    public function setIdtipodocidentidad($idtipodocidentidad){
        $this->idtipodocidentidad = $idtipodocidentidad;
    }

    public function setDocidentidad($docidentidad){
        $this->docidentidad = $docidentidad;
    }

    public function setTelefono($telefono){
        $this->telefono = $telefono;
    }

    public function setLogo($logo){
        $this->logo=$logo;
    }

    public function setEstado($estado){
        $this->estado=$estado;
    }

    public function listar() {
       
        try {
            $sql = "
                    select 
                        idempresa,
                        desempresa,
                        direccion,
                        representantelegal,
                        idtipodocidentidad,
                        docidentidad,
                        telefono,
                        logo,
                        estado
                        
                    from 
                        se_empresa   
                    order by 
                        desempresa
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
                        idempresa,
                        desempresa
                    from 
                        se_empresa   
                    order by 
                        desempresa
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
