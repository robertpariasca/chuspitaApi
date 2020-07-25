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
            //return $this->getIdempresa();
        } catch (Exception $exc) {
            throw $exc;
        }
    }

    public function agregar($ip,$codlog) {
        $this->dblink->beginTransaction();
        
        try {
            //condiciones
            $sqlcon = "
                    select 
                            desoficina
                    from
                            se_oficina
                    where
                            desoficina = :p_desoficina
                    and 
                            idempresa = :p_idempresa;
                ";
//fin dondiciones
            $sentenciacon = $this->dblink->prepare($sqlcon);
            $sentenciacon->bindParam(":p_desoficina", $this->getDesoficina());
            $sentenciacon->bindParam(":p_idempresa", $this->getIdempresa());
//            $sentencia->bindParam(":p_tipo", $this->getTipo());
            $sentenciacon->execute();

            if ($sentenciacon->rowCount()) {

              $this->dblink->commit();
                    return "DU";
            }else{
                $sql = "select to_char(count(*)+1,'fm00') as nc from se_oficina where idempresa=:p_idempresa;";
                $sentencia = $this->dblink->prepare($sql);
                $sentencia->bindParam(":p_idempresa", $this->getIdempresa());
                $sentencia->execute();
                
                if ($sentencia->rowCount()){
                    $resultado = $sentencia->fetch(PDO::FETCH_ASSOC);
                    $nuevoCodigo = $resultado["nc"];
                    $this->setIdoficina($nuevoCodigo);
                    
                    /*Insertar en la tabla candidato*/
    //                $sql = "
    //                    insert into laboratorio(codigo_laboratorio,nombre,codigo_pais) 
    //                    values(:p_cod_lab, :p_nomb, :p_codigo_pais)
    //                    ";
                    
                    $sql = "select * from fn_registraroficina(                    
                                            :p_idempresa,
                                            :p_idoficina, 
                                            :p_desoficina,
                                            :p_direccion,
                                            :p_codigolog, 
                                            :p_ip
                                         );";
                    $sentencia = $this->dblink->prepare($sql);
                    // $sentencia->bindParam(":p_codigoCandidato", $this->getCodigoCandidato());
                    $sentencia->bindParam(":p_idempresa", $this->getIdempresa());
                    $sentencia->bindParam(":p_idoficina", $this->getIdoficina());
                    $sentencia->bindParam(":p_desoficina", $this->getDesoficina());
                    $sentencia->bindParam(":p_direccion", $this->getDireccion());
                    $sentencia->bindParam(":p_codigolog", $codlog);
                    $sentencia->bindParam(":p_ip", $ip);
                    $sentencia->execute();
                    /*Insertar en la tabla laboratorio*/
                    
                    /*Actualizar el correlativo*/
                    /*
                    $sql = "update correlativo set numero = numero + 1 
                            where tabla='se_empresa'";
                    $sentencia = $this->dblink->prepare($sql);
                    $sentencia->execute();
                    */
                    /*Actualizar el correlativo*/
                    
                    $this->dblink->commit();
                    return "EXITO";
                    
                }else{
                    throw new Exception("No se ha configurado el correlativo para la tabla empresa");
                }
       
            }

            
        } catch (Exception $exc) {
            $this->dblink->rollBack();
            throw $exc;
        }
        
        return false;
    }


}
