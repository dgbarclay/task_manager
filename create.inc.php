<?php

include_once 'header.php';

if (isset($_POST['submit'])) { //Only allows users to enter if they press button.

	include_once 'dbh.inc.php';

	if(isset($_SESSION['u_id'])){

	$userID = $_SESSION['u_id'];
	$name = $_POST['name'];			//Takes user inputed values and assigns each to an identifier.
	$description = $_POST['description'];
	$date_due = $_POST['date_due'];
	$completed = 'Not yet completed';

	if(empty($userID)){					//Checks if the user is logged in before creating graph.
		header("Location: ../signup.php?user_not_logged_in");		//Updates url to display error message to user.
		exit();
	}
	else{
		if(empty($name) || empty($description) || empty($date_due)){    //Checks if any user fields are left empty and displays relevent error message to the user.
			header("Location: ../index.php?data=empty");
			exit();
		}
		else {
			$name = checkForAppostrophe($name);
			$description = checkForAppostrophe($description);
			$date_due = checkForAppostrophe($date_due);

			//Calls function to replace single qoutes with a similar unicode equivalent to prevent SQL injection by escaping statements with single qoutes.

			//User data encrypted BEFORE entering into database, removes the need for prepared statements as SQL injection not possible with encrypted data.
			//If SQL injection attempted, PHP code will be encrypted and will not be able to be ran inside the database.

			//$sql = ("INSERT INTO userData (user_id, name, description, date_due) VALUES ($userID, AES_ENCRYPT('$name','$key'), AES_ENCRYPT('$description','$key'), AES_ENCRYPT('$date_due','$key')");
			$sql = ("INSERT INTO userData (user_id, name, description, date_due, completed) VALUES
			('$userID', '$name', '$description', '$date_due', '$completed')");

			// $sql = ("INSERT INTO userData (user_id, name, description, date_due)
			// VALUES ($userID, AES_ENCRYPT('$name','$key'), AES_ENCRYPT('$description','$key'),
			//  AES_ENCRYPT('$date_due','$key'))");

			$result = mysqli_query($conn, $sql);
			print($sql);
			if($result == TRUE){
				header("Location: ../index.php?create=success");		//Updates URL with success message.
			}else{
				print " error";
			}

			}

		}

	}

}

else{
	header("Location: ../signup.php?user_id_unavailable"); 	//If not logged in.
		exit();
}
