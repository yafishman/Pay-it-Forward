<?php
ini_set("session.cookie_httponly", 1);
session_start();
header("Content-Type: application/json");
$json_str = file_get_contents('php://input');
$json_obj = json_decode($json_str, true);
$groupName = $json_obj["groupName"];
$zipcode = $_SESSION["zipcode"];
$username = $_SESSION["username"];
$mysqli = new mysqli('localhost', 'wustl_inst', 'wustl_pass', 'payitforward');
if($mysqli->connect_errno) {
  echo json_encode(array(
    "success" => false,
    "message" => "No connection found"
  ));
  exit;
}
//makes sure a user is logged in and has given the group a name
if(empty($username)) {
  echo json_encode(array(
    "success" => false,
    "message" => "Please log in to create groups"
  ));
  exit;
}
if(empty($groupName)) {
  echo json_encode(array(
    "success" => false,
    "message" => "Please give your group a name"
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
//puts the new group in the groups table
$stmt = $mysqli->prepare("insert into groups (name,hasPass,password,founder,zipcode) values (?,?,?,?,?)");
if(!$stmt){
  echo json_encode(array(
    "success" => false,
    "message" => "Query Prep Failed"
  ));
  exit;
}
$false = 0;
$default = "00000";

$stmt->bind_param('sisss', $groupName, $false, $default, $username, $zipcode);

$stmt->execute();
// $_SESSION['username'] = $username;
// $_SESSION['groupName'] = $groupName;
echo json_encode(array(
  "success" => true,
  "message" => "Your public group has been created!"
));
exit;
?>
