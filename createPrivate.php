<?php
ini_set("session.cookie_httponly", 1);
SESSION_START();
header("Content-Type: application/json");
$json_str = file_get_contents('php://input');
$json_obj = json_decode($json_str, true);
$groupName = $json_obj["groupName"];
$zipcode = $json_obj["zipcode"];
$password = $json_obj["password"];
$username = $_SESSION["username"];
$mysqli = new mysqli('localhost', 'wustl_inst', 'wustl_pass', 'payitforward');
if($mysqli->connect_errno) {
  echo json_encode(array(
    "success" => false,
    "message" => "No connection found"
  ));
  exit;
}
//makes sure the user is logged in and has given the group a name and a password
if(empty($username)) {
  echo json_encode(array(
    "success" => false,
    "message" => "Please log in to create groups"
  ));
  exit;
}
if(empty($groupName) or empty($password)) {
  echo json_encode(array(
    "success" => false,
    "message" => "Please give your group a name and password"
  ));
  exit;
}
$stmt = $mysqli->prepare("select name from groups");
if(!$stmt){
  echo json_encode(array(
    "success" => false,
    "message" => "Query Prep Failed"
  ));
  exit;
}

$stmt->execute();

$stmt->bind_result($potential);
//checks to see if that group already exists
while($stmt->fetch()){
  if ($potential==$groupName){
    echo json_encode(array(
      "success" => false,
      "message" => "Sorry but that group name is taken"
    ));
    exit;
  }
}
$hashedPass = password_hash($password, PASSWORD_DEFAULT);
$stmt = $mysqli->prepare("insert into groups (name,hasPass,password,founder,zipcode) values (?,?,?,?,?)");
if(!$stmt){
  echo json_encode(array(
    "success" => false,
    "message" => "Query Prep Failed"
  ));
  exit;
}
$true = 1;
$stmt->bind_param('sisss', $groupName, $true, $hashedPass, $username, $zipcode);

$stmt->execute();
$_SESSION['username'] = $username;
$_SESSION['groupName'] = $groupName;
echo json_encode(array(
  "success" => true,
  "message" => "Your private group has been created!"
));
exit;

?>
