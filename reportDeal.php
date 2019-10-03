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
    "message" => "Please log in to report deals"
  ));
  exit;
} 
$stmt = $mysqli->prepare("select deal from reported_deals where user='$username'");
if(!$stmt){
  echo json_encode(array(
    "success" => false,
    "message" => "Query Prep Failed"
  ));
  exit;
}

$stmt->execute();

$stmt->bind_result($potential);
//checks to see if that user already reported that deal
while($stmt->fetch()){
  if($potential==$deal) {
    echo json_encode(array(
      "success" => false,
      "message" => "You've already reported this deal"
    ));
    exit;
  }
}
//puts the deal and the user reporting it into the reported_deals table
$stmt = $mysqli->prepare("insert into reported_deals (deal, user) values (?, ?)");
if(!$stmt){
  echo json_encode(array(
    "success" => false,
    "message" => "Query Prep Failed"
  ));
  exit;
}
$stmt->bind_param('ss', $deal, $username);
$stmt->execute();
//gets the number of reports for a certain deal
$stmt = $mysqli->prepare("select reports from deals where name='$deal'");
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
  $reports=$potential;
}
$reports++;
//increases the number of reports when someone else reports this deal
$stmt = $mysqli->prepare("update deals set reports='$reports' where name='$deal'");
if (!$stmt){
  echo json_encode(array(
    "success" => false,
    "message" => "Query Prep Failed"
  ));
  exit;
}
$stmt->execute();
echo json_encode(array(
  "success" => true,
  "message" => "This deal has been reported"
));
exit;
?>
