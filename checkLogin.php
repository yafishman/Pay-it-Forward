<?php
//checks a users login information
ini_set("session.cookie_httponly", 1);
session_start();
header("Content-Type: application/json");
$json_str = file_get_contents('php://input');
$json_obj = json_decode($json_str, true);
$username = $json_obj["username"];
$password = $json_obj["password"];
$zipcode = $json_obj["zipcode"];
$mysqli = new mysqli('localhost', 'wustl_inst', 'wustl_pass', 'payitforward');

if($mysqli->connect_errno) {
  echo json_encode(array(
    "success" => false,
    "message" => "No connection found"
  ));
  exit;
}
//checks that all fields are filled in
if(empty($username) or empty($password) or empty($zipcode)) {
  echo json_encode(array(
    "success" => false,
    "message" => "Please enter a username, password, and zipcode"
  ));
  exit;
} if(strlen($zipcode)==5 && ctype_digit($zipcode)) {
  $stmt = $mysqli->prepare("select username,password from users");
  if(!$stmt){
    echo json_encode(array(
      "success" => false,
      "message" => "Query Prep Failed"
    ));
    exit;
  }

  $stmt->execute();

  $stmt->bind_result($potUser, $potPass);
  //checks to see if the user logged in with correct credentials
  while($stmt->fetch()){
    if(password_verify($password,$potPass) && ($username==$potUser)) {
      $_SESSION['username'] = $username;
      $_SESSION['zipcode'] = $zipcode;
      echo json_encode(array(
        "success" => true,
      ));
      exit;
    }
  }
        echo json_encode(array(
          "success" => false,
          "message" => "Incorrect Username or Password"
        ));
        exit;
  } else {
    echo json_encode(array(
      "success" => false,
      "message" => "Please enter a valid zipcode"
    ));
    exit;
  }
?>
