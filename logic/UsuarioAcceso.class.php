<?php

require_once '../data/Conexion.class.php';

class UsuarioAcceso extends Conexion {
    
    //private $Cuenta;
    //usuario_acceso

    private $codusuario;
    private $Idempresa;
    private $Idoficina;
    private $Idcargo;


    public function getCodusuario() {
        return $this->codusuario;
    }

    public function getIdempresa(){
        return $this->Idempresa;
    }

    public function getIdoficina(){
        return $this->Idoficina;
    }

    public function getIdcargo(){
        return $this->Idcargo;
    }

    public function setCodusuario($codusuario) {
        $this->codusuario = $codusuario;
    }

    public function setIdempresa($Idempresa){
        $this->Idempresa = $Idempresa;
    }

    public function setIdoficina($Idoficina){
        $this->Idoficina = $Idoficina;
    }

    public function setIdcargo($Idcargo){
        $this->Idcargo = $Idcargo;
    }

    public function listarEmpresa() {
       
        try {
            $sql = "
                    select 
                        u.codusuario,
                        a.idempresa,
						e.desempresa
                    from 
                        se_usuario u
                    inner join
                        se_usuario_acceso a
                    on
                        u.codusuario=a.codusuario
					inner join
						se_empresa e
					on
						a.idempresa = e.idempresa
                    where
						u.codusuario=:p_codusuario;
                ";
            $sentencia = $this->dblink->prepare($sql);
            $sentencia->bindParam(":p_codusuario", $this->getCodusuario());
            $sentencia->execute();
            $resultado = $sentencia->fetchAll(PDO::FETCH_ASSOC);
            return $resultado;
        } catch (Exception $exc) {
            throw $exc;
        }
    }

    public function listarOficina() {
       
        try {
            $sql = "
                     select 
                        a.idoficina,
						o.desoficina
                    from 
                        se_usuario u
                    inner join
                        se_usuario_acceso a
                    on
                        u.codusuario=a.codusuario
					inner join
						se_oficina o
					on
                        a.idempresa = o.idempresa and a.idoficina = o.idoficina
                    where
                        u.codusuario=:p_codusuario
                    and
                        a.idempresa=:p_idempresa;
                ";
            $sentencia = $this->dblink->prepare($sql);
            $sentencia->bindParam(":p_codusuario", $this->getCodusuario());
            $sentencia->bindParam(":p_idempresa", $this->getIdempresa());
            $sentencia->execute();
            $resultado = $sentencia->fetchAll(PDO::FETCH_ASSOC);
            return $resultado;
        } catch (Exception $exc) {
            throw $exc;
        }
    }

    public function listarCargo() {
       
        try {
            $sql = "
                     select 
                        a.idcargo,
						o.descripcion
                    from 
                        se_usuario u
                    inner join
                        se_usuario_acceso a
                    on
                        u.codusuario=a.codusuario
					inner join
						se_cargo o
					on
						a.idempresa = o.idempresa and a.idoficina = o.idoficina and a.idcargo = o.idcargo
                    where
                        u.codusuario=:p_codusuario
                    and
                        a.idempresa=:p_idempresa
                    and
                        a.idoficina=:p_idoficina;
                ";
            $sentencia = $this->dblink->prepare($sql);
            $sentencia->bindParam(":p_codusuario", $this->getCodusuario());
            $sentencia->bindParam(":p_idempresa", $this->getIdempresa());
            $sentencia->bindParam(":p_idoficina", $this->getIdoficina());
            $sentencia->execute();
            $resultado = $sentencia->fetchAll(PDO::FETCH_ASSOC);
            return $resultado;
        } catch (Exception $exc) {
            throw $exc;
        }
    }

    public function listar() {
       
        try {
            $sql = "
                    select 
                        u.codusuario,
                        a.idempresa,
						e.desempresa,
                        a.idoficina,
						o.desoficina,
                        a.idcargo,
						c.descripcion
                    from 
                        se_usuario u
                    inner join
                        se_usuario_acceso a
                    on
                        u.codusuario=a.codusuario
                    inner join
                        se_cargo c
                    on
                        a.idempresa = c.idempresa and a.idoficina = c.idoficina and a.idcargo=c.idcargo
					inner join
						se_oficina o
					on
						a.idempresa = o.idempresa and a.idoficina = o.idoficina
					inner join
						se_empresa e
					on
						a.idempresa = e.idempresa
                    where
						u.codusuario=:p_codusuario;
                ";
            $sentencia = $this->dblink->prepare($sql);
            $sentencia->bindParam(":p_codusuario", $this->getCodusuario());
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
                    
                    $sql = "select * from fn_registrarusuarioacceso(                    
                                            :p_codusuario,
                                            :p_idempresa, 
                                            :p_idoficina, 
                                            :p_idcargo, 
                                            :p_codigolog, 
                                            :p_ip
                                         );";
                    $sentencia = $this->dblink->prepare($sql);
                    // $sentencia->bindParam(":p_codigoCandidato", $this->getCodigoCandidato());
                    $sentencia->bindParam(":p_codusuario", $this->getCodusuario());
                    $sentencia->bindParam(":p_idempresa", $this->getIdempresa());
                    $sentencia->bindParam(":p_idoficina", $this->getIdoficina());
                    $sentencia->bindParam(":p_idcargo", $this->getIdcargo());
                    $sentencia->bindParam(":p_codigolog", $codlog);
                    $sentencia->bindParam(":p_ip", $ip);
                    $sentencia->execute();
                    /*Insertar en la tabla laboratorio*/
                    
                    /*Actualizar el correlativo*/
                    $sql = "update correlativo set numero = numero + 1 
                            where tabla='se_usuario'";
                    $sentencia = $this->dblink->prepare($sql);
                    $sentencia->execute();
                    /*Actualizar el correlativo*/
                    $this->dblink->commit();
                    return "EXITO";
    
       
            

            
        } catch (Exception $exc) {
            $this->dblink->rollBack();
            throw $exc;
        }
        
        return false;
    }
    public function eliminar($ip,$codlog) {
       
        try {
            $sql = "
            SELECT * from fn_eliminarusuarioacceso(
                :p_codusuario,
                :p_codigolog, 
                :p_ip
            )
                ";
            $sentencia = $this->dblink->prepare($sql);
            $sentencia->bindParam(":p_codusuario",  $this->getCodusuario());
            $sentencia->bindParam(":p_codigolog", $codlog);
            $sentencia->bindParam(":p_ip", $ip);
            $sentencia->execute();
            return "EXITO";
            //return $this->getIdempresa();
        } catch (Exception $exc) {
            throw $exc;
        }
    } 
}
