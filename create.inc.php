<?php

include_once 'header.php';

if (isset($_POST['submit'])) { //Only allows users to enter if they press button.

	include_once 'dbh.inc.php';

	if(isset($_SESSION['u_id'])){
		// gather data submitted
		$userID = $_SESSION['u_id'];
		$name = $_POST['name'];
		$description = $_POST['description'];
		$date_due = $_POST['date_due'];
		$completed = 'Not yet completed';
		// check if user is logged in
		if(empty($userID)){
			header("Location: ../db581/signup.php?user_not_logged_in");		// update url to display error message to user
			exit();
		}
		else{
			if(empty($name) || empty($description) || empty($date_due)){    // check if any user fields are empty
				header("Location: ../db581/index.php?data=empty");
				exit();
			}
			else {
				// prevent sql injection by checking inputs
				$name = mysqli_real_escape_string($conn, $name);
				$description = mysqli_real_escape_string($conn, $description);
				$date_due = mysqli_real_escape_string($conn, $date_due);

				if ($conn->connect_error){
					die ("Connection failure: ".$conn->connect_error);
				}
				// prepared statement initialised
				$stmt = $conn->prepare("INSERT INTO userData (user_id, name, description, date_due, completed)
				 VALUES (?, ?, ?, ?, ?)");

				$stmt->bind_param("issss", $userID, $name, $description, $date_due, $completed);
				$stmt->bind_result($v1, $v2, $v3, $v4, $v5);
				$result = $stmt->execute();

				if($result == TRUE){
					header("Location: ../db581/index.php?create=success");		// updates URL with success message.
				}else{
					print(" oh no");
				}

			}

		}

	}

}

else{
	header("Location: ../db581/signup.php?user_id_unavailable"); 	//If not logged in.
		exit();
}
