<?php

if (isset($_POST['submit'])) { //Only allows users to enter if they press button

	include_once 'dbh.inc.php';

	$first =$_POST['first']; //Prevents php code being entered and ran on database
	$last =$_POST['last'];
	$email =$_POST['email'];
	$uid = $_POST['uid'];
	$pwd = $_POST['pwd'];

	//Exception handling
	//Checks for any empty fields
	if(empty($first) || empty($last) || empty($email)|| empty($uid)|| empty($pwd)){
		header("Location: ../signup.php?signup=empty");
		exit();
	}else {
		//Check if input characters are valid.
		if (!preg_match("/^[a-zA-Z]*$/", $first)|| !preg_match("/^[a-zA-Z]*$/", $last ) || !preg_match("/^[a-zA-Z]*$/", $uid )){
			header("Location: ../signup.php?signup=invalid");
			exit();

		}else {
			//Check if email is valid, contains @.
			if(!filter_var($email, FILTER_VALIDATE_EMAIL)){
				header("Location: ../signup.php?signup=invalidEmail");
				exit();
			}
				else{
					$sql = "SELECT * FROM users WHERE user_uid= '$uid'";
					$result = mysqli_query($conn,$sql);
					$resultCheck = mysqli_num_rows($result);
					//Checks if username already exists in database.
					if ($resultCheck > 0){
						header("Location: ../signup.php?signup=usernameTaken");
						exit();
					}
					else{
							$sql = "SELECT * FROM users WHERE user_email= '$email'";
							$result = mysqli_query($conn,$sql);
							$resultCheck = mysqli_num_rows($result);
							//Checks if email already exists in database.
							if ($resultCheck > 0){
								header("Location: ../signup.php?signup=emailtaken");
								exit();
							}
						else{
						//Hashing of user password using password_hash fucntion and PASSWORD_DEFAULT algorithm.
						$hashedPwd = password_hash($pwd, PASSWORD_DEFAULT);
						//Insert the user into database.
						$sql = "INSERT INTO users (user_first, user_last, user_email, user_uid, user_pwd) VALUES ('$first', '$last', '$email', '$uid', '$hashedPwd');";
						$result = mysqli_query($conn, $sql);
						print $sql;
						//IF successful, user then redirected to login page to log into website.
						if($result == TRUE){
							header("Location: ../login.php");
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
else{

	header("Location: ../signup.php");
	exit();
}
