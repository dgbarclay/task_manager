<?php

include_once 'header.php';

include_once 'dbh.inc.php';
// must be logged in
if(isset($_SESSION['u_id'])){
  $cookie_name = "mode";
  $cookie_value = $_REQUEST['mode'];
  setcookie($cookie_name, $cookie_value, time() + (86400 * 30), "/"); // 86400 = 1 day
	// update user of change
  header("Location: ../db581/account.php?mode=$cookie_value");
}
else{
  header("Location: ../db581/index.php?user=notloggedin");
}

?>
