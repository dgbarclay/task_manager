<?php
session_start();

include_once 'dbh.inc.php';

if (isset($_POST['submit'])) {
  // prevent SQL injection
  $uid = mysqli_real_escape_string($conn, $_POST['uid']);
  $pwd = mysqli_real_escape_string($conn, $_POST['pwd']);

  if (empty($uid)||empty($pwd)) {
    header("Location: ../db581/index.php?login=empty");
    exit();
  }
  else{
    // prepared statements to prevent SQL injection
    $stmt = $conn->prepare("SELECT user_id, user_first, user_last, user_email,
       user_uid, user_pwd, salt FROM users WHERE user_uid = ? OR user_email = ? ");
    $stmt->bind_param("ss", $uid, $uid);
    $stmt->execute();
    $stmt->bind_result($user_id, $user_first, $user_last, $user_email, $user_uid, $user_pwd, $salt);

    while($stmt->fetch()) {
      // salt and pepper hash on password
      $hashedPwdCheck = md5($salt . $pwd . $pepper);
      if ($hashedPwdCheck != $user_pwd) {
          header("Location: ../db581/index.php?login=error");
          exit();
      } else {
          // assign user credentials to session
          $_SESSION['u_id'] = $user_id;
          $_SESSION['u_first'] = $user_first;
          $_SESSION['u_last'] = $user_last;
          $_SESSION['u_email'] = $user_email;
          $_SESSION['u_uid'] = $user_uid;
          header("Location: ../db581/index.php?login=success");
          exit();
      }
      break;
    }
    header("Location: ../db581/index.php?login=error");
    exit();
  }
}
else {
  header("Location: ../db581/index.php?login=error");
  exit();
}

?>
