<?php
ini_set("session.cookie_httponly", 1);
SESSION_START();
header("Content-Type: application/json");
$json_str = file_get_contents('php://input');
$json_obj = json_decode($json_str, true);
$groupName = $json_obj["groupName"];
$password = $json_obj["password"];
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
if(empty($username)) {
  if($hasPass) {
  } else {
    if($groupZip==$zipcode) {
    } else {
      //for a public group, checks to see if the zip codes are the same
      echo json_encode(array(
        "success" => false,
        "message" => "This group is not in your zip code."
      ));
      exit;
    }
  }
echo json_encode(array(
  "success" => true,
  "message" => "You have succesfully joined!"
));
exit;
} else {
  //checks to see if you are banned from the group you are trying to join
  $stmt = $mysqli->prepare("select user from banned_users where group_name='$groupName'");
  if(!$stmt){
    echo json_encode(array(
      "success" => false,
      "message" => "Query Prep Failed 1"
    ));
    exit;
  }

  $stmt->execute();

  $stmt->bind_result($user);
  while($stmt->fetch()){
    if($username==$user) {
      echo json_encode(array(
        "success" => false,
        "message" => "You are banned from this group"
      ));
      exit;
    }
  }
  //gets the information about a group with the name being the group name you are trying to join
  $stmt = $mysqli->prepare("select name,hasPass,password,zipcode from groups where name='$groupName'");
  if(!$stmt){
    echo json_encode(array(
      "success" => false,
      "message" => "Query Prep Failed 2"
    ));
    exit;
  }
  $stmt->execute();

  $stmt->bind_result($potential,$hasPass,$groupPass,$groupZip);

  while($stmt->fetch()){
    if($hasPass) {
      //for a private group, checks to see if the password entered matches the password of the group
      if(password_verify($password,$groupPass)) {
      } else {
        echo json_encode(array(
          "success" => false,
          "message" => "Incorrect password."
        ));
        exit;
      }
    } else {
      if($groupZip==$zipcode) {
      } else {
        echo json_encode(array(
          "success" => false,
          "message" => "This group is not in your zip code."
        ));
        exit;
      }
    }
  }
  //if you have not joined the group before, it will add your username to the users in said group
  $stmtt = $mysqli->prepare("insert into group_users (user, group_name) values (?, ?)");
  if(!$stmtt){
    echo json_encode(array(
      "success" => false,
      "message" => "Query Prep Failed 3/4"
    ));
    exit;
  }

  $stmtt->bind_param('ss', $username, $groupName);

  $stmtt->execute();
  echo json_encode(array(
    "success" => true,
    "message" => "You have succesfully joined!"
  ));
  exit;
}
?>
