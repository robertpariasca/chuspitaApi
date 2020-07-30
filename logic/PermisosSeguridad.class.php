<?php

require_once '../data/Conexion.class.php';

class UsuarioAcceso extends Conexion {
    
    //private $Cuenta;
    //usuario_acceso

    private $Idcargo;
    private $Codmenuitem;


    public function getIdcargo(){
        return $this->Idcargo;
    }

    public function getIdcodmenuitem(){
        return $this->Codmenuitem;
    }

    public function setIdcargo($Idcargo){
        $this->Idcargo = $Idcargo;
    }

    public function setIdcodmenuitem($Codmenuitem){
        $this->Codmenuitem = $Codmenuitem;
    }


    public function listar() {
       
        try {
            $sql = "
                    select 
                        u.codusuario,
                        u.idempresa,
                        u.idoficina,
                        u.idcargo
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
                    order by 
                        u.codusuario
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
 
}
