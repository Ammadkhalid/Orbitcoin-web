<?php
require_once "php/config.php";
function Clean($string) {
	$string = mysql_real_escape_string($string);
	$string = htmlspecialchars($string);
	return $string;
}

if(isset($_GET["hash"]) && isset($_GET["id"])) {
	$code = Clean(mysqli_real_escape_string($con, ($_GET["hash"])));
	$id = Clean(mysqli_real_escape_string($con, $_GET["id"]));
	$yes = Clean(mysqli_real_escape_string($con, "Yes"));
	$sqli_chk = mysqli_query($con, "SELECT ID FROM users WHERE Code='".$code."' AND ID='".$id."'");
	if(mysqli_num_rows($sqli_chk) == 1) {
		$sqli_up = mysqli_query($con, "UPDATE users SET Email_confirm='".$yes."' WHERE Code='".$code."' AND ID='".$id."'");
		$sqli_up = mysqli_query($con, "UPDATE users SET Code=Null WHERE Code='".$code."' AND ID='".$id."'");
		if($sqli_up) {
			echo "<script>alert('ok!')</alert>";
		}
	} else {
		echo "<script>alert('ERROR')</alert>";
	}
}

session_start();
if(isset($_SESSION["Usr_c"])) {
	$session = $_SESSION["Usr_c"];
	$sqli_chk_session_true = mysqli_query($con, "SELECT Session_id FROM users WHERE Session_id='".$session."'");
	if(mysqli_num_rows($sqli_chk_session_true) == 1) {
		header("location:wallet.php");
	} else {
		session_destroy();
	}
}
?>
<!DOCTYPE html>
<html >
  <head>
    <meta charset="UTF-8">
	<link rel="icon"  type="image/png" href="https://github.com/ghostlander/Orbitcoin/blob/master/src/qt/res/icons/orbitcoin.png?raw=true"/>
    <title>Wallet Login</title>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.3/jquery.min.js"></script>
    
    
    
        <link rel="stylesheet" href="css/login.css">
        <link rel="stylesheet" href="css/notifIt.css">
		<script src="js/notifIt.min.js"></script>
		<script src="js/notifIt.js"></script>
    
    
    
  </head>

  <body>

    
<link href='http://fonts.googleapis.com/css?family=Lato&subset=latin,latin-ext' rel='stylesheet' type='text/css'>

<div class="container">
  <div class="profile">
    <span class="profile__avatar" id="toggleProfile">
     <img src="img/user.jpg" alt="Avatar" /> 
    </span>
    <div class="profile__form">
      <div class="profile__fields">
        <div class="field">
          <input type="text" id="fieldUser" class="input" required pattern=.*\S.* />
          <label for="fieldUser" class="label">Username</label>
        </div>
        <div class="field">
          <input type="password" id="fieldPassword" class="input" required pattern=.*\S.* />
          <label for="fieldPassword" class="label">Password</label>
        </div>
        <div class="profile__footer">
          <button class="btn">Login</button>
		  <a href="forgot.php" class="forgot">Forgot Password ?</a><br />
		  <a href="reg.php" class="reg">Create an wallet</a>
        </div>
      </div>
     </div>
  </div>
</div>

    <script src="js/login.js"></script>
    
    
  </body>
</html>
