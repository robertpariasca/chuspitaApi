<?php

require_once '../data/Conexion.class.php';

class Usuario extends Conexion {
    private $CodigoUsuario;
    private $Dni;
    private $P_foto;
    private $Nombres;
    private $Apellidos;
    private $Direccion;
    private $Email;
    private $Telefono;
    private $Sexo;
    private $Edad;
    private $Cargo;
    private $Constrasenia;
    private $Tipo;
    private $Estado;
    //private $Cuenta;
    //usuario_acceso
    private $Idempresa;
    private $Idoficina;
    private $Idcargo;


    public function getCodigoUsuario() {
        return $this->CodigoUsuario;
    }

    public function getDni() {
        return $this->Dni;
    }

    public function getP_foto() {
        return $this->P_foto;
    }

    public function getNombres() {
        return $this->Nombres;
    }

    public function getApellidos() {
        return $this->Apellidos;
    }

    public function getDireccion() {
        return $this->Direccion;
    }

    public function getEmail(){
        return $this->Email;
    }

    public function getTelefono(){
        return $this->Telefono;
    }

    public function getSexo(){
        return $this->Sexo;
    }
    
    public function getEdad(){
        return $this->Edad;
    }

    public function getCargo()
    {
        return $this->Cargo; // es el código del cargo
    }

    public function getConstrasenia(){
        return $this->Constrasenia;
    }

    public function getTipo()
    {
        return $this->Tipo;
    }

    public function getEstado(){
        return $this->Estado;
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

    public function setCodigoUsuario($CodigoUsuario) {
        $this->CodigoUsuario = $CodigoUsuario;
    }

    public function setDni($Dni) {
        $this->Dni = $Dni;
    }

    public function setP_foto($P_foto) {
        $this->P_foto = $P_foto;
    }

    public function setNombres($Nombres) {
        $this->Nombres = $Nombres;
    }

    public function setApellidos($Apellidos) {
        $this->Apellidos = $Apellidos;
    }

    public function setDireccion($Direccion) {
        $this->Direccion = $Direccion;
    }

    public function SetEmail($Email){
        $this->Email = $Email;
    }

    public function setTelefono($Telefono) {
        $this->Telefono = $Telefono;
    }

    public function setSexo($Sexo) {
        $this->Sexo = $Sexo;
    }

    public function setEdad($Edad) {
        $this->Edad = $Edad;
    }

    public function SetCargo($Cargo){
        $this->Cargo = $Cargo;
    }

    public function SetConstrasenia($Constrasenia){
        $this->Constrasenia = $Constrasenia;
    }

    public function SetTipo($Tipo){
        $this->Tipo = $Tipo;
    }

    public function SetEstado($Estado){
        $this->Estado = $Estado;
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

    public function listar() {
       
        try {
            $sql = "
                    select 
                        codigo_usuario,
                        clave,
                        tipo,
                        estado,
                        fecha_registro,
                        doc_id
                        
                    from 
                        credenciales_acceso   
                    order by 
                            2
                ";
            $sentencia = $this->dblink->prepare($sql);
            $sentencia->execute();
            $resultado = $sentencia->fetchAll(PDO::FETCH_ASSOC);
            return $resultado;
        } catch (Exception $exc) {
            throw $exc;
        }
    }

    public function agregar() {
        $this->dblink->beginTransaction();
        
        try {
            $sql = "select * from f_generar_correlativo('se_usuario') as nc";
            $sentencia = $this->dblink->prepare($sql);
            $sentencia->execute();
            
            if ($sentencia->rowCount()){
                $resultado = $sentencia->fetch(PDO::FETCH_ASSOC);
                $nuevoCodigo = $resultado["nc"];
                $this->setCodigoUsuario($nuevoCodigo);
                
                /*Insertar en la tabla candidato*/
//                $sql = "
//                    insert into laboratorio(codigo_laboratorio,nombre,codigo_pais) 
//                    values(:p_cod_lab, :p_nomb, :p_codigo_pais)
//                    ";
                
                $sql = "select * from fn_registrarUsuario(                    
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
                // $sentencia->bindParam(":p_codigoCandidato", $this->getCodigoCandidato());
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
                /*Insertar en la tabla laboratorio*/
                
                /*Actualizar el correlativo*/
                $sql = "update correlativo set numero = numero + 1 
                        where tabla='credenciales_acceso'";
                $sentencia = $this->dblink->prepare($sql);
                $sentencia->execute();
                /*Actualizar el correlativo*/
                $this->dblink->commit();
                return true;
                
            }else{
                throw new Exception("No se ha configurado el correlativo para la tabla credenciales_acceso");
            }
            
        } catch (Exception $exc) {
            $this->dblink->rollBack();
            throw $exc;
        }
        
        return false;
    }
   
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
}
