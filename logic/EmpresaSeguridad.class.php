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
                        idempresa
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
    public function agregar($ip,$codlog) {
        $this->dblink->beginTransaction();
        
        try {
            //condiciones
            $sqlcon = "
                    select 
                            desempresa
                    from
                            se_empresa
                    where
                            desempresa = :p_desempresa;
                ";
//fin dondiciones
            $sentenciacon = $this->dblink->prepare($sqlcon);
            $sentenciacon->bindParam(":p_desempresa", $this->getDesempresa());
//            $sentencia->bindParam(":p_tipo", $this->getTipo());
            $sentenciacon->execute();

            if ($sentenciacon->rowCount()) {

              $this->dblink->commit();
                    return "DU";
            }else{
                $sql = "select * from f_generar_correlativo_3digitos('se_empresa') as nc";
                $sentencia = $this->dblink->prepare($sql);
                $sentencia->execute();
                
                if ($sentencia->rowCount()){
                    $resultado = $sentencia->fetch(PDO::FETCH_ASSOC);
                    $nuevoCodigo = $resultado["nc"];
                    $this->setIdempresa($nuevoCodigo);
                    
                    /*Insertar en la tabla candidato*/
    //                $sql = "
    //                    insert into laboratorio(codigo_laboratorio,nombre,codigo_pais) 
    //                    values(:p_cod_lab, :p_nomb, :p_codigo_pais)
    //                    ";
                    
                    $sql = "select * from fn_registrarempresa(                    
                                            :p_idempresa,
                                            :p_desempresa, 
                                            :p_direccion,
                                            :p_representantelegal, 
                                            :p_idtipodocidentidad, 
                                            :p_docidentidad, 
                                            :p_telefono, 
                                            :p_logo, 
                                            :p_codigolog, 
                                            :p_ip
                                         );";
                    $sentencia = $this->dblink->prepare($sql);
                    // $sentencia->bindParam(":p_codigoCandidato", $this->getCodigoCandidato());
                    $sentencia->bindParam(":p_idempresa", $this->getIdempresa());
                    $sentencia->bindParam(":p_desempresa", $this->getDesempresa());
                    $sentencia->bindParam(":p_direccion", $this->getDireccion());
                    $sentencia->bindParam(":p_representantelegal", $this->getRepresentantelegal());
                    $sentencia->bindParam(":p_idtipodocidentidad", $this->getIdtipodocidentidad());
                    $sentencia->bindParam(":p_docidentidad", $this->getDocidentidad());
                    $sentencia->bindParam(":p_telefono", $this->getTelefono());
                    $sentencia->bindParam(":p_logo", $this->getLogo());
                    $sentencia->bindParam(":p_codigolog", $codlog);
                    $sentencia->bindParam(":p_ip", $ip);
                    $sentencia->execute();
                    /*Insertar en la tabla laboratorio*/
                    
                    /*Actualizar el correlativo*/
                    $sql = "update correlativo set numero = numero + 1 
                            where tabla='se_empresa'";
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
            SELECT * from fn_eliminarempresa(
                :p_idempresa, 
                :p_codigolog, 
                :p_ip
            );";
            $sentencia = $this->dblink->prepare($sql);
            $sentencia->bindParam(":p_idempresa", $this->getIdempresa());
            $sentencia->bindParam(":p_codigolog", $codlog);
            $sentencia->bindParam(":p_ip", $ip);
            $sentencia->execute();
            return "EXITO";
        } catch (Exception $exc) {
            throw $exc;
        }
    }
    public function actualizar($ip,$codlog) {
        $this->dblink->beginTransaction();
        
        try {
            $sqlcon = "
            select 
                    desempresa
            from
                    se_empresa
            where
                    desempresa = :p_desempresa
            and
                    idempresa <> :p_idempresa;
        ";
//fin dondiciones
    $sentenciacon = $this->dblink->prepare($sqlcon);
    $sentenciacon->bindParam(":p_desempresa", $this->getDesempresa());
    $sentenciacon->bindParam(":p_idempresa", $this->getDesempresa());
//            $sentencia->bindParam(":p_tipo", $this->getTipo());
    $sentenciacon->execute();

    if ($sentenciacon->rowCount()) {

      $this->dblink->commit();
            return "DU";
    }else{
                    $sql = "select * from fn_editarempresa(                    
                                            :p_idempresa,
                                            :p_desempresa, 
                                            :p_direccion,
                                            :p_representantelegal, 
                                            :p_idtipodocidentidad, 
                                            :p_docidentidad, 
                                            :p_telefono, 
                                            :p_logo, 
                                            :p_codigolog, 
                                            :p_ip
                                         );";
                    $sentencia = $this->dblink->prepare($sql);
                    // $sentencia->bindParam(":p_codigoCandidato", $this->getCodigoCandidato());
                    $sentencia->bindParam(":p_idempresa", $this->getIdempresa());
                    $sentencia->bindParam(":p_desempresa", $this->getDesempresa());
                    $sentencia->bindParam(":p_direccion", $this->getDireccion());
                    $sentencia->bindParam(":p_representantelegal", $this->getRepresentantelegal());
                    $sentencia->bindParam(":p_idtipodocidentidad", $this->getIdtipodocidentidad());
                    $sentencia->bindParam(":p_docidentidad", $this->getDocidentidad());
                    $sentencia->bindParam(":p_telefono", $this->getTelefono());
                    $sentencia->bindParam(":p_logo", $this->getLogo());
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
