<?php
  // allows user ID to be stored for access throughout website.
  session_start();
?>
  <!-- default black style for page -->
  <link id="pagestyle" rel="stylesheet" type="text/css" href="style.css">
  <!-- function to update style sheer with dark or light mode -->
  <script>
    function darkTheme() {
    var theme = document.getElementById('pagestyle');
    theme.href = "style.css";
    }

    function lightTheme() {
      var theme = document.getElementById('pagestyle');
      theme.href = "styleLight.css";
    }

  </script>

<?php
  $cookie_name = "mode";
  $cookie_value = "dark"; //dark mode by default
?>
<!DOCTYPE html>
<html>
<head>

</head>
<body>
    <header>
      <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <nav>
            <div class="main-wrapper">
              <!-- update cookies -->
              <?php if(!isset($_COOKIE[$cookie_name])) { ?>
                      <p></p>
              <?php }elseif ($_COOKIE[$cookie_name] == "light"){?>
    							<script>lightTheme();</script>
    						<?php } else{?> <!-- Default mode is dark-->
    				    <script>darkTheme();</script>
    				  <?php }?>


<div class= "nav-login"> <!--feference to the CSS for navigation bar. -->
<?php
  // layout depends on whether user is logged in
  if (isset($_SESSION['u_id'])){
      echo '<form action = "logout.inc.php" method = "POST">
      <button type="submit" name="submit">Logout</button>
      </form>
      <ul>
        <li><a href="index.php">Home</a></li>
        <li><a href="account.php">Account</a></li>
      </ul>';
  }else{
    // log in form
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
