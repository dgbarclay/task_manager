<?php

session_start();

if (isset($_POST['submit'])) {

    include_once 'dbh.inc.php';

    $uid = $_POST['uid'];
    $pwd = $_POST['pwd'];
    //Exception handling
    //Check if empty
    if (empty($uid)||empty($pwd)) {
        header("Location: ../index.php?login=empty");
        exit();
    }else{
        //Allows either username or email to be entered for log in.
        $sql = "SELECT * FROM users WHERE user_uid='$uid' OR user_email = '$uid'";
        $result = mysqli_query($conn, $sql);
        $resultCheck = mysqli_num_rows($result);
        //Checks if login information is correct and matches username or email in database
        if ($resultCheck < 1){
            header("Location: ../index.php?login=error");
            exit();
        } else {
            if($row = mysqli_fetch_assoc($result)){
                //Checks if new password hashed matches hashed password in database.
                $hashedPwdCheck = password_verify($pwd, $row['user_pwd']);
                if ($hashedPwdCheck == false){
                    header("Location: ../index.php?login=error");
                    exit();
                } elseif ($hashedPwdCheck == true){ //If false value was not given, entry would be allowed even without true value if else statement is used
                    //User logged in and their credentials are assigned to the session cache.
                    $_SESSION['u_id'] = $row['user_id'];
                    $_SESSION['u_first'] = $row['user_first'];
                    $_SESSION['u_last'] = $row['user_last'];
                    $_SESSION['u_email'] = $row['user_email'];
                    $_SESSION['u_uid'] = $row['user_uid'];
                    //Updates URL to acknowledge the log in succes.
                    header("Location: ../index.php?login=success");
                    exit();

                }
            }
        }
    }
} else {
    header("Location: ../index.php?login=error");
    exit();

}
