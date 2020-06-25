<!DOCTYPE html>
<?php
	include_once 'header.php';

	include_once 'dbh.inc.php'

  ?>
    <section class="main-container">
        <div class= "main-wrapper">


          <?php
            if(!isset($_COOKIE[$cookie_name])){
              echo "<p>Current mode not set!</p>";
            } else {
              echo "<p>Current Mode: " . $_COOKIE[$cookie_name]."</p>";
            }
				  ?>
					<!-- Selection for light or dark mode -->
					<p><br><font size=6>Select mode:</font></p><br>
          <form action= setCookie.php method='POST'>
            <input type='radio' id='Light' name='mode' value='light'>
            <label for='Light'>Light Mode</label><br>
            <input type='radio' id='Dark' name='mode' value='dark'>
            <label for='Dark'>Dark Mode</label><br>
            <br>
          <button type= 'submit' name='submit' href = 'account.php'>Update</button>
          </form>


        </div>
    </section>
<?php
include_once 'footer.php';
?>
