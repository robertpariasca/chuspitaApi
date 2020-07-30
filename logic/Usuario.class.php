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
                        u.codusuario,
                        alias,
                        fecharegistro,
                        nombre,
                        apellidos,
                        estado,
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
   /*
    public function leerDatos($p_dni) {
        try {
            $sql = "
                    select 
                            u.doc_id,
                            u.nombreCompleto,
                            u.direccion,
                            u.telefono,
                            c.clave,                            
                            c.estado,
                            c.codigo_usuario,
                            c.tipo
                        
                    from 
                        usuario u inner join credenciales_acceso c
                    on
                        u.doc_id = c.doc_id
                    where 
                        u.doc_id = :p_dni;

                ";
            
            $sentencia = $this->dblink->prepare($sql);
            $sentencia->bindParam(":p_dni", $p_dni);
            $sentencia->execute();
            $resultado = $sentencia->fetch(PDO::FETCH_ASSOC);
            return $resultado;
        } catch (Exception $exc) {
            throw $exc;
        }
    }

    public function leerFoto($p_dni) {
        try {
            $sql = "
                    select 
                        doc_id
                    from 
                        credenciales_acceso
                    where 
                        doc_id = :p_dni

                ";
            
            $sentencia = $this->dblink->prepare($sql);
            $sentencia->bindParam(":p_dni", $p_dni);
           // $sentencia->bindParam(":p_foto", $this->getP_foto);
            $sentencia->execute();
            $resultado = $sentencia->fetch(PDO::FETCH_ASSOC);
            return $resultado;
        } catch (Exception $exc) {
            throw $exc;
        }
    }



    public function editar() {
        try {
            $sql = "select * from fn_editarUsuario(                    
                                        :p_cod_usuario,
                                        :p_doc_id, 
                                        :p_nombres,
                                        :p_apellidos, 
                                        :p_direccion, 
                                        :p_telefono, 
                                        :p_sexo, 
                                        :p_edad, 
                                        :p_email, 
                                        :p_cargo_id, 
                                        :p_clave,
                                        :p_tipo,
                                        :p_estado
                                     );";
            $sentencia = $this->dblink->prepare($sql);
            
            $sentencia->bindParam(":p_cod_usuario", $this->getCodigoUsuario());
            $sentencia->bindParam(":p_doc_id", $this->getDni());
            $sentencia->bindParam(":p_nombres", $this->getNombres());
            $sentencia->bindParam(":p_apellidos", $this->getApellidos());
            $sentencia->bindParam(":p_direccion", $this->getDireccion());
            $sentencia->bindParam(":p_telefono", $this->getTelefono());
            $sentencia->bindParam(":p_sexo", $this->getSexo());
            $sentencia->bindParam(":p_edad", $this->getEdad());
            $sentencia->bindParam(":p_email", $this->getEmail());
            $sentencia->bindParam(":p_cargo_id", $this->getCargo());
            $sentencia->bindParam(":p_clave", $this->getConstrasenia());
            $sentencia->bindParam(":p_tipo", $this->getTipo());
            $sentencia->bindParam(":p_estado", $this->getEstado());
            $sentencia->execute();
            return true;
        } catch (Exception $exc) {
            throw $exc;
        }
        return false;
    }

    public function eliminar() {
        try {
            $sql = "select * from fn_eliminarUsuario(                    
                                        :p_doc_id
                                     );";
            $sentencia = $this->dblink->prepare($sql);
            $sentencia->bindParam(":p_doc_id", $this->getDni());
            $sentencia->execute();
            return true;
        } catch (Exception $exc) {
            throw $exc;
        }
        return false;
    }
    */
}
