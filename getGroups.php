<?php
ini_set("session.cookie_httponly", 1);
SESSION_START();
header("Content-Type: application/json");
$json_str = file_get_contents('php://input');
$json_obj = json_decode($json_str, true);
$username = $_SESSION["username"];
$mysqli = new mysqli('localhost', 'wustl_inst', 'wustl_pass', 'payitforward');
if($mysqli->connect_errno) {
  echo json_encode(array(
    "success" => false,
    "message" => "No connection found"
  ));
  exit;
}
//prepares to get all groups from groups table
$stmt = $mysqli->prepare("select name,hasPass from groups");
if(!$stmt){
  echo json_encode(array(
    "success" => false,
    "message" => "Query Prep Failed"
  ));
  exit;
}

$stmt->execute();
$groups= [];
$stmt->bind_result($groupName,$hasPass);
while($stmt->fetch()){
  $array = [
      'name'=> $groupName,
      'hasPass'=> $hasPass];
    array_push($groups,$array);
  }
  //gets names of groups a user is banned from
$stmt = $mysqli->prepare("select group_name from banned_users where user='$username'");
if(!$stmt){
  echo json_encode(array(
    "success" => false,
    "message" => "Query Prep Failed"
  ));
  exit;
}
//pushes group names and groups a user is banned from
$stmt->execute();
$bannedgroups= [];
$stmt->bind_result($groupName);
while($stmt->fetch()){
  $array = [
      'name'=> $groupName];
    array_push($bannedgroups,$array);
  }
array_diff($groups,$bannedgroups);
echo json_encode(array(
  "success" => true,
  "groups" => $groups
));
exit;
?>
