<?php

require_once '../data/Conexion.class.php';

class Menu extends Conexion {

    private $idmodulo;
    private $codmenu;
    private $desmenu;

    public function getIdmodulo(){
        return $this->idmodulo;
    }

    public function getCodmenu(){
        return $this->codmenu;
    }

    public function getDesmenu(){
        return $this->desmenu;
    }

    public function setIdmodulo($idmodulo){
        $this->idmodulo = $idmodulo;
    }

    public function setCodmenu($codmenu){
        $this->codmenu = $codmenu;
    }

    public function setDesmenu($desmenu){
        $this->desmenu = $desmenu;
    }

    public function listar() {
       
        try {
            $sql = "
                    select 
                        idmodulo,
                        codmenu,
                        desmenu
                    from 
                        se_menu
                    order by 
                        desmenu
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
                        codmenu,
                        desmenu
                    from 
                        se_menu
                    where
                        idmodulo=:p_idmodulo
                    order by 
                        desmenu
                ";
            $sentencia = $this->dblink->prepare($sql);
            $sentencia->bindParam(":p_idmodulo",  $this->getIdmodulo());
            $sentencia->execute();
            $resultado = $sentencia->fetchAll(PDO::FETCH_ASSOC);
            return $resultado;
            //return $this->getIdmodulo();
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
                            desmenu
                    from
                            se_menu
                    where
                            idmodulo=:p_idmodulo
                    and
                            desmenu = :p_desmenu;
                ";
//fin dondiciones
            $sentenciacon = $this->dblink->prepare($sqlcon);
            $sentenciacon->bindParam(":p_idmodulo", $this->getIdmodulo());
            $sentenciacon->bindParam(":p_desmenu", $this->getDesmenu());
//            $sentencia->bindParam(":p_tipo", $this->getTipo());
            $sentenciacon->execute();

            if ($sentenciacon->rowCount()) {

              $this->dblink->commit();
                    return "DU";
            }else{
                $sql = "select to_char(count(*)+1,'fm00') as nc from se_menu where idmodulo=:p_idmodulo;";
                $sentencia = $this->dblink->prepare($sql);
                $sentencia->bindParam(":p_idmodulo", $this->getIdmodulo());
                $sentencia->execute();
                
                if ($sentencia->rowCount()){
                    $resultado = $sentencia->fetch(PDO::FETCH_ASSOC);
                    $nuevoCodigo = $resultado["nc"];
                    $this->setCodmenu($nuevoCodigo);
                    
                    /*Insertar en la tabla candidato*/
    //                $sql = "
    //                    insert into laboratorio(codigo_laboratorio,nombre,codigo_pais) 
    //                    values(:p_cod_lab, :p_nomb, :p_codigo_pais)
    //                    ";
                    
                    $sql = "select * from fn_registrarmenu(                    
                                            :p_idmodulo,
                                            :p_codmenu,
                                            :p_desmenu, 
                                            :p_codigolog, 
                                            :p_ip
                                         );";
                    $sentencia = $this->dblink->prepare($sql);
                    // $sentencia->bindParam(":p_codigoCandidato", $this->getCodigoCandidato());
                    $sentencia->bindParam(":p_idmodulo", $this->getIdmodulo());
                    $sentencia->bindParam(":p_codmenu", $this->getCodmenu());
                    $sentencia->bindParam(":p_desmenu", $this->getDesmenu());
                    $sentencia->bindParam(":p_codigolog", $codlog);
                    $sentencia->bindParam(":p_ip", $ip);
                    $sentencia->execute();
                    /*Insertar en la tabla laboratorio*/
                    
                    /*
                    $sql = "update correlativo set numero = numero + 1 
                            where tabla='se_modulo'";
                    $sentencia = $this->dblink->prepare($sql);
                    $sentencia->execute();
                    */
                    $this->dblink->commit();
                    return "EXITO";
                    
                }else{
                    throw new Exception("No se ha configurado el correlativo para la tabla Menu");
                }
       
            }

            
        } catch (Exception $exc) {
            $this->dblink->rollBack();
            throw $exc;
        }
        
        return false;
    }  
}
