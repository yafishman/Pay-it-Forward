<?php
//finds deals in a certain group with a certain name and deletes them
ini_set("session.cookie_httponly", 1);
SESSION_START();
header("Content-Type: application/json");
$json_str = file_get_contents('php://input');
$json_obj = json_decode($json_str, true);
$username = $_SESSION['username'];
$deal = $json_obj["deal"];
$groupName = $json_obj["groupName"];
$mysqli = new mysqli('localhost', 'wustl_inst', 'wustl_pass', 'payitforward');
if($mysqli->connect_errno){
  printf("Connection Failed");
  exit;
}
$stmt = $mysqli->prepare("delete from deals where name='$deal' and group_name='$groupName'");
if (!$stmt){
  printf ("Query Prep Failed");
}

$stmt->execute();
$stmt->close();
echo json_encode(array(
  "success" => true,
  "message" => "Your deal has been deleted"
));
exit;
?>
