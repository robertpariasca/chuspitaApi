<?php

require_once '../data/Conexion.class.php';

class Menuitems extends Conexion {

    private $idmodulo;
    private $codmenu;
    private $codmenuitem;
    private $desmenuitem;
    private $link;

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

    public function getLink(){
        return $this->link;
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

    public function setLink($link){
        $this->link = $link;
    }

    public function listar() {
       
        try {
            $sql = "
                   	  select 
							i.idmodulo,
							o.desmodulo,
							i.codmenu,
							m.desmenu,
							codmenuitem,
                            desmenuitem,
                            link
						from 
							se_menuitems i
						inner join
							se_modulo o
						on
							i.idmodulo=o.idmodulo
						inner join
							se_menu m
						on
							i.codmenu=m.codmenu and i.idmodulo=m.idmodulo
						group by
							i.idmodulo, o.desmodulo,i.codmenu, m.desmenu,codmenuitem,desmenuitem
						order by 
							o.desmodulo, m.desmenu, codmenuitem
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
                        desmenuitem
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
                                            :p_link,
                                            :p_codigolog, 
                                            :p_ip
                                         );";
                    $sentencia = $this->dblink->prepare($sql);
                    // $sentencia->bindParam(":p_codigoCandidato", $this->getCodigoCandidato());
                    $sentencia->bindParam(":p_idmodulo", $this->getIdmodulo());
                    $sentencia->bindParam(":p_codmenu", $this->getCodmenu());
                    $sentencia->bindParam(":p_codmenuitem", $this->getCodmenuitem());
                    $sentencia->bindParam(":p_desmenuitem", $this->getDesmenuitem());
                    $sentencia->bindParam(":p_link", $this->getLink());
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

    public function eliminar($ip,$codlog) {
       
        try {
            $sql = "
            SELECT * from fn_eliminarmenuitems(
                :p_idmodulo,
                :p_codmenu,
                :p_codmenuitem,
                :p_codigolog, 
                :p_ip
            )
                ";
            $sentencia = $this->dblink->prepare($sql);
            $sentencia->bindParam(":p_idmodulo",  $this->getIdmodulo());
            $sentencia->bindParam(":p_codmenu",  $this->getCodmenu());
            $sentencia->bindParam(":p_codmenuitem",  $this->getCodmenuitem());
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
                            desmenuitem
                    from
                            se_menuitems
                    where
                            idmodulo=:p_idmodulo
                    and
                            codmenu=:p_codmenu
                    and
                            desmenuitem = :p_desmenuitem
                    and
                            codmenuitem <> :p_codmenuitem;
                ";
//fin dondiciones
            $sentenciacon = $this->dblink->prepare($sqlcon);
            $sentenciacon->bindParam(":p_idmodulo", $this->getIdmodulo());
            $sentenciacon->bindParam(":p_codmenu", $this->getCodmenu());
            $sentenciacon->bindParam(":p_codmenuitem", $this->getCodmenuitem());
            $sentenciacon->bindParam(":p_desmenuitem", $this->getDesmenuitem());
//            $sentencia->bindParam(":p_tipo", $this->getTipo());
            $sentenciacon->execute();

            if ($sentenciacon->rowCount()) {

              $this->dblink->commit();
                    return "DU";
            }else{

                    $sql = "select * from fn_editarmenuitems(                    
                                            :p_idmodulo,
                                            :p_codmenu,
                                            :p_codmenuitem,
                                            :p_desmenuitem,
                                            :p_link,
                                            :p_codigolog, 
                                            :p_ip
                                         );";
                    $sentencia = $this->dblink->prepare($sql);
                    // $sentencia->bindParam(":p_codigoCandidato", $this->getCodigoCandidato());
                    $sentencia->bindParam(":p_idmodulo", $this->getIdmodulo());
                    $sentencia->bindParam(":p_codmenu", $this->getCodmenu());
                    $sentencia->bindParam(":p_codmenuitem", $this->getCodmenuitem());
                    $sentencia->bindParam(":p_desmenuitem", $this->getDesmenuitem());
                    $sentencia->bindParam(":p_link", $this->getLink());
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

            }

            
        } catch (Exception $exc) {
            $this->dblink->rollBack();
            throw $exc;
        }
        
        return false;
    }

}
