<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

try {
    require_once '../logic/CargoSeguridad.class.php';
   
$data = json_decode(file_get_contents("php://input"));


    $objSesion = new Cargo();
    //$objSesion->setEmail($email);
    //$objSesion->setClave($clave);
    $objSesion->setIdempresa($data->idempresa);
    $objSesion->setIdoficina($data->idoficina);
    $objSesion->setIdcargo($data->idcargo);
    $objSesion->setDescripcion($data->descripcion);

    //$resultado = $objSesion->listar();
    $resultado = $objSesion->actualizar($data->ip,$data->codlog);
    http_response_code(200);
    //echo json_encode($data->alias);
    echo json_encode($resultado);
} catch (Exception $exc) {
    echo $exc->getMessage();
}