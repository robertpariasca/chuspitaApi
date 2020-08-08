<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

try {
    require_once '../logic/Usuario.class.php';
    require_once '../logic/UsuarioAcceso.class.php';

$data = json_decode(file_get_contents("php://input"));


    $objSesion = new Usuario();
    $objSesionAcceso = new UsuarioAcceso();
    //$objSesion->setEmail($email);
    $objSesion->setCodusuario($data->codusuario);
    $objSesion->setAlias($data->alias);
    $objSesion->setClave($data->contraseña);
    $objSesion->setNombre($data->nombre);
    $objSesion->setApellidos($data->apellidos);
    
    $resultado = $objSesion->actualizar($data->ip,$data->codlog);
   
    if ($resultado=="EXITO"){
        $objSesionAcceso->setCodusuario($objSesion->getCodusuario());
        $objSesionAcceso->eliminar($data->ip,$data->codlog);
        foreach ($data->cargocadena as $valor) {
           
            $objSesionAcceso->setIdempresa($valor->codempresa);
            $objSesionAcceso->setIdoficina($valor->codoficina);
            $objSesionAcceso->setIdcargo($valor->codcargo);
            $objSesionAcceso->agregar($data->ip,$data->codlog);
          };
    }

    http_response_code(200);
    //echo json_encode($prueba);
    echo json_encode($resultado);

} catch (Exception $exc) {
    echo $exc->getMessage();
}