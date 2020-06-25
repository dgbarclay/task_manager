<?php
    session_start(); //Begins session, allows user ID to be stored for access throughout website.

    //Key for encryption of all data in database, robust enough to keep web hosting account secure.
    $key = '7X8MSO2ubF`Widl';
    //Key updated in one place and applies to all encryption used throughout website.

    function checkForAppostrophe($variable){
    $variable = str_replace("'", "ʼ", $variable); //Replaces appostrophe with similar unicode replacement to avoid escaping SQL commands for statements containing any appostrophes.
    return $variable;
    }

?>


<!DOCTYPE html>
<html>
<head>


    <!--Reference to the library chart.js used to create graphs -->
    <script src= "https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.7.2/Chart.min.js"></script>
    <link rel="stylesheet" type= "text/css" href="style.css">

</head>
<body>
    <header>
        <nav>
            <div class="main-wrapper">



<div class= "nav-login"> <!--Reference to the CSS for navigation bar. -->
<?php
    //Checks if user is logged in and determines the layout and contents of the webpage
    //If user not logged in; Home, Create and Sign Up pages are displayed aswell as login option in centre of navigation bar.
    //If user logged in; Home, Create and Saved pages are displayed.
    if (isset($_SESSION['u_id'])){
        echo '<form action = "logout.inc.php" method = "POST">
            <button type="submit" name="submit">Logout</button>
            </form>
            <ul>
              <li><a href="index.php">Home</a></li>
            </ul>';
    }else{
        echo '<form action = "login.inc.php" method = "POST">
        <input type = "text" name= "uid" placeholder = "Username/e-mail">
        <input type = "password" name= "pwd" placeholder = "Password">
        <button type = "submit" name="submit">Login</button>
        </form>
        <ul>
          <li><a href="index.php">Home</a></li>
          <li><a href="signup.php">Register</a></li>
        </ul>';
    }
?>

        </nav>

    </header>
