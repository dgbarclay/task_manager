<?php

include_once 'header.php';

include_once 'dbh.inc.php';

$userID = $_SESSION['u_id'];
$userDataID = $_GET['userDataID'];
$completed = $_GET['completed'];

$myXMLBody = "<user>
							<id>$userDataID</id>
							</user>";

if ($completed == 'true'){
	$completed = 'Completed';
	$url = 'http://students.emps.ex.ac.uk/dm656/check.php/' . $userDataID;
}
else{
	$completed = 'Not yet completed';
	$url = 'http://students.emps.ex.ac.uk/dm656/uncheck.php/' . $userDataID;
}

$stmt = $conn->prepare("UPDATE userData SET completed = ?
 WHERE (user_dataid = ?) AND (user_id = ?)");

$stmt->bind_param("sii", $completed, $userDataID, $userID);
$stmt->bind_result($v1, $v2, $v3);
$result = $stmt->execute();

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
