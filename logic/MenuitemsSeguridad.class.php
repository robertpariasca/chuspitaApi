<?php

require_once '../data/Conexion.class.php';

class Menuitems extends Conexion {

    private $idmodulo;
    private $codmenu;
    private $codmenuitem;
    private $desmenuitem;
    private $tipoactividad;

    public function getIdmodulo(){
        return $this->idmodulo;
    }

    public function getCodmenu(){
        return $this->codmenu;
    }

    public function getCodmenuitem(){
        return $this->codmenuitem;
    }

    public function getDesmenuitem(){
        return $this->desmenuitem;
    }

    public function getTipoactividad(){
        return $this->tipoactividad;
    }

    public function setIdmodulo($idmodulo){
        $this->idmodulo = $idmodulo;
    }

    public function setCodmenu($codmenu){
        $this->codmenu = $codmenu;
    }
    
    public function setCodmenuitem($codmenuitem){
        $this->codmenuitem = $codmenuitem;
    }

    public function setDesmenuitem($desmenuitem){
        $this->desmenuitem = $desmenuitem;
    }

    public function setTipoactividad($tipoactividad){
        $this->tipoactividad = $tipoactividad;
    }

    public function listar() {
       
        try {
            $sql = "
                    select 
                        idmodulo,
                        codmenu,
                        codmenuitem,
                        desmenuitem,
                        tipoactividad
                    from 
                        se_menuitems
                    order by 
                        desmenuitem
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
                        codmenu,
                        codmenuitem,
                        desmenuitem,
                        tipoactividad
                    from 
                        se_menuitems
                    where
                        idmodulo=:p_idmodulo
                    and
                        codmenu=:p_codmenu
                    order by 
                        desmenuitem
                ";
            $sentencia = $this->dblink->prepare($sql);
            $sentencia->bindParam(":p_idmodulo",  $this->getIdmodulo());
            $sentencia->bindParam(":p_codmenu",  $this->getCodmenu());
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
                            desmenuitem
                    from
                            se_menuitems
                    where
                            idmodulo=:p_idmodulo
                    and
                            codmenu=:p_codmenu
                    and
                            desmenuitem = :p_desmenuitem;
                ";
//fin dondiciones
            $sentenciacon = $this->dblink->prepare($sqlcon);
            $sentenciacon->bindParam(":p_idmodulo", $this->getIdmodulo());
            $sentenciacon->bindParam(":p_codmenu", $this->getCodmenu());
            $sentenciacon->bindParam(":p_desmenuitem", $this->getDesmenuitem());
//            $sentencia->bindParam(":p_tipo", $this->getTipo());
            $sentenciacon->execute();

            if ($sentenciacon->rowCount()) {

              $this->dblink->commit();
                    return "DU";
            }else{
                $sql = "select to_char(count(*)+1,'fm00') as nc from se_menuitems where idmodulo=:p_idmodulo and codmenu=:p_codmenu;";
                $sentencia = $this->dblink->prepare($sql);
                $sentencia->bindParam(":p_idmodulo", $this->getIdmodulo());
                $sentencia->bindParam(":p_codmenu", $this->getCodmenu());
                $sentencia->execute();
                
                if ($sentencia->rowCount()){
                    $resultado = $sentencia->fetch(PDO::FETCH_ASSOC);
                    $nuevoCodigo = $resultado["nc"];
                    $this->setCodmenuitem($nuevoCodigo);
                    
                    /*Insertar en la tabla candidato*/
    //                $sql = "
    //                    insert into laboratorio(codigo_laboratorio,nombre,codigo_pais) 
    //                    values(:p_cod_lab, :p_nomb, :p_codigo_pais)
    //                    ";
                    
                    $sql = "select * from fn_registrarmenuitems(                    
                                            :p_idmodulo,
                                            :p_codmenu,
                                            :p_codmenuitem,
                                            :p_desmenuitem,
                                            :p_tipoactividad,
                                            :p_codigolog, 
                                            :p_ip
                                         );";
                    $sentencia = $this->dblink->prepare($sql);
                    // $sentencia->bindParam(":p_codigoCandidato", $this->getCodigoCandidato());
                    $sentencia->bindParam(":p_idmodulo", $this->getIdmodulo());
                    $sentencia->bindParam(":p_codmenu", $this->getCodmenu());
                    $sentencia->bindParam(":p_codmenuitem", $this->getCodmenuitem());
                    $sentencia->bindParam(":p_desmenuitem", $this->getDesmenuitem());
                    $sentencia->bindParam(":p_tipoactividad", $this->getTipoactividad());
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
