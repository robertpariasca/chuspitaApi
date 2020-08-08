<?php

require_once '../data/Conexion.class.php';

class Usuario extends Conexion {
    
    //private $Cuenta;
    //usuario_acceso

    private $codusuario;
    private $alias;
    private $clave;
    private $fecharegistro;
    private $nombre;
    private $apellidos;
    private $estado;


    public function getCodusuario() {
        return $this->codusuario;
    }

    public function getAlias(){
        return $this->alias;
    }

    public function getClave(){
        return $this->clave;
    }

    public function getFecharegistro(){
        return $this->fecharegistro;
    }

    public function getNombre() {
        return $this->nombre;
    }

    public function getApellidos() {
        return $this->apellidos;
    }

     public function getEstado(){
        return $this->estado;
    }

    public function setCodusuario($codusuario) {
        $this->codusuario = $codusuario;
    }

    public function setAlias($alias){
        $this->alias = $alias;
    }

    public function setClave($clave){
        $this->clave = $clave;
    }

    public function setFecharegistro($fecharegistro){
        $this->fecharegistro = $fecharegistro;
    }

    public function setNombre($nombre) {
        $this->nombre = $nombre;
    }

    public function setApellidos($apellidos) {
        $this->apellidos = $apellidos;
    }

    public function SetEstado($estado){
        $this->estado = $estado;
    }

    public function listar() {
       
        try {
            $sql = "
                    select 
                        codusuario,
                        alias,
                        clave,
                        fecharegistro,
                        nombre,
                        apellidos
                    from 
                        se_usuario
                    order by 
                        codusuario
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
                            alias
                    from
                            se_usuario
                    where
                            alias = :p_alias;
                ";
//fin dondiciones
            $sentenciacon = $this->dblink->prepare($sqlcon);
            $sentenciacon->bindParam(":p_alias", $this->getAlias());
//            $sentencia->bindParam(":p_tipo", $this->getTipo());
            $sentenciacon->execute();

            if ($sentenciacon->rowCount()) {

              $this->dblink->commit();
                    return "DU";
            }else{
                $sql = "select * from f_generar_correlativo_3digitos('se_usuario') as nc";
                $sentencia = $this->dblink->prepare($sql);
                $sentencia->execute();
                
                if ($sentencia->rowCount()){
                    $resultado = $sentencia->fetch(PDO::FETCH_ASSOC);
                    $nuevoCodigo = $resultado["nc"];
                    $this->setCodusuario($nuevoCodigo);
                    
                    /*Insertar en la tabla candidato*/
    //                $sql = "
    //                    insert into laboratorio(codigo_laboratorio,nombre,codigo_pais) 
    //                    values(:p_cod_lab, :p_nomb, :p_codigo_pais)
    //                    ";
                    
                    $sql = "select * from fn_registrarUsuario(                    
                                            :p_codusuario,
                                            :p_alias, 
                                            :p_clave,
                                            :p_nombre, 
                                            :p_apellidos, 
                                            :p_codigolog, 
                                            :p_ip
                                         );";
                    $sentencia = $this->dblink->prepare($sql);
                    // $sentencia->bindParam(":p_codigoCandidato", $this->getCodigoCandidato());
                    $sentencia->bindParam(":p_codusuario", $this->getCodusuario());
                    $sentencia->bindParam(":p_alias", $this->getAlias());
                    $sentencia->bindParam(":p_clave", $this->getClave());
                    $sentencia->bindParam(":p_nombre", $this->getNombre());
                    $sentencia->bindParam(":p_apellidos", $this->getApellidos());
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
                    
                }else{
                    throw new Exception("No se ha configurado el correlativo para la tabla usuario");
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
            SELECT * from fn_eliminarusuario(
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
    public function actualizar($ip,$codlog) {
        $this->dblink->beginTransaction();
        
        try {
            //condiciones
            $sqlcon = "
                    select 
                            alias
                    from
                            se_usuario
                    where
                            alias = :p_alias
                    and
                            codusuario <> :p_codusuario;
                ";
//fin dondiciones
            $sentenciacon = $this->dblink->prepare($sqlcon);
            $sentenciacon->bindParam(":p_alias", $this->getAlias());
            $sentenciacon->bindParam(":p_codusuario", $this->getCodusuario());
//            $sentencia->bindParam(":p_tipo", $this->getTipo());
            $sentenciacon->execute();

            if ($sentenciacon->rowCount()) {

              $this->dblink->commit();
                    return "DU";
            }else{
                    
                    $sql = "select * from fn_editarUsuario(                    
                                            :p_codusuario,
                                            :p_alias, 
                                            :p_clave,
                                            :p_nombre, 
                                            :p_apellidos, 
                                            :p_codigolog, 
                                            :p_ip
                                         );";
                    $sentencia = $this->dblink->prepare($sql);
                    // $sentencia->bindParam(":p_codigoCandidato", $this->getCodigoCandidato());
                    $sentencia->bindParam(":p_codusuario", $this->getCodusuario());
                    $sentencia->bindParam(":p_alias", $this->getAlias());
                    $sentencia->bindParam(":p_clave", $this->getClave());
                    $sentencia->bindParam(":p_nombre", $this->getNombre());
                    $sentencia->bindParam(":p_apellidos", $this->getApellidos());
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
