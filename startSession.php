<?php
//allows for the creation of session variables (username and zipcode) once the user has logged in
ini_set("session.cookie_httponly", 1);
SESSION_START();
header("Content-Type: application/json");
$json_str = file_get_contents('php://input');
$json_obj = json_decode($json_str, true);
$_SESSION['username'] = $json_obj["username"];
$_SESSION['zipcode'] = $json_obj["zipcode"];
echo json_encode(array(
    "success" => true
));
?>
