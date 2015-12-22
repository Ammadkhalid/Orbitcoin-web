<?php
require_once "php/config.php";
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
    <title>Create Wallet For Orbitcoin</title>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.3/jquery.min.js"></script>
    
    
    
        <link rel="stylesheet" href="css/reg.css">
        <link rel="stylesheet" href="css/notifIt.css">
		<script src="js/notifIt.min.js"></script>
		<script src="js/notifIt.js"></script>
		
    
    
    
  </head>

  <body>

    
<link href='http://fonts.googleapis.com/css?family=Lato&subset=latin,latin-ext' rel='stylesheet' type='text/css'>

<div class="container">
  <div class="profile">
    <span class="profile__avatar" id="toggleProfile">
     <h3>Create Wallet For <span style="color:#478B16">Orb</span></h3>
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
		<div class="field">
          <input type="password" id="fieldCPassword" class="input" required pattern=.*\S.* />
          <label for="fieldCPassword" class="label">Confirm Password</label>
        </div>
		<div class="field">
          <input type="text" id="fieldEmail" class="input" required pattern=.*\S.* />
          <label for="fieldEmail" class="label">Email</label>
        </div>
		<div class="field">
		<!--<input type="hidden" name="capcode" id="capcode" value="false" />
		//<?php
		//if (!class_exists('KeyCAPTCHA_CLASS')) {
		////	include('php/keycaptcha.php');
		//}
		///$kc_o = new KeyCAPTCHA_CLASS();
		//echo $kc_o->render_js();
		//
		///?>
		</div> ---->
        <div class="profile__footer">
          <button class="btn">Create Wallet</button><br />
		  <a href="login.php" class="reg">Login Now?</a>
        </div>
      </div>
     </div>
  </div>
</div>
	
        <script src="js/reg.js"></script>

    
    
    
  </body>
</html>