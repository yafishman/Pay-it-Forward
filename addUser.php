<?php
ini_set("session.cookie_httponly", 1);
SESSION_START();
header("Content-Type: application/json");
$json_str = file_get_contents('php://input');
$json_obj = json_decode($json_str, true);
$groupName = $json_obj["groupName"];
$userTo = $json_obj["userTo"];
$username = $_SESSION["username"];
$exists=false;
$mysqli = new mysqli('localhost', 'wustl_inst', 'wustl_pass', 'payitforward');
if($mysqli->connect_errno) {
  echo json_encode(array(
    "success" => false,
    "message" => "No connection found"
  ));
  exit;
}
if(empty($userTo)) {
  echo json_encode(array(
    "success" => false,
    "message" => "Please enter a username to invite"
  ));
  exit;
}

$stmt = $mysqli->prepare("select username from users");
if(!$stmt){
  echo json_encode(array(
    "success" => false,
    "message" => "Query Prep Failed"
  ));
  exit;
}

$stmt->execute();

$stmt->bind_result($user);
while($stmt->fetch()){
  if($userTo==$user) {
    $exists = true;
  }
}
if($exists) {
  //adds the desired user to the list of users in a certain group
  $stmtt = $mysqli->prepare("insert into group_users (user, group_name) values (?, ?)");
  if(!$stmtt){
    echo json_encode(array(
      "success" => false,
      "message" => "Query Prep Failed "
    ));
    exit;
  }

  $stmtt->bind_param('ss', $userTo, $groupName);

  $stmtt->execute();
  echo json_encode(array(
    "success" => true,
    "message" => "You have succesfully invited "
  ));
  exit;
}
//returns that the username entered does not exist
echo json_encode(array(
  "success" => false,
  "message" => "This user does not exist"
));
exit;
?>
