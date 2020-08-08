<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

try {
    require_once '../logic/Usuario.class.php';
   
$data = json_decode(file_get_contents("php://input"));


    $objSesion = new Usuario();
    //$objSesion->setEmail($email);
    //$objSesion->setClave($clave);
    $objSesion->setCodusuario($data->codusuario);

    //$resultado = $objSesion->listar();
    $resultado = $objSesion->eliminar($data->ip,$data->codlog);
    http_response_code(200);
    //echo json_encode($data->alias);
    echo json_encode($resultado);
} catch (Exception $exc) {
    echo $exc->getMessage();
}