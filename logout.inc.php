<?php
//If user chooses to logout, the session is reset and the user is taken back to index.php.
if(isset($_POST['submit'])){
    session_start();
    session_unset();
    session_destroy();
    header("Location:../db581/index.php");
    exit();
}
