<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

try {
    require_once '../logic/ModuloSeguridad.class.php';
   
$data = json_decode(file_get_contents("php://input"));


    $objSesion = new Modulo();
    //$objSesion->setEmail($email);
    //$objSesion->setClave($clave);
    $objSesion->setDesmodulo($data->desmodulo);

    //$resultado = $objSesion->listar();
    $resultado = $objSesion->agregar($data->ip,$data->codlog);
    http_response_code(200);
    //echo json_encode($data->alias);
    echo json_encode($resultado);
} catch (Exception $exc) {
    echo $exc->getMessage();
}