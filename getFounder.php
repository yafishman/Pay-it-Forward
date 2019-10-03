<?php
//determines the founder of a certain group so certain HTML attributes can be rendered for that user (perm ban and add user)
ini_set("session.cookie_httponly", 1);
SESSION_START();
header("Content-Type: application/json");
$json_str = file_get_contents('php://input');
$json_obj = json_decode($json_str, true);
$groupName = $json_obj["groupName"];
$mysqli = new mysqli('localhost', 'wustl_inst', 'wustl_pass', 'payitforward');
if($mysqli->connect_errno) {
  echo json_encode(array(
    "success" => false,
    "message" => "No connection found"
  ));
  exit;
}
$stmt = $mysqli->prepare("select founder from groups where name='$groupName'");
if(!$stmt){
  echo json_encode(array(
    "success" => false,
    "message" => "Query Prep Failed"
  ));
  exit;
}

$stmt->execute();

$stmt->bind_result($owner);
//checks to see if that group already exists
while($stmt->fetch()){
  $founder=$owner;
  
}
echo json_encode(array(
  "success" => true,
  "message" => "Founder found!",
  "founder" => $owner
));
exit;
?>
