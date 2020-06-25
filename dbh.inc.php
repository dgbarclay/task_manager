<?php
//Data required for databases to be accessesed, this document is called at the top of each document.
$dbServername = "localhost";
$dbUsername = "root";
$dbPassword = "";  //Real database will contain secure password.
$dbName = "login";

//Makes connection to database.
$conn = mysqli_connect($dbServername,$dbUsername,$dbPassword,$dbName);
