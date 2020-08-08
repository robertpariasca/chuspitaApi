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
                        c.idempresa,
                        e.desempresa,
                        c.idoficina,
                        o.desoficina,
                        c.idcargo,
                        c.descripcion                        
                    from 
                        se_cargo c
                    inner join
                        se_empresa e
                    on 
                        c.idempresa=e.idempresa
                    inner join
                        se_oficina o
                    on 
                        c.idoficina=o.idoficina and c.idempresa=o.idempresa
					group by
						c.idempresa, e.desempresa, c.idoficina, o.desoficina, c.idcargo
                    order by 
                        e.desempresa, o.desoficina, idcargo
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
            $sentencia->bindParam(":p_idoficina",  $this->getIdoficina());
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
                            descripcion
                    from
                            se_cargo
                    where
                            descripcion = :p_descripcion
                    and 
                            idempresa = :p_idempresa
                    and
                            idoficina = :p_idoficina;
                ";
//fin dondiciones
            $sentenciacon = $this->dblink->prepare($sqlcon);
            $sentenciacon->bindParam(":p_descripcion", $this->getDescripcion());
            $sentenciacon->bindParam(":p_idempresa", $this->getIdempresa());
            $sentenciacon->bindParam(":p_idoficina",  $this->getIdoficina());
//            $sentencia->bindParam(":p_tipo", $this->getTipo());
            $sentenciacon->execute();

            if ($sentenciacon->rowCount()) {

              $this->dblink->commit();
                    return "DU";
            }else{
                $sql = "select to_char(count(*)+1,'fm00') as nc from se_cargo where idempresa=:p_idempresa and idoficina=:p_idoficina;";
                $sentencia = $this->dblink->prepare($sql);
                $sentencia->bindParam(":p_idempresa", $this->getIdempresa());
                $sentencia->bindParam(":p_idoficina", $this->getIdoficina());
                $sentencia->execute();
                
                if ($sentencia->rowCount()){
                    $resultado = $sentencia->fetch(PDO::FETCH_ASSOC);
                    $nuevoCodigo = $resultado["nc"];
                    $this->setIdcargo($nuevoCodigo);
                    
                    /*Insertar en la tabla candidato*/
    //                $sql = "
    //                    insert into laboratorio(codigo_laboratorio,nombre,codigo_pais) 
    //                    values(:p_cod_lab, :p_nomb, :p_codigo_pais)
    //                    ";
                    
                    $sql = "select * from fn_registrarcargo(                    
                                            :p_idempresa,
                                            :p_idoficina, 
                                            :p_idcargo,
                                            :p_descripcion,
                                            :p_codigolog, 
                                            :p_ip
                                         );";
                    $sentencia = $this->dblink->prepare($sql);
                    // $sentencia->bindParam(":p_codigoCandidato", $this->getCodigoCandidato());
                    $sentencia->bindParam(":p_idempresa", $this->getIdempresa());
                    $sentencia->bindParam(":p_idoficina", $this->getIdoficina());
                    $sentencia->bindParam(":p_idcargo", $this->getIdcargo());
                    $sentencia->bindParam(":p_descripcion", $this->getDescripcion());
                    $sentencia->bindParam(":p_codigolog", $codlog);
                    $sentencia->bindParam(":p_ip", $ip);
                    $sentencia->execute();

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
            SELECT * from fn_eliminarcargo(
                :p_idempresa, 
                :p_idoficina,
                :p_idcargo,
                :p_codigolog, 
                :p_ip
            )
                ";
            $sentencia = $this->dblink->prepare($sql);
            $sentencia->bindParam(":p_idempresa",  $this->getIdempresa());
            $sentencia->bindParam(":p_idoficina",  $this->getIdoficina());
            $sentencia->bindParam(":p_idcargo",  $this->getIdcargo());
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
                            descripcion
                    from
                            se_cargo
                    where
                            descripcion = :p_descripcion
                    and 
                            idempresa = :p_idempresa
                    and
                            idoficina = :p_idoficina
                    and
                            idcargo <> :p_idcargo;
                ";
//fin dondiciones
            $sentenciacon = $this->dblink->prepare($sqlcon);
            $sentenciacon->bindParam(":p_descripcion", $this->getDescripcion());
            $sentenciacon->bindParam(":p_idempresa", $this->getIdempresa());
            $sentenciacon->bindParam(":p_idoficina",  $this->getIdoficina());
            $sentenciacon->bindParam(":p_idcargo",  $this->getIdcargo());
//            $sentencia->bindParam(":p_tipo", $this->getTipo());
            $sentenciacon->execute();

            if ($sentenciacon->rowCount()) {

              $this->dblink->commit();
                    return "DU";
            }else{

                    $sql = "select * from fn_editarcargo(                    
                                            :p_idempresa,
                                            :p_idoficina, 
                                            :p_idcargo,
                                            :p_descripcion,
                                            :p_codigolog, 
                                            :p_ip
                                         );";
                    $sentencia = $this->dblink->prepare($sql);
                    // $sentencia->bindParam(":p_codigoCandidato", $this->getCodigoCandidato());
                    $sentencia->bindParam(":p_idempresa", $this->getIdempresa());
                    $sentencia->bindParam(":p_idoficina", $this->getIdoficina());
                    $sentencia->bindParam(":p_idcargo", $this->getIdcargo());
                    $sentencia->bindParam(":p_descripcion", $this->getDescripcion());
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
