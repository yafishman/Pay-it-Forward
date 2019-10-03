<?php
ini_set("session.cookie_httponly", 1);
SESSION_START();
header("Content-Type: application/json");
$json_str = file_get_contents('php://input');
$json_obj = json_decode($json_str, true);
//$groupName = $_SESSION["groupName"];
$group = $json_obj["group"];
$username = $_SESSION["username"];
$mysqli = new mysqli('localhost', 'wustl_inst', 'wustl_pass', 'payitforward');
if($mysqli->connect_errno) {
  echo json_encode(array(
    "success" => false,
    "message" => "No connection found"
  ));
  exit;
}
//retrieves all the deals from a certain group, as well as all the information about them
$stmt = $mysqli->prepare("select name,date,location,poster,likes,reports from deals where group_name='$group'");
if(!$stmt){
  echo json_encode(array(
    "success" => false,
    "message" => "Query Prep Failed"
  ));
  exit;
}

$stmt->execute();
$deals= [];
$stmt->bind_result($deal,$date,$zipcode,$poster,$likes,$reports);
while($stmt->fetch()){
  if($reports < 5) {
    $array = [
      'deal'=> $deal,
      'date'=> $date,
      'likes'=> $likes,
      'zipcode'=> $zipcode,
      'poster'=> $poster,
      'reports'=> $reports];
    array_push($deals,$array);
  }
}
//passes back associate array of deals (like a dictionary)
echo json_encode(array(
  "success" => true,
  "deals" => $deals
));
exit;
?>
