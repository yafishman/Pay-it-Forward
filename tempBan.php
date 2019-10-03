<?php
ini_set("session.cookie_httponly", 1);
SESSION_START();
header("Content-Type: application/json");
$json_str = file_get_contents('php://input');
$json_obj = json_decode($json_str, true);
$groupName = $json_obj["groupName"];
$userTo = $json_obj["userTo"];
$username = $_SESSION["username"];
$mysqli = new mysqli('localhost', 'wustl_inst', 'wustl_pass', 'payitforward');
if($mysqli->connect_errno) {
  echo json_encode(array(
    "success" => false,
    "message" => "No connection found"
  ));
  exit;
}
//removes a user's username from the list of users in a group, so they will have to join the group again to be back in it
$stmt = $mysqli->prepare("delete from group_users where group_name='$groupName' and user='$userTo'");
if (!$stmt){
  printf ("Query Prep Failed");
}
$stmt->execute();
$stmt->close();
echo json_encode(array(
    "success" => true,
    "message" => " has been temporarily banned from "
  ));
  exit;
?>
