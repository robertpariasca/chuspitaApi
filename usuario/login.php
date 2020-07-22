<?php
// required headers
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

// include database and object files
include_once '../config/database.php';
include_once '../objects/login.php';
  
// instantiate database and Login object
$database = new Database();
$db = $database->getConnection();
  
// initialize object
$login = new Login($db);
  
// get values of login
$data = json_decode(file_get_contents("php://input"));
  
//set usuario values
$login->alias = $data->alias;
$login->password = $data->password;

// query products
$stmt = $login->search($keywords);
$num = $stmt->rowCount();

// check if more than 0 record found
if($num>0){
  
    // products array
    $login_arr=array();
    $login_arr["records"]=array();
  
    // retrieve our table contents
    // fetch() is faster than fetchAll()
    // http://stackoverflow.com/questions/2770630/pdofetchall-vs-pdofetch-in-a-loop
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)){
        // extract row
        // this will make $row['name'] to
        // just $name only
        extract($row);
  
        $login_datos=array(
            "codusuario" => $codusuario,
            "nombre" => $nombre,
            "apellidos" => $apellidos
        );
  
        array_push($login_arr["records"], $login_datos);
    }
  
    // set response code - 200 OK
    http_response_code(200);
  
    // show products data
    echo json_encode($login_arr);
}
  
else{
    // set response code - 404 Not found
    http_response_code(404);
  
    // tell the user no products found
    echo json_encode(
        array("message" => "Usuario no encontrado")
    );
}