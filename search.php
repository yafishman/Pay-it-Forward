<?php
ini_set("session.cookie_httponly", 1);
SESSION_START();
header("Content-Type: application/json");
$json_str = file_get_contents('php://input');
$json_obj = json_decode($json_str, true);
$potential = $json_obj["potential"];
$false = 0;
$mysqli = new mysqli('localhost', 'wustl_inst', 'wustl_pass', 'payitforward');
if($mysqli->connect_errno) {
  echo json_encode(array(
    "success" => false,
    "message" => "No connection found"
  ));
  exit;
}
if(empty($potential)) {
  echo json_encode(array(
    "success" => false,
    "message" => "Please enter a group name"
  ));
  exit;
}
//only allows users to search through public groups
$stmt = $mysqli->prepare("select name from groups where hasPass='$false'");
if(!$stmt){
  echo json_encode(array(
    "success" => false,
    "message" => "Query Prep Failed"
  ));
  exit;
}

$stmt->execute();

$stmt->bind_result($groupName);
//checks to see if that group already exists
while($stmt->fetch()){
  if ($potential==$groupName){
    echo json_encode(array(
      "success" => true,
      "message" => "Group found!"
    ));
    exit;
  }
}
echo json_encode(array(
  "success" => false,
  "message" => "Group not found!"
));
exit;
?>
