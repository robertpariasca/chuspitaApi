<?php

require_once '../data/Conexion.class.php';

class Multitabla extends Conexion {

    private $id;
    private $nombrecampo;
    private $tipocampo;
    private $valorcampo;

    private $email;
    private $clave;

    public function getId(){
        return $this->id;
    }

    public function getNombrecampo(){
        return $this->nombrecampo;
    }

    public function getTipocampo(){
        return $this->tipocampo;
    }

    public function getValorcampo(){
        return $this->valorcampo;
    }

    public function setId($id){
        $this->id = $id;
    }

    public function setNombreCampo($nombrecampo){
        $this->nombrecampo = $nombrecampo;
    }

    public function setTipocampo($tipocampo){
        $this->tipocampo = $tipocampo;
    }

    public function setValorcampo($valorcampo){
        $this->valorcampo = $valorcampo;
    }

    public function listar() {
       
        try {
            $sql = "
                    select 
                        nombre_campo,
                        tipo_campo,
                        valor_campo
                    from 
                        multitabla
                    where
                        nombre_campo=:p_nombrecampo
                    order by 
                        nombre_campo

                ";
            $sentencia = $this->dblink->prepare($sql);
            $sentencia->bindParam(":p_nombrecampo",  $this->getNombrecampo());
            $sentencia->execute();
            $resultado = $sentencia->fetchAll(PDO::FETCH_ASSOC);
            return $resultado;
        } catch (Exception $exc) {
            throw $exc;
        }
    } 
       
}
