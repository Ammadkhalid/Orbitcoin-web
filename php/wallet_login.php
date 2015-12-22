<?php
if(!isset($_SESSION["usr_c"])) {

function Clean($string) {
	$string = mysql_real_escape_string($string);
	$string = htmlspecialchars($string);
	$string = strip_tags($string);
	return $string;
}
require_once "config.php";
if(isset($_POST["Username"]) && isset($_POST["Password"])) {
	if(!empty($_POST["Username"])) {
		$username = Clean(mysqli_real_escape_string($con, $_POST["Username"]));
		$chk_user = mysqli_query($con, "SELECT Username FROM users WHERE Username='".$username."'");
		if(mysqli_num_rows($chk_user) != 1) {
			echo "Username Not Found!";
		} else {
			if(!empty($_POST["Password"])) {
				$password = Clean(mysqli_real_escape_string($con, $_POST["Password"]));
				$password = sha1($password);
				$chk_login = mysqli_query($con, "SELECT Username,Password,Session_id FROM users WHERE Username='".$username."' AND Password='".$password."'");
				if(mysqli_num_rows($chk_login) == 1) {
					$yes = Clean('Yes');
					$sql_chk_user_con = mysqli_query($con, "SELECT Username,Password,Session_id FROM users WHERE Username='".$username."' AND Password='".$password."' AND Email_confirm='".$yes."'");
					if(mysqli_num_rows($sql_chk_user_con) == 1) {
					session_start();
					$convert = mysqli_fetch_array($chk_login);
					$hash = substr(str_shuffle(str_repeat('abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!#$_=+:/,', 45)), 0, 45);
					
					$Update_session_id = mysqli_query($con, "UPDATE users SET Session_id='".$hash."' WHERE Username='".$username."' AND Password='".$password."'");
					if($Update_session_id) {
						$insert_session = $_SESSION["Usr_c"] = $hash;
						session_save_path("/");
						
						if($_SERVER["SERVER_PORT"] == 443) {
							$chk_http = True;
						} else {
							$chk_http = false;
						}
						
						session_set_cookie_params(0, "/", $_SERVER["SERVER_NAME"], $chk_http, TRUE);
						if($insert_session) {
							echo "Login Successful!";
						}
					}
					} else {
						echo "Email is not confirm!";
					}
				} else {
					echo "Incorrect Password!";
				}
			} else {
				echo "Please Enter Password!";
			}
		}
	} else {
		echo "Please Enter Username!";
	}
} else {
	echo "ERROR Please reload the page!";
}

} else {
	echo "Login Successful!";
}
?>