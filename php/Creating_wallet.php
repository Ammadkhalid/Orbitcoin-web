<?php
require_once "config.php";
require_once "jsonRPCClient.php";
function Clean($string) {
	$string = mysql_real_escape_string($string);
	$string = htmlspecialchars($string);
	$string = strip_tags($string);
	return $string;
}
if(isset($_POST["Username"]) && isset($_POST["Email"]) && isset($_POST["Password"]) && isset($_POST["Cpassword"])) {
	if(!empty($_POST["Username"])) {
		$username = Clean(mysqli_real_escape_string($con, $_POST["Username"]));
		if(!ctype_alnum($username)) {
			echo "Username Must be in alphabet and numbers";
		}
		$sqli_for_chk_user = mysqli_query($con, "SELECT Username FROM users WHERE Username='".$username."'");
		if(mysqli_num_rows($sqli_for_chk_user) == 1) {
			echo "Username already taken!";
		} else {
			if(!empty($_POST["Password"])) {
				if(strlen($_POST["Password"]) >= 6) {
				$password = Clean(mysqli_real_escape_string($con, $_POST["Password"]));
				$password = sha1($password);
				if(!empty($_POST["Cpassword"])) {
					$cpassword = Clean($_POST["Cpassword"]);
					$cpassword = sha1($cpassword);
					if($password == $cpassword) {
						$email = Clean(mysqli_real_escape_string($con, $_POST["Email"]));
						if(!filter_var($email, FILTER_VALIDATE_EMAIL)) {
							echo "Email is incorect!";
						} else {
							/// for captcha
							//if (!class_exists('KeyCAPTCHA_CLASS')) {
							//include('../php/keycaptcha.php');
							//}
							//$kc_o = new KeyCAPTCHA_CLASS();
							//if ($kc_o->check_result($_POST['capcode'])) {
	
							//} else {
							
							//}
							
							$address = $coin->getaccountaddress($username);
							if($address) {
								$code = sha1(rand(0, 1000));
								$id = mysqli_insert_id();
								$create_wallet = mysqli_query($con, "INSERT INTO users (Username,Password,Email,Wallet_address,Code) VALUES ('".$username."','".$cpassword."','".$email."','".$address."', '".$code."')");
								$subject = "Do not reply Email Confirmation $username";
								$message = "This email has been sended by orb-web-wallet by clicking on the link to confirm your orbitcoin account wallet!
								http://localhost/test/login.php?hash=$code&id=$id
								";
								$mail = mail($email, $subject, $message);
								if($mail) {
									echo "Please Confirm Your Email!";
								}
							}
						}
					} else {
						echo "Password is not match!";
					}
				} else {
					echo "Please Enter Confirm Password!";
				}
				} else {
					echo "Enter Mini 6 Characters In Password!";
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
?>