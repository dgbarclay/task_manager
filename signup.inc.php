<?php

if (isset($_POST['submit'])) {

	include_once 'dbh.inc.php';

	$first =$_POST['first']; //Prevents php code being entered and ran on database
	$last =$_POST['last'];
	$email =$_POST['email'];
	$uid = $_POST['uid'];
	$pwd = $_POST['pwd'];

	// exception handling
	// check for any empty fields
	if(empty($first) || empty($last) || empty($email)|| empty($uid)|| empty($pwd)){
		header("Location: ../db581/signup.php?signup=empty");
		exit();
	}else{
		// check if username is the same as password
		if (strtoLower($pwd) == strtoLower($uid) || strtoLower($pwd) == strtoLower($email)){
			header("Location: ../db581/signup.php?password=username");
			exit();
		}else{
		// check if input characters are valid
			if (!preg_match("/^[a-zA-Z]*$/", $first)|| !preg_match("/^[a-zA-Z]*$/", $last ) || !preg_match("/^[a-zA-Z]*$/", $uid )){
				header("Location: ../db581/signup.php?signup=invalid");
				exit();

			}else {
				// check if email is valid
				if(!filter_var($email, FILTER_VALIDATE_EMAIL)){
					header("Location: ../db581/signup.php?signup=invalidEmail");
					exit();
				}
				else{
					$sql = "SELECT * FROM users WHERE user_uid= '$uid'";
					$result = $conn->query($sql);
					// checks if username already exists in database
					if ($result->num_rows > 0){
						header("Location: ../db581/signup.php?signup=usernameTaken");
						exit();
					}
					else{
							$sql = "SELECT * FROM users WHERE user_email= '$email'";
							$result = $conn->query($sql);
							// checks if email already exists in database
							if ($result->num_rows > 0){
								header("Location: ../db581/signup.php?signup=emailtaken");
								exit();
							}
						else{
							// salt and pepper to hash password
							$salt = bin2hex(openssl_random_pseudo_bytes(5));
							$hashedPwd = md5($salt.$pwd.$pepper);
							// prevent sql injection
							$first = mysqli_real_escape_string($conn, $first);
							$last = mysqli_real_escape_string($conn, $last);
							$email = mysqli_real_escape_string($conn, $email);
							$uid = mysqli_real_escape_string($conn, $uid);

							$stmt = $conn->prepare("INSERT INTO users (user_first, user_last, user_email, user_uid, user_pwd, salt)
							VALUES (?, ?, ?, ?, ?, ?);");

							$stmt->bind_param("ssssss", $first, $last, $email, $uid, $hashedPwd, $salt);
							$stmt->bind_result($v1, $v2, $v3, $v4, $v5, $v6, $v7);
							$result = $stmt->execute();

							if($result == TRUE){
								header("Location: ../db581/index.php?register=successful");
							}else{
								print "error";
							}
							exit();
						}
					}
				}
			}
		}
	}
}
else{
	header("Location: ../db581/signup.php");
	exit();
}
