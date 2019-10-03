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
$stmt = $mysqli->prepare("select founder from groups where name='$groupName'");
if(!$stmt){
  echo json_encode(array(
    "success" => false,
    "message" => "Query Prep Failed"
  ));
  exit;
}

$stmt->execute();

$stmt->bind_result($potential);
while($stmt->fetch()){
  if ($potential!=$username){
    echo json_encode(array(
      "success" => false,
      "message" => "You are not the creator of this group"
    ));
    exit;
  }
}
//adds the user to the banned user list so they will not be able to join this group again
$stmt = $mysqli->prepare("insert into banned_users (group_name,user) values (?, ?)");
if(!$stmt){
  echo json_encode(array(
    "success" => false,
    "message" => "Query Prep Failed"
  ));
  exit;
}

$stmt->bind_param('ss', $groupName, $userTo);

$stmt->execute();
//removes a user's username from the list of users in a group
$stmt = $mysqli->prepare("delete from group_users where group_name='$groupName' and user='$userTo'");
if (!$stmt){
  printf ("Query Prep Failed");
}
$stmt->execute();
$stmt->close();
echo json_encode(array(
    "success" => true,
    "message" => " has been permanently banned from "
  ));
  exit;
?>
