<?php
//data required for databases to be accessesed, this document is called at the top of each document
$dbServername = "emps-sql.ex.ac.uk";
$dbUsername = "db581";
$dbPassword = "db581";
$dbName = "db581";

// makes connection to database
$conn = new mysqli($dbServername,$dbUsername,$dbPassword,$dbName);
// pepper to use for password hashing
$pepper = '7X8MSO2ubF`Widl';
