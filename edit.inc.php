<?php

include_once 'header.php';

include_once 'dbh.inc.php';

if(isset($_SESSION['u_id'])){
	// gather data depending on input type
	if (isset($_POST['submit'])) {
		$userID = $_SESSION['u_id'];
		$delete = $_POST['delete'];
		$userDataID = $_POST['userDataID'];
		$name = $_POST['name'];
		$description = $_POST['description'];
		$date_due = $_POST['date_due'];
		$completed = $_POST['completed'];
	}
	else{
		$userID = $_SESSION['u_id'];
		$delete = $_GET['delete'];
		$userDataID = $_GET['userDataID'];
		$name = $_GET['name'];
		$description = $_GET['description'];
		$date_due = $_GET['date_due'];
		$completed = $_GET['completed'];
	}

	if ($delete == 'True'){
		// delete task from database
		$sql = "DELETE FROM userData WHERE (user_dataid = '$userDataID') AND (user_id = '$userID')";
		$result = $conn->query($sql);

		if($result == TRUE){
			// updates URL with success message.
			header("Location: ../db581/index.php?delete=success");
		}else{
			header("Location: ../db581/index.php?delete=failure");
		}
	}
	else {
		if(empty($userID)){
			header("Location: ../db581/signup.php?user_not_logged_in");		//Updates url to display error message to user.
			exit();
		}
		else{
			if(empty($name) || empty($description) || empty($date_due) || empty($completed)){    //Checks if any user fields are left empty and displays relevent error message to the user.
				header("Location: ../db581/index.php?userDataID=$userDataID&data=empty");
				exit();
			}
			else {
				// prevent sql injection
				$name = mysqli_real_escape_string($conn, $name);
				$description = mysqli_real_escape_string($conn, $description);
				$date_due = mysqli_real_escape_string($conn, $date_due);
				// initiate prepared statement
				$stmt = $conn->prepare("UPDATE userData SET user_id = ?,
	      name = ?, description = ?, date_due = ?, completed = ?
	       WHERE (user_dataid = ?) AND (user_id = ?)");

				$stmt->bind_param("issssii", $userID, $name, $description, $date_due, $completed, $userDataID, $userID);
	      $stmt->bind_result($v1, $v2, $v3, $v4, $v5, $v6, $v7);
				$result = $stmt->execute();

	      if($result == TRUE){
					if ($completed == 'Completed'){
						$url = 'http://students.emps.ex.ac.uk/dm656/check.php/' . $userDataID;
					}
					else{
						$url = 'http://students.emps.ex.ac.uk/dm656/uncheck.php/' . $userDataID;
					}
					$myXMLBody = "<user>
												<id>$userDataID</id>
												</user>";
					$curl = curl_init($url);
					curl_setopt($curl, CURLOPT_HTTPHEADER, array("Content-Type: text/xml"));
					curl_setopt($curl, CURLOPT_URL, $url);
					curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "PUT");
					curl_setopt($curl, CURLOPT_POSTFIELDS, $myXMLBody);
					curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
					curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 300);
					$result = curl_exec($curl);
					$response = curl_getinfo($curl, CURLINFO_HTTP_CODE);
					curl_close($curl);
					if ($response == 409){
						header("Location: ../db581/index.php?edit=success&error=alreadycompleted");
					}
	        else if ($response == 400){
						header("Location: ../db581/index.php?edit=success&error=wrongidentifier");
					}
					else if ($response == 404){
						header("Location: ../db581/index.php?edit=success&error=notfound");
					}
					else if ($response == 405){
						header("Location: ../db581/index.php?edit=success&error=wrongmethod");
					}
					else if ($response == 401){
						header("Location: ../db581/index.php?edit=success&error=notcompleted");
					}
					else{
						header("Location: ../db581/index.php?edit=success");
					}
	      }else{
	        header("Location: ../db581/index.php?edit=failure");
	      }
			}
		}
	}
}
else{
	header("Location: ../db581/signup.php?user_not_logged_in");
	exit();
}
