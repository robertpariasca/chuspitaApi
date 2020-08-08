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
                        idmodulo
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
                        idmodulo,
                        desmodulo
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
    public function agregar($ip,$codlog) {
        $this->dblink->beginTransaction();
        
        try {
            //condiciones
            $sqlcon = "
                    select 
                            desmodulo
                    from
                            se_modulo
                    where
                            desmodulo = :p_desmodulo;
                ";
//fin dondiciones
            $sentenciacon = $this->dblink->prepare($sqlcon);
            $sentenciacon->bindParam(":p_desmodulo", $this->getDesmodulo());
//            $sentencia->bindParam(":p_tipo", $this->getTipo());
            $sentenciacon->execute();

            if ($sentenciacon->rowCount()) {

              $this->dblink->commit();
                    return "DU";
            }else{
                $sql = "select * from f_generar_correlativo_2digitos('se_modulo') as nc";
                $sentencia = $this->dblink->prepare($sql);
                $sentencia->execute();
                
                if ($sentencia->rowCount()){
                    $resultado = $sentencia->fetch(PDO::FETCH_ASSOC);
                    $nuevoCodigo = $resultado["nc"];
                    $this->setIdmodulo($nuevoCodigo);
                    
                    /*Insertar en la tabla candidato*/
    //                $sql = "
    //                    insert into laboratorio(codigo_laboratorio,nombre,codigo_pais) 
    //                    values(:p_cod_lab, :p_nomb, :p_codigo_pais)
    //                    ";
                    
                    $sql = "select * from fn_registrarmodulo(                    
                                            :p_idmodulo,
                                            :p_desmodulo, 
                                            :p_codigolog, 
                                            :p_ip
                                         );";
                    $sentencia = $this->dblink->prepare($sql);
                    // $sentencia->bindParam(":p_codigoCandidato", $this->getCodigoCandidato());
                    $sentencia->bindParam(":p_idmodulo", $this->getIdmodulo());
                    $sentencia->bindParam(":p_desmodulo", $this->getDesmodulo());
                    $sentencia->bindParam(":p_codigolog", $codlog);
                    $sentencia->bindParam(":p_ip", $ip);
                    $sentencia->execute();
                    /*Insertar en la tabla laboratorio*/
                    
                    /*Actualizar el correlativo*/
                    $sql = "update correlativo set numero = numero + 1 
                            where tabla='se_modulo'";
                    $sentencia = $this->dblink->prepare($sql);
                    $sentencia->execute();
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
    public function eliminar($ip,$codlog) {
       
        try {
            $sql = "
            SELECT * from fn_eliminarmodulo(
                :p_idmodulo,
                :p_codigolog, 
                :p_ip
            )
                ";
            $sentencia = $this->dblink->prepare($sql);
            $sentencia->bindParam(":p_idmodulo",  $this->getIdmodulo());
            $sentencia->bindParam(":p_codigolog", $codlog);
            $sentencia->bindParam(":p_ip", $ip);
            $sentencia->execute();
            return "EXITO";
            //return $this->getIdempresa();
        } catch (Exception $exc) {
            throw $exc;
        }
    }
    public function actualizar($ip,$codlog) {
        $this->dblink->beginTransaction();
        
        try {
            //condiciones
            $sqlcon = "
                    select 
                            desmodulo
                    from
                            se_modulo
                    where
                            desmodulo = :p_desmodulo
                    and
                            idmodulo <> :p_idmodulo;
                ";
//fin dondiciones
            $sentenciacon = $this->dblink->prepare($sqlcon);
            $sentenciacon->bindParam(":p_desmodulo", $this->getDesmodulo());
            $sentenciacon->bindParam(":p_idmodulo", $this->getIdmodulo());
//            $sentencia->bindParam(":p_tipo", $this->getTipo());
            $sentenciacon->execute();

            if ($sentenciacon->rowCount()) {

              $this->dblink->commit();
                    return "DU";
            }else{
                    
                    $sql = "select * from fn_editarmodulo(                    
                                            :p_idmodulo,
                                            :p_desmodulo, 
                                            :p_codigolog, 
                                            :p_ip
                                         );";
                    $sentencia = $this->dblink->prepare($sql);
                    // $sentencia->bindParam(":p_codigoCandidato", $this->getCodigoCandidato());
                    $sentencia->bindParam(":p_idmodulo", $this->getIdmodulo());
                    $sentencia->bindParam(":p_desmodulo", $this->getDesmodulo());
                    $sentencia->bindParam(":p_codigolog", $codlog);
                    $sentencia->bindParam(":p_ip", $ip);
                    $sentencia->execute();

                    $this->dblink->commit();
                    return "EXITO";

       
            }

            
        } catch (Exception $exc) {
            $this->dblink->rollBack();
            throw $exc;
        }
        
        return false;
    }
}
