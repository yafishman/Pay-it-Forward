<?php
//kills session to prevent a logged out user from using the site as if they were logged in
header("Content-Type: application/json");
ini_set("session.cookie_httponly", 1);
SESSION_START();
SESSION_DESTROY();
?>
