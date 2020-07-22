<?php

require_once '../data/Conexion.class.php';

class Sesion extends Conexion {

    private $email;
    private $clave;

    public function getEmail() {
        return $this->email;
    }

    public function getClave() {
        return $this->clave;
    }

    public function setEmail($email) {
        $this->email = $email;
    }

    public function setClave($clave) {
        $this->clave = $clave;
    }

    public function iniciarSesion() {
        try {
            $sql = "
                    select 
                            u.codusuario,
                            nombre,
                            clave,
                            apellidos,
                            idempresa,
                            idcargo,
                            idoficina,
                            estado
                    from
                            se_usuario u
                    inner join
                            se_usuario_acceso a
                    on
                            u.codusuario=a.codusuario
                    where
                            alias = :p_email;
                ";


            $sentencia = $this->dblink->prepare($sql);
            $sentencia->bindParam(":p_email", $this->getEmail());
//            $sentencia->bindParam(":p_tipo", $this->getTipo());
            $sentencia->execute();

            if ($sentencia->rowCount()) {//Le pregunto si ha devuelto registros
                //El usuario si existe
                
                $resultado = $sentencia->fetch(PDO::FETCH_ASSOC);
                if ($resultado["clave"] === md5($this->getClave())) {
                    if ($resultado["estado"] === "0") {
                        return "IN"; //Usuario Inactivo
                    } else {
                        //session_name("CampusVirtual");
                        //session_start();
                        extract($resultado);
                        $datos_login=array(
                            "codusuario" => $codusuario,
                            "nombre" => $nombre,
                            "apellidos" => $apellidos,
                            "idempresa" => $idempresa,
                            "idcargo" => $idcargo,
                            "idoficina" => $idoficina
                        );
                        $products_arr=array();
                        $products_arr["registro"]=array();
                        array_push($products_arr["registro"], $datos_login);
/*                     
//                        $_SESSION["s_usuario"]  = $resultado["nombre"] . ' ' . $resultado["apellidos"];
                        $_SESSION["s_usuario"] = $resultado["nombrecompleto"];
                        $_SESSION["s_email"] = $this->getEmail();
                        $_SESSION["s_doc_id"] = $resultado["doc_id"];
                        $_SESSION["codigo_usuario"] = $resultado["codigo_usuario"];
                        $_SESSION["cargo_id"] = $resultado["cargo_id"];
                        $_SESSION["cargo"] = $resultado["cargo"]; // descripción del cargo
                        $_SESSION["tipo"] = $resultado["tipo"]; // tipo de usuario rol
*/
                        return $products_arr; //Si ingresa
                    }
                } else { //la contraseña no es igual
                    return "CI"; //Contraseña incorrecta
                }
            } else { //No se encontró registros (El usuario no existe)
                return "NE"; //No Existe
            }
        } catch (Exception $exc) {
            throw $exc;
        }
    }
    public function obtenerOpcionesMenu($codigoCargo) {
        try {
            $sql = "
                select
                        distinct 
                        m.codigo_menu,
                        m.nombre
                from
                        menu m
                        inner join menu_item_accesos a on ( m.codigo_menu = a.codigo_menu )
                where
                        a.cargo_id = :p_cargo_id
                        and a.acceso = '1'
                order by
                        1
                ";
            $sentencia = $this->dblink->prepare($sql);
            $sentencia->bindParam(":p_cargo_id", $codigoCargo);
            $sentencia->execute();
            $resultado = $sentencia->fetchAll(PDO::FETCH_ASSOC);
            return $resultado;
        } catch (Exception $exc) {
            throw $exc;
        }
    }

public function obtenerOpcionesMenuItem($codigoCargo, $codigoMenu) {
        try {
            $sql = "
                    select
                            m.nombre,
                            m.archivo
                    from
                            menu_item m
                            inner join menu_item_accesos a 
                            on 
                            ( 
                                    m.codigo_menu = a.codigo_menu and 
                                    m.codigo_menu_item = a.codigo_menu_item 
                            )

                    where
                            a.cargo_id = :p_cargo_id
                            and a.codigo_menu = :p_codigo_menu
                            and a.acceso = '1'
                    order by
                            a.codigo_menu_item
                ";
            
//            $sentencia = $this->dbLink->prepare($sql);
            $sentencia = $this->dblink->prepare($sql);
            $sentencia->bindParam(":p_cargo_id", $codigoCargo);
            $sentencia->bindParam(":p_codigo_menu", $codigoMenu);
            $sentencia->execute();
            $resultado = $sentencia->fetchAll(PDO::FETCH_ASSOC);
            return $resultado;
            
        } catch (Exception $exc) {
            throw $exc;
        }
    }
    
    
}
