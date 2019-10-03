<?php
ini_set("session.cookie_httponly", 1);
SESSION_START();
header("Content-Type: application/json");
$json_str = file_get_contents('php://input');
$json_obj = json_decode($json_str, true);
$groupName = $_SESSION["groupName"];
$deal = $json_obj["deal"];
$username = $_SESSION["username"];
$mysqli = new mysqli('localhost', 'wustl_inst', 'wustl_pass', 'payitforward');
if($mysqli->connect_errno) {
  echo json_encode(array(
    "success" => false,
    "message" => "No connection found"
  ));
  exit;
}
if(empty($username)) {
  echo json_encode(array(
    "success" => false,
    "message" => "Please log in to like deals"
  ));
  exit;
} 
$stmt = $mysqli->prepare("select deal_name from liked_deals where user='$username'");
if(!$stmt){
  echo json_encode(array(
    "success" => false,
    "message" => "Query Prep Failed"
  ));
  exit;
}

$stmt->execute();

$stmt->bind_result($potential);
//checks to see if that username already liked said deal
while($stmt->fetch()){
  if($potential==$deal) {
    echo json_encode(array(
      "success" => false,
      "message" => "You've already liked this deal"
    ));
    exit;
  }
}
$stmt = $mysqli->prepare("insert into liked_deals (deal_name, user) values (?, ?)");
if(!$stmt){
  echo json_encode(array(
    "success" => false,
    "message" => "Query Prep Failed"
  ));
  exit;
}
$stmt->bind_param('ss', $deal, $username);
$stmt->execute();

$stmt = $mysqli->prepare("select likes from deals where name='$deal'");
//gets the number of likes from a deal
if(!$stmt){
  echo json_encode(array(
    "success" => false,
    "message" => "Query Prep Failed 2"
  ));
  exit;
}

$stmt->execute();

$stmt->bind_result($potential);
while($stmt->fetch()){
  $likes=$potential;
}
$likes++;
//increases the number of likes for a deal
$stmt = $mysqli->prepare("update deals set likes='$likes' where name='$deal'");
if (!$stmt){
  echo json_encode(array(
    "success" => false,
    "message" => "Query Prep Failed 3"
  ));
  exit;
}
$stmt->execute();
echo json_encode(array(
  "success" => true,
  "message" => "This deal has been liked"
));
exit;
?>
