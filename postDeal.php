<?php
ini_set("session.cookie_httponly", 1);
SESSION_START();
header("Content-Type: application/json");
$json_str = file_get_contents('php://input');
$json_obj = json_decode($json_str, true);
$groupName = $json_obj["groupName"];
$deal = $json_obj["deal"];
$zipcode = $_SESSION["zipcode"];
$username = $_SESSION["username"];
$mysqli = new mysqli('localhost', 'wustl_inst', 'wustl_pass', 'payitforward');
if($mysqli->connect_errno) {
  echo json_encode(array(
    "success" => false,
    "message" => "No connection found"
  ));
  exit;
}
//only lets a user post a deal if they are logged in and in a group and checks that they have not submitted an empty deal
if(empty($username)) {
  echo json_encode(array(
    "success" => false,
    "message" => "Please log in to post a deal"
  ));
  exit;
}
if(empty($groupName)) {
  echo json_encode(array(
    "success" => false,
    "message" => "Please join a group to post a deal"
  ));
  exit;
}
if(empty($deal)) {
  echo json_encode(array(
    "success" => false,
    "message" => "Please provide the details of your deal"
  ));
  exit;
} else {
  //inserts the deal into the deals table
    $stmt = $mysqli->prepare("insert into deals (name,group_name,location, poster) values (?, ?,?,?)");
    if(!$stmt){
      echo json_encode(array(
        "success" => false,
        "message" => "Query Prep Failed"
      ));
      exit;
    }
    $stmt->bind_param('ssss', $deal, $groupName, $zipcode, $username);
    $stmt->execute();
    echo json_encode(array(
      "success" => true,
      "message" => "Your deal has been posted! Thanks for paying it forward"
    ));
    exit;
  }
?>
