<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

try {
    //$email = $_POST["p_email"];
    //$clave = $_POST["p_clave"];

    require_once '../logic/Sesion.class.php';
    //require_once '../util/functions/Helper.class.php';
    /* Obtener los datos ingresados en el formulario */

//    if (!isset($_POST["txtEmail"]) || $_POST["txtEmail"] == "") {
//        Helper::mensaje("Debe ingresar su email", "e", "../view/index.php", 5);
//    } else 
//        if (!isset($_POST["txtClave"]) || $_POST["txtClave"] == "") {
//        Helper::mensaje("Debe ingresar su clave", "e", "../view/index.php", 5);
//        }

$data = json_decode(file_get_contents("php://input"));


    $objSesion = new Sesion();
    //$objSesion->setEmail($email);
    //$objSesion->setClave($clave);
    $objSesion->setEmail($data->email);
    $objSesion->setClave($data->clave);

    $resultado = $objSesion->iniciarSesion();
    //Helper::imprimeJSON(200, "", $resultado);
    http_response_code(200);
    //echo json_encode(array("message" => $resultado));
    echo json_encode($resultado);
} catch (Exception $exc) {
    echo $exc->getMessage();
}