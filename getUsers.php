<?php
ini_set("session.cookie_httponly", 1);
SESSION_START();
header("Content-Type: application/json");
$json_str = file_get_contents('php://input');
$json_obj = json_decode($json_str, true);
$username = $_SESSION["username"];
$groupName = $json_obj["groupName"];
$mysqli = new mysqli('localhost', 'wustl_inst', 'wustl_pass', 'payitforward');
//checks sql connection
if($mysqli->connect_errno) {
  echo json_encode(array(
    "success" => false,
    "message" => "No connection found"
  ));
  exit;
}
//gets all users that are in a certain group
$stmt = $mysqli->prepare("select user from group_users where group_name='$groupName'");
if(!$stmt){
  echo json_encode(array(
    "success" => false,
    "message" => "Query Prep Failed"
  ));
  exit;
}

$stmt->execute();
$users= [];
$stmt->bind_result($user);
while($stmt->fetch()){
    array_push($users,$user);
}
echo json_encode(array(
  //returns an array of users
  "success" => true,
  "users" => $users
));
exit;
?>
