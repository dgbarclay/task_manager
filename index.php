<!-- index.php acts as the view controller, data is taken away from this page,
calculated and returned. The user will only ever see the index page after they
have logged in, unless they choose to change user preferences.
This aims to address the architecture requirements as the database code is
seperate from index.php, and only code used to display data remains on this page. -->
<?php
	include_once 'header.php';

	include_once 'dbh.inc.php';
?>
	<!-- main wrapper for responsive css -->
  <section class="main-container">
    <div class= "main-wrapper">
			<!-- script to change status using XML -->
			<!-- Requirement for client side scripting -->
			<script>
				function editStatus(id, state) {
				  var xhttp = new XMLHttpRequest();
					xhttp.onreadystatechange = function() {
				    if (this.readyState == 4 && this.status == 200) {
							var element = "state" + id;
							if (state == true){
							document.getElementById(element).innerHTML = "Completed";
							}
							else{
								document.getElementById(element).innerHTML= "Not yet completed";
							}
				    }
				  };
				  xhttp.open("GET", "/db581/state.php?userDataID="+id+"&completed="+state, true);
				  xhttp.send();
				}
			</script>

<?php
// Layout depending on whether user is logged in
	if(isset($_SESSION['u_id'])) {
		$username = htmlspecialchars($_SESSION['u_uid']);
	  $userID = $_SESSION['u_id'];
	  echo '<p><font size = 6><br>';
	  echo $username;
	  echo', create new task:<br></font></p>';
?>
  <form class="signup-form" action="create.inc.php" method="POST">
  <input type="string" name="name" placeholder="Name">
  <input type="string" name="description" placeholder="Description">
  <input type="date" name="date_due" placeholder="Date Due">
	<button type= "submit" name="submit" href = "index.php">Create</button>
  </form>

	<h1><br><br></h1>
	<table class='center'>
	<tr>
	    <th>Name</th>
	    <th>Description</th>
	    <th>Date Due</th>
			<th>Status</th>
			<th>Edit</th>
			<th>Remove</th>
	</tr>
	<p><font size = 5>Current Tasks:<br><br></font></p>

	<?php
	// list every task currently in database with user ID
	$userID = $_SESSION['u_id'];
	$sql = "SELECT user_dataid FROM userData WHERE user_id = $userID";
	$result = $conn->query($sql);
	$dataIDarray = array();
	$completedOnAPI = array();

	while ($row = $result->fetch_assoc()){
	    array_push($dataIDarray, $row['user_dataid']);
	}
	// loop through to print into table and exception handle
	for($counter=0; $counter < count($dataIDarray); $counter++){

    $userDataID = $dataIDarray[$counter];
    $userID = $_SESSION['u_id'];

		$sql = "SELECT name, description, date_due, completed FROM userData
		WHERE user_id = '$userID' AND user_dataid = '$userDataID'";

		$result = $conn->query($sql);

    if($row = $result->fetch_assoc()){
				// prevent XSS
        $name = htmlspecialchars($row['name']);
        $description = htmlspecialchars($row['description']);
        $date_due = htmlspecialchars($row['date_due']);
				$completed = htmlspecialchars($row['completed']);
				$delete = "/db581/edit.inc.php?userDataID=$userDataID&delete=True";
    }
    else{
        exit();
    }
		// check to see whether a task has been completed by someone else using
		// web service application that is not local
		if ($completed != 'Completed'){
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
		// error code when task is unchecked and is unchecked again
		if ($response != 401){
			array_push($completedOnAPI,
					"<p><font size = 4><br>The task '$name', has been completed by
					someone else, click to remove it : <a href=$delete><img src='delete.png' width='15' height='15'></a>
					</font></p>
					");
		}

		// print task data
		echo "<tr>
    <td>$name</td>
    <td>$description</td>
    <td>$date_due</td>
		<td><div id='state$userDataID'>$completed</div></td>
		";

		// create unique pop up box for every task
		echo "<td><a href='#popup$counter'><img alt='Edit' src='edit.png' width='15' height='15'></a></td>
		<td>
		<a href=$delete><img src='delete.png' width='15' height='15'></a>
		</td>
		</tr>";
		echo "
		<div id='popup$counter' class='overlay'>
		<div class='popup'>
		<a class='close' href='#'>&times;</a>
		<div class='content'>
		<p><br><font size = 6><br>Change Status:</font></p>
		<p><br></p>";

		$false = 'false';
		$true = 'true';

		// edit status of task using XML
		echo "
		<form>
			<button onclick='editStatus($userDataID, $true)'><font size = 3>Done</font></button>
			<br>
			<button onclick='editStatus($userDataID, $false)'><font size = 2>Not Done</font></button>
		</form>";
		// edit task
		echo "
		<p><font size = 6><br>Update Values:</font></p>
		<form class='signup-form' action='/db581/edit.inc.php' method='POST'>
			<input type='string' name='name' placeholder= '$name' >
			<input type='string' name='description' placeholder= '$description' >
			<input type='date' name='date_due' placeholder= '$date_due' ><br>
			<input type='radio' id='True' name='completed' value='Completed'>
			<label for='True'>Completed</label>
			<input type='radio' id='False' name='completed' value='Not yet completed'>
			<label for='False'>Not yet completed</label><br><br>
			<input type='hidden' name='userDataID' value= '$userDataID'>
			<input type='hidden' name='delete' value= 'False'>
		<button type= 'submit' name='submit' href = 'index.php'>Edit</button>
		</form>
		</div>
		</div>
		</div>";
  }
	echo "</table>";
	foreach($completedOnAPI as $item){
		echo $item;
	}
?>
<!-- import or export pop up box -->
<br>
<button onclick="window.location.href= '#popup';">Import</button>
<br>
<button onclick="window.location.href= '#popupExport';">Export</button>

<?php
		echo "
		<div id='popup' class='overlay'>
		<div class='popup'>
			<a class='close' href='#'>&times;</a>
			<div class='content'>
				<p><br><font size = 6><br>Import:</font></p>
				<br>

				<table>
		    <tr>
		        <th>Select</th>
		        <th>ID</th>
		        <th>Name</th>
		        <th>Description</th>
		        <th>Date Due</th>
				</tr>
				<form action='/db581/import.php' method='POST' onsubmit=\"return confirm('Are you sure you want to import?');\">
				";

		$context = stream_context_create(array('http'=>array(
		    'method' => 'GET'
		)));
		$returnData = file_get_contents('http://students.emps.ex.ac.uk/dm656/tasks.php', false, $context);
		$array = explode("</task>",$returnData);
		// get data from longer XML string
		foreach ($array as $item){
			$date = explode("</name>",$item);
			$date_new = explode("<due>",$date[1]);

			$name = explode("</name>",$item);
			$name_new = explode("<name>",$name[0]);

			$id = explode("</id>",$item);
			$id_new = explode("<id>",$id[0]);
			$id_newer = explode("</id>",$id_new[1]);
			// get description from task list
			$context = stream_context_create(array('http'=>array(
			    'method' => 'GET'
			)));
			$url = 'http://students.emps.ex.ac.uk/dm656/task.php/' . $id_newer[0];  //RESTful API
			$newInfo = file_get_contents($url, false, $context);

			$description = explode("</description>",$newInfo);
			$description_new = explode("<description>", $description[0]);
			// print data form RESTful web application
			echo "
			<tr>
			<td><input type='checkbox' name='id[]' id='id' value='$id_newer[0]'></td>
	    <td>$id_newer[0]</td>
	    <td>$name_new[1]</td>
			<td>$description_new[1]</td>
	    <td>$date_new[1]</td>
			</tr>
			";
		}

		echo "</table>
					<br>
					<button type= 'submit' name='submit' href='index.php'>Import</button>
					</form>
				</div>
			</div>
		</div>";
		// export pop up box with confirmation box
		echo "
		<div id='popupExport' class='overlay'>
		<div class='popup'>
			<a class='close' href='#'>&times;</a>
			<div class='content'>
				<p><br><font size = 6><br>Export:</font></p>
				<p><br></p>
				<table>
		    <tr>
		        <th>Select</th>
		        <th>ID</th>
		        <th>Name</th>
		        <th>Description</th>
		        <th>Date Due</th>
				</tr>
				<form action='/db581/export.php' method='POST' onsubmit=\"return confirm('Are you sure you want to export?');\">
				";
		$sql = "SELECT user_dataid FROM userData WHERE user_id = $userID";
		$result = $conn->query($sql);
		$dataIDarray = array();

		while ($row = $result->fetch_assoc()){
		    array_push($dataIDarray, $row['user_dataid']);
		}
		// get tasks from web application
		for($counter=0; $counter < count($dataIDarray); $counter++){

		    $userDataID = $dataIDarray[$counter];
		    $userID = $_SESSION['u_id'];

				$sql = "SELECT name, description, date_due FROM userData
				WHERE user_id = '$userID' AND user_dataid = '$userDataID'";

				$result = $conn->query($sql);

		    if($row = $result->fetch_assoc()){
		        $name = htmlspecialchars($row['name']);
		        $description = htmlspecialchars($row['description']);
		        $date_due = htmlspecialchars($row['date_due']);
		    }
		    else{
		        exit();
		    }
				echo "<tr>
				<td><input type='checkbox' name='id[]' id='id' value='$userDataID'></td>
				<td>$userDataID</td>
		    <td>$name</td>
		    <td>$description</td>
		    <td>$date_due</td>
				</tr>
				";
		    }
			echo "</table>
						<br>
					 <button type= 'submit' name='submit' href='index.php'>Export</button>
					</form>
				</div>
			</div>
		</div>
			";
			$userID = $_SESSION['u_id'];
			$sql = "SELECT user_dataid FROM userData WHERE user_id = $userID";
			$result = $conn->query($sql);
			$dataIDarray = array();

			while ($row = $result->fetch_assoc()){
			    array_push($dataIDarray, $row['user_dataid']);
			}
	}
	// if user not logged in
	else{
	    echo '<p><font size = 6><a href="signup.php">Register<br><br></a></font></p>';
			echo '<p><font size = 4>or log in...</font></p>';
			echo '<form class="signup-form" action = "login.inc.php" method = "POST">
	    <input type = "text" name= "uid" placeholder = "Username/e-mail">
	    <input type = "password" name= "pwd" placeholder = "Password">
	    <button type = "submit" name="submit">Login</button>
	    </form>';
	}
?>

<br></br>
</div>
</section>

<?php
	include_once 'footer.php';
?>
