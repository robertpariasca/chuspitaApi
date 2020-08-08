<?php

require_once '../data/Conexion.class.php';

class Permisos extends Conexion {
    
    //private $Cuenta;
    //usuario_acceso

    private $Idempresa;
    private $Idoficina;
    private $Idcargo;
    private $Idmodulo;
    private $Codmenu;
    private $Codmenuitem;
    private $Tipoactividad;


    public function getIdempresa(){
        return $this->Idempresa;
    }

    public function getIdoficina(){
        return $this->Idoficina;
    }

    public function getIdcargo(){
        return $this->Idcargo;
    }

    public function getIdmodulo(){
        return $this->Idmodulo;
    }

    public function getCodmenu(){
        return $this->Codmenu;
    }

    public function getCodmenuitem(){
        return $this->Codmenuitem;
    }

    public function getTipoactividad(){
        return $this->Tipoactividad;
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

    public function setIdmodulo($Idmodulo){
        $this->Idmodulo = $Idmodulo;
    }

    public function setCodmenu($Codmenu){
        $this->Codmenu = $Codmenu;
    }

    public function setCodmenuitem($Codmenuitem){
        $this->Codmenuitem = $Codmenuitem;
    }

    public function setTipoactividad($Tipoactividad){
        $this->Tipoactividad = $Tipoactividad;
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
                        r.descripcion
                    from 
                        se_permisos c
                    inner join
                        se_empresa e
                    on 
                        c.idempresa=e.idempresa
                    inner join
                        se_oficina o
                    on 
                        c.idoficina=o.idoficina and c.idempresa=o.idempresa
                    inner join
                        se_cargo r
                    on
                        c.idcargo=r.idcargo and c.idoficina=r.idoficina and c.idempresa=r.idempresa
					group by
						c.idempresa, e.desempresa, c.idoficina, o.desoficina, c.idcargo, r.descripcion, r.idcargo
                    order by 
                        e.desempresa, o.desoficina, r.idcargo
                ";
            $sentencia = $this->dblink->prepare($sql);
            $sentencia->execute();
            $resultado = $sentencia->fetchAll(PDO::FETCH_ASSOC);
            return $resultado;
        } catch (Exception $exc) {
            throw $exc;
        }
    }
    public function listarPermisos() {
       
        try {
            $sql = "
            select 
                        c.idmodulo,
                        m.desmodulo,
                        c.codmenu,
                        o.desmenu,
                        c.codmenuitem,
                        r.desmenuitem,
                        c.tipoactividad,
                        r.link                      
                    from 
                        se_permisos c
                    inner join
                        se_modulo m
                    on 
                        c.idmodulo=m.idmodulo
                    inner join
                        se_menu o
                    on 
                        c.idmodulo=o.idmodulo and c.codmenu=o.codmenu
                    inner join
                        se_menuitems r
                    on
                        c.idmodulo=r.idmodulo and c.codmenu=r.codmenu and c.codmenuitem=r.codmenuitem
					where
						c.idempresa=:p_idempresa and c.idoficina=:p_idoficina and c.idcargo=:p_idcargo
					
					order by
						 m.idmodulo, o.codmenu, r.codmenuitem;
                ";
            $sentencia = $this->dblink->prepare($sql);
            $sentencia->bindParam(":p_idempresa", $this->getIdempresa());
            $sentencia->bindParam(":p_idoficina", $this->getIdoficina());
            $sentencia->bindParam(":p_idcargo", $this->getIdcargo());
            $sentencia->execute();
            $resultado = $sentencia->fetchAll(PDO::FETCH_ASSOC);
            return $resultado;
        } catch (Exception $exc) {
            throw $exc;
        }
    }


    public function validar() {
        $this->dblink->beginTransaction();
        
        try {
         
            $sqlcon = "
                    select 
                            idempresa
                    from
                            se_permisos
                    where
                            idempresa = :p_idempresa
                    and
                            idcargo = :p_idcargo
                    and
                            idoficina = :p_idoficina
                            ;
                ";
//fin dondiciones
            $sentenciacon = $this->dblink->prepare($sqlcon);
            $sentenciacon->bindParam(":p_idempresa", $this->getIdempresa());
            $sentenciacon->bindParam(":p_idcargo", $this->getIdcargo());
            $sentenciacon->bindParam(":p_idoficina", $this->getIdoficina());
//            $sentencia->bindParam(":p_tipo", $this->getTipo());
            $sentenciacon->execute();

            if ($sentenciacon->rowCount()) {

              $this->dblink->commit();
                    return "DU";
            }else{
                $this->dblink->commit();
                    return "PASA";
            }
  
        } catch (Exception $exc) {
            $this->dblink->rollBack();
            throw $exc;
        }
        
        return false;
    }

    public function agregar($ip,$codlog) {
        $this->dblink->beginTransaction();
        
        try {
                
                    $sql = "select * from fn_registrarpermisos(                    
                                            :p_idempresa, 
                                            :p_idoficina, 
                                            :p_idcargo,
                                            :p_idmodulo,
                                            :p_codmenu,
                                            :p_codmenuitem, 
                                            :p_tipoactividad, 
                                            :p_codigolog, 
                                            :p_ip
                                         );";
                    $sentencia = $this->dblink->prepare($sql);
                    // $sentencia->bindParam(":p_codigoCandidato", $this->getCodigoCandidato());
                    $sentencia->bindParam(":p_idempresa", $this->getIdempresa());
                    $sentencia->bindParam(":p_idoficina", $this->getIdoficina());
                    $sentencia->bindParam(":p_idcargo", $this->getIdcargo());
                    $sentencia->bindParam(":p_idmodulo", $this->getIdmodulo());
                    $sentencia->bindParam(":p_codmenu", $this->getCodmenu());
                    $sentencia->bindParam(":p_codmenuitem", $this->getCodmenuitem());
                    $sentencia->bindParam(":p_tipoactividad", $this->getTipoactividad());
                    $sentencia->bindParam(":p_codigolog", $codlog);
                    $sentencia->bindParam(":p_ip", $ip);
                    $sentencia->execute();

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
            SELECT * from fn_eliminarpermisos(
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
}
