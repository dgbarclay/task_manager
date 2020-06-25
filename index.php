<?php
	include_once 'header.php';

	include_once 'dbh.inc.php'
?>
    <section class="main-container">
        <div class= "main-wrapper">

            <?php
                //If logged in, user prompted to create graph.
                //If user not logged in, they are prompted to sign up.
                if(isset($_SESSION['u_id'])) {
										$username = $_SESSION['u_uid'];     //Gathers username and unique user ID from session cookies.
							      $userID = $_SESSION['u_id'];
							      echo '<p><font size = 6><br>';
							      //Includes username of user into text on the webpage.
							      echo $username;
							      echo', create new task:<br></font></p>';
							      ?>
							      <form class="signup-form" action="create.inc.php" method="POST">            <!-- Users can enter data here -->
							      <input type="string" name="name" placeholder="Name">
							      <input type="string" name="description" placeholder="Description">
							      <input type="date" name="date_due" placeholder="Date Due">
										<button type= "submit" name="submit" href = "index.php">Create</button>
							      </form>
							      <?
										echo "
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
										";

										echo ' <p><font size = 5>Current Tasks:<br><br></font></p>';
										//Gathers data for unique data ID primary key where the user_id of each column is equal to the logged in users' ID.
										$userID = $_SESSION['u_id'];
										$sql = "SELECT user_dataid FROM userData WHERE user_id = $userID";
										$result = mysqli_query($conn, $sql);
										$dataIDarray = array();


										while (($row = mysqli_fetch_assoc($result))){
										    //Pushes data ID of all instances where ID of user is present onto dataIDarray.
										    array_push($dataIDarray, $row['user_dataid']);
										}

										//Loops through dataIDarray and for each data ID, displays the data into the table.
										for($counter=0; $counter < count($dataIDarray); $counter++){

										    $userDataID = $dataIDarray[$counter];
										    $userID = $_SESSION['u_id'];

										    //Decrypts each encrypted data set using the same key stored in function.php.

										    // $sql = "SELECT AES_DECRYPT(user_variable, '$key') AS user_variable, AES_DECRYPT(user_frequency, '$key')
												//  AS user_frequency, AES_DECRYPT(user_title, '$key') AS user_title, AES_DECRYPT(user_frequencyType, '$key')
												//  AS user_frequencyType FROM userData WHERE user_id = $userID AND user_dataid = $userDataID"; //ORDER BY

												$sql = "SELECT name, description, date_due, completed FROM userData
												WHERE user_id = '$userID' AND user_dataid = '$userDataID'";

												$result = mysqli_query($conn, $sql);
										    $resultCheck = mysqli_num_rows($result);

										    if($row = mysqli_fetch_assoc($result)){
										        $name = $row['name'];
										        $description = $row['description'];
										        $date_due = $row['date_due'];
														$completed = $row['completed'];

												$location = "edit.inc.php?userDataID=$userDataID&delete=False";
												$delete = "/edit.inc.php?userDataID=$userDataID&delete=True";
										        //Seperates each value from comma seperated list, creates an array with values.
										    }
										    else{
										        exit();
										    }
												echo "<tr>
								        <td>$name</td>
								        <td>$description</td>
								        <td>$date_due</td>
												<td>$completed</td>";
												//Assigns URL to view.png for each entry into the table.
												echo "<td><a href='#popup1'><img alt='Edit' src='edit.png' width='15' height='15'></a></td>
												<td><a href =$delete ><img alt='Delete' src='delete.png' width='15' height='15'></td>
												</tr>";
												echo "</table>";

												echo "
												<div id='popup1' class='overlay'>
												<div class='popup'>
													<a class='close' href='#'>&times;</a>
													<div class='content'>
														<p><br><font size = 6><br>Change Status:</font></p>
														<p><br></p>
														<form action= $location method='POST'>
															<input type='radio' id='True' name='completed' value='Completed'>
															<label for='True'>Completed</label><br>
															<input type='radio' id='False' name='completed' value='Not yet completed'>
															<label for='False'>Not yet completed</label><br>
															<input type='hidden' name='name' value= '$name' >
															<input type='hidden' name='description' value= '$description' >
															<input type='hidden' name='date_due' value= '$date_due' >
															<br>
														<button type= 'submit' name='submit' href = 'index.php'>Edit</button>
														</form>
														
														<p><font size = 6><br>Update Values:</font></p>
														<form class='signup-form' action= $location method='POST'>
															<input type='string' name='name' placeholder= '$name' >
															<input type='string' name='description' placeholder= '$description' >
															<input type='date' name='date_due' placeholder= '$date_due' >
															<input type='radio' id='True' name='completed' value='Completed'>
															<label for='True'>Completed</label>
															<input type='radio' id='False' name='completed' value='Not yet completed'>
															<label for='False'>Not yet completed</label><br><br>
														<button type= 'submit' name='submit' href = 'index.php'>Edit</button>
														</form>
													</div>
												</div>
											</div>
												";




										    }
										    //Table ends.
                }
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
            <br>
					</div>
    </section>
<?php
	include_once 'footer.php';
?>
