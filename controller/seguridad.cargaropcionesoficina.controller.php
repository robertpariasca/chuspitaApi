<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

try {

    require_once '../logic/OficinaSeguridad.class.php';

$data = json_decode(file_get_contents("php://input"));


    $objSesion = new Oficina();
    $objSesion->setIdempresa($data->idempresa);

    $resultado = $objSesion->listarCombo();
    //Helper::imprimeJSON(200, "", $resultado);
    http_response_code(200);
    //echo json_encode(array("message" => $resultado));
    echo json_encode($resultado);
    //echo $data;
} catch (Exception $exc) {
    echo $exc->getMessage();
}