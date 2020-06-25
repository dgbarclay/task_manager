<?php

include_once 'header.php';

include_once 'dbh.inc.php';

if(isset($_SESSION['u_id'])) {

  $userID = $_SESSION['u_id'];
  $id = $_POST['id'];
  // if no task selected
  if (count($id) == 0){
    header("Location: ../db581/index.php?import=noneselected");
    exit();
  }
  // for every item selected
  foreach($id as $item){
    // new get request to get task data from id passed in
    $context = stream_context_create(array('http'=>array(
        'method' => 'GET'
    )));
    $url = 'http://students.emps.ex.ac.uk/dm656/task.php/' . $item;  //RESTful API
    $newInfo = file_get_contents($url, false, $context);
    // gets each element from xml
    $description = explode("</description>",$newInfo);
    $description_new = explode("<description>", $description[0]);

    $name = explode("</name>",$newInfo);
    $name_new = explode("<name>", $name[0]);

    $date = explode("</due>",$newInfo);
    $date_new = explode("<due>",$date[0]);

    $completed = 'Not yet completed';
    // sql injection prevention
    $name = mysqli_real_escape_string($conn, $name);
    $description = mysqli_real_escape_string($conn, $description);
    $date_due = mysqli_real_escape_string($conn, $date_due);

    $stmt = $conn->prepare("INSERT INTO userData (user_id, name, description, date_due, completed)
    VALUES (?, ?, ?, ?, ?)");

    $stmt->bind_param("issss", $userID, $name_new[1], $description_new[1], $date_new[1], $completed);
    $stmt->bind_result($v1, $v2, $v3, $v4, $v5);
    $result = $stmt->execute();

    if ($result != TRUE) {
       header("Location: ../db581/index.php?import=failed");
       break;
    }

    $myXMLBody = "<user>
    							<id>$item</id>
    							</user>";

    $url = 'http://students.emps.ex.ac.uk/dm656/uncheck.php/' . $item;
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
  }
  header("Location: ../db581/index.php?import=success");
}
else{
   header("Location: ../db581/signup.php?user_not_logged_in");		//Updates url to display error message to user.
   exit();
}
