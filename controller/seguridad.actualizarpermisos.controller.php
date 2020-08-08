<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

try {
    require_once '../logic/PermisosSeguridad.class.php';

$data = json_decode(file_get_contents("php://input"));


    $objSesion = new Permisos();
    //$objSesion->setEmail($email);
    //$objSesion->setClave($clave);
    
    $objSesion->setIdempresa($data->empresa);
    $objSesion->setIdoficina($data->oficina);
    $objSesion->setIdcargo($data->cargo);
    
    $objSesion->eliminar($data->ip,$data->codlog);
    

        foreach ($data->cargocadena as $valor) {
            $objSesion->setIdmodulo($valor->codmodulo);
            $objSesion->setCodmenu($valor->codmenu);
            $objSesion->setCodmenuitem($valor->codmenuitem);
            $objSesion->setTipoactividad($valor->tipoactividad);

            $resultado = $objSesion->agregar($data->ip,$data->codlog);
            
          };
    
    http_response_code(200);
    //echo json_encode($prueba);
    echo json_encode($resultado);

} catch (Exception $exc) {
    echo $exc->getMessage();
}