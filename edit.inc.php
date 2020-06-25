<?php

include_once 'header.php';

// if (isset($_POST['submit'])) { //Only allows users to enter if they press button.

	include_once 'dbh.inc.php';

	if(isset($_SESSION['u_id'])){

		$userID = $_SESSION['u_id'];
	  $userDataID = $_REQUEST['userDataID']; //Taken from the url and assigned to a local variable.
		$delete = $_REQUEST['delete']; //Taken from the url and assigned to a local variable.

		if ($delete == 'True'){
			$sql = "DELETE FROM userData WHERE (user_dataid = '$userDataID') AND (user_id = '$userID')";

			$result = mysqli_query($conn, $sql);
			print($sql);
			if($result == TRUE){
				header("Location: ../index.php?delete=success");		//Updates URL with success message.
			}else{
				print " error in edit";
			}
		}
		else {
			$name = $_POST['name'];			//Takes user inputed values and assigns each to an identifier.
			$description = $_POST['description'];
			$date_due = $_POST['date_due'];
			$completed = $_POST['completed'];

			if(empty($userID)){					//Checks if the user is logged in before creating graph.
				header("Location: ../signup.php?user_not_logged_in");		//Updates url to display error message to user.
				exit();
			}
			else{
				if(empty($name) || empty($description) || empty($date_due) || empty($completed)){    //Checks if any user fields are left empty and displays relevent error message to the user.
					header("Location: ../index.php?userDataID=$userDataID&data=empty");
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
					$sql = "UPDATE userData SET user_id = '$userID',
		      name = '$name', description = '$description', date_due = '$date_due', completed = '$completed'
		       WHERE (user_dataid = '$userDataID') AND (user_id = '$userID')";

		      // $sql = ("INSERT INTO userData (user_id, name, description, date_due)
		      // VALUES ($userID, AES_ENCRYPT('$name','$key'), AES_ENCRYPT('$description','$key'),
		      //  AES_ENCRYPT('$date_due','$key'))");

		      $result = mysqli_query($conn, $sql);
		      print($sql);
		      if($result == TRUE){
		        header("Location: ../index.php?edit=success");		//Updates URL with success message.
		      }else{
		        print " error in edit";
		      }

					}

				}
	  	}
	  }
// }
//
// else{
// 	header("Location: ../signup.php?user_id_unavailable"); 	//If not logged in.
// 		exit();
// }
