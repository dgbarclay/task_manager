<?php

include_once 'header.php';

include_once 'dbh.inc.php';

if(isset($_SESSION['u_id'])) {

  $userID = $_SESSION['u_id'];
  $id = $_POST['id'];

  if (count($id) == 0){
    header("Location: ../db581/index.php?export=noneselected");
    exit();
  }
  // for every item selected
  foreach($id as $item){
    $sql = "SELECT name, description, date_due FROM userData
    WHERE user_id = '$userID' AND user_dataid = '$item'";

    $result = $conn->query($sql);

    if($row = $result->fetch_assoc()){
      // prevent XSS
      $name = htmlspecialchars($row['name']);
      $description = htmlspecialchars($row['description']);
      $date_due = htmlspecialchars($row['date_due']);
    }
    else{
      exit();
    }
    // modify string to fit type
    if (strlen($date_due) < 12){
      $date_due = strval($date_due) . " 20:00:00";
    }
    // dynamically create xml
    $myXMLBody = "<taskinfo>
                  <name>$name</name>
                  <due>$date_due</due>
                  <description>$description</description>
                  </taskinfo>";

    $context = stream_context_create(array('http'=>array(
        'method' => 'POST',
        'content' => $myXMLBody
    )));
    $returnData = file_get_contents('http://students.emps.ex.ac.uk/dm656/add.php', false, $context);
  }
  header("Location: ../db581/index.php?export=success");
}
else{
   header("Location: ../db581/signup.php?user_not_logged_in");
   exit();
}
