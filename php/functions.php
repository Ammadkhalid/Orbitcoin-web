<?php
include "config.php";
require_once "jsonRPCClient.php";
session_start();
if(isset($_SESSION["Usr_c"])) {
	$session = $_SESSION["Usr_c"];
	$chk_session = mysqli_query($con, "SELECT Session_id FROM users WHERE Session_id='".$session."'");
	if(mysqli_num_rows($chk_session) == 1) {
		
function Clean($string) {
	$string = mysql_real_escape_string($string);
	$string = htmlspecialchars($string);
	return $string;
}

function Username($con, $session) {
	$sel_query_username = mysqli_query($con, "SELECT Username FROM users WHERE Session_id='".$session."' ");
	$username = mysqli_fetch_array($sel_query_username);
	echo $username["Username"]."!";
}

function Is_validate_address($coin, $orb_address_for_vali) {
	$chk = $coin->validateaddress($orb_address_for_vali);
	if($chk["isvalid"] == 1) {
		echo "Ok!";
	}
}

function chk_user_pass_during_send_payment($con, $user_input_pass_in_send_payment, $session) {
	$sqli_chk_sel = mysqli_query($con, "SELECT Username FROM users WHERE Session_id='".$session."' ");
	$username = mysqli_fetch_array($sqli_chk_sel);
	$username = $username["Username"];
	$sqli_chk_pass = mysqli_query($con, "SELECT Password FROM users WHERE Username='".$username."' AND Session_id='".$session."' AND Password='".$user_input_pass_in_send_payment."'");
	if(mysqli_num_rows($sqli_chk_pass) == 1) {
		echo "Ok!";
	}
}

function chk_amount($con, $coin,  $chk_amount, $session) {
	if(is_numeric($chk_amount)) {
		$payment = doubleval($chk_amount);
		$sqli_chk_user = mysqli_query($con, "SELECT Username FROM users WHERE Session_id='".$session."'");
		$user_data = mysqli_fetch_array($sqli_chk_user);
		$username = $user_data["Username"];
		$address = $coin->getaddressesbyaccount($username);
				$balance = 0;
				$list_accounts = $coin->listaddressgroupings();
				$count = $list_accounts;
				for($i=count($count)-1;$i>=0;$i--) {
				$llops = $count[$i];
					for($o=count($llops)-1;$o>=0;$o--) {
						for($adr=count($address)-1;$adr>=0;$adr--) {
							if($count[$i][$o][0] == $address[$adr]) {
							$balance += $count[$i][$o][1];
						}
					}
				}
			}
		if($balance == $payment) {
				echo "No fee to spend";
			} elseif($balance - 0.001 >= $payment) {
				echo "Ok!";
			}
	}
}

function send_payment($con, $coin, $orbitcoin_address, $amnount, $password_orb, $session) {
	$chk_address = $coin->validateaddress($orbitcoin_address);
	$password_orb = sha1($password_orb);
	if($chk_address["isvalid"] == 1) {
		if(is_numeric($amnount)) {
			$payment = floatval($amnount);
			$sqli_for_sel_usr_data = mysqli_query($con, "SELECT Username FROM users WHERE Session_id='".$session."'");
			$user_data = mysqli_fetch_array($sqli_for_sel_usr_data);
			$username = $user_data["Username"];
				$address = $coin->getaddressesbyaccount($username);
				$balance = 0;
				$list_accounts = $coin->listaddressgroupings();
				$count = $list_accounts;
				for($i=count($count)-1;$i>=0;$i--) {
				$llops = $count[$i];
					for($o=count($llops)-1;$o>=0;$o--) {
						for($adr=count($address)-1;$adr>=0;$adr--) {
							if($count[$i][$o][0] == $address[$adr]) {
							$balance += $count[$i][$o][1];
						}
					}
				}
			}
			
			if($balance == $payment) {
				echo "No fee to spend";
			} elseif($balance >= $payment + 0.001 ) {
				$settxfee = $coin->settxfee(0.001);
				$sqli_chk_pass = mysqli_query($con, "SELECT Password FROM users WHERE Session_id='".$session."' AND Username='".$username."' AND Password='".$password_orb."'");
				if(mysqli_num_rows($sqli_chk_pass) == 1) {
					$send = $coin->sendtoaddress($orbitcoin_address, doubleval($payment));
					if($send) {
						echo "Send! Tx id:"."<br />";
						echo "<a style='color:red;' href='atlas.phoenixcoin.org:1080/tx/".$send."' target=__blank>".$send.'</a>';
						$time = time();
						$sqli_insert = mysqli_query($con, "INSERT INTO send_transations (Account,To_address,Amount_send,Date_send,Tx_id) VALUES ('".$username."','".$orbitcoin_address."','".$payment."','".$time."','".$send."')");
					}
				} else {
					echo "Incorrect Password!";
				}
			} else {
				echo "Insufficient Balance!";
			}
			
		} else {
			echo "Invalid Amount";
		}
	} else {
		echo "Invalid Orbitcoin Address";
	}
}

function coustom_send($con, $coin, $session, $orbitcoin, $amount, $tx_comment, $password, $fee)  {
	$orb_adrs = explode(",", $orbitcoin);
	$amount = explode(",", $amount);
	$password = sha1($password);
	
	function is_empty($orb_adrs, $coin) {
		foreach($orb_adrs as $adrs => $key) {
			if(empty($key)) {
				return "Please Enter Orbitcoin To send payment!";
			} else {
				$test = $coin->validateaddress($key);
				if($test["isvalid"] != 1) {
					return "Invalid Orbitcoin address!";
				} else {
					return "Ok!";
				}
			}
		}
	}
	
	if(is_empty($orb_adrs, $coin) == "Ok!") {
		
		function is_amount($amount) {
			foreach($amount as $amt => $key) {
				if(empty($key)) {
					return "Please Enter Amount's";
				} else {
					if(!preg_match("/^[0-9]+(\.[0-9]{1,8})?$/", $key)) {
						return "Invalid Amount's";
					} else {
						return "Ok!";
					}
				}
			}
		}
		if(is_amount($amount) == "Ok!") {
			$sqli_for_sel_usr_data = mysqli_query($con, "SELECT Username FROM users WHERE Session_id='".$session."'");
			$user_data = mysqli_fetch_array($sqli_for_sel_usr_data);
			$username = $user_data["Username"];
				$address = $coin->getaddressesbyaccount($username);
				$balance = 0;
				$list_accounts = $coin->listaddressgroupings();
				$count = $list_accounts;
				for($i=count($count)-1;$i>=0;$i--) {
				$llops = $count[$i];
					for($o=count($llops)-1;$o>=0;$o--) {
						for($adr=count($address)-1;$adr>=0;$adr--) {
							if($count[$i][$o][0] == $address[$adr]) {
							$balance += $count[$i][$o][1];
						}
					}
				}
			}
			
			if(empty($fee)) {
				$fee = floatval(0.001);
			} elseif(is_numeric($fee)) {
				if($fee >= 0.001) {
				$fee = floatval($fee);
				} else {
					echo "Mini Miner Fee is 0.001 ORB";
				}
			} else {
				echo "Invalid Miner Fee!";
			}
			
			$array = array();
			
			foreach($amount as $index => $key) {
				$convert = floatval($key);
				$array[$orb_adrs[$index]] = floatval($amount[$index]);
			}
			
			$check = 0;
			foreach($array as $amnount => $total) {
				$check += floatval($total);
			}
			
			if($fee >= 0.001) {
			if($balance >= $check + $fee) {
				$setxfee = $coin->settxfee($fee);
				if($setxfee) {
					$send_adrs = $array;
					if(!empty($tx_comment)) {
						
					$send_payment_to_manay = $coin->sendmany($username, $array, 0, json_decode('$tx_comment'));
					} else {
						$send_payment_to_manay = $coin->sendmany($username, $array, 0);
					}
					if($send_payment_to_manay) {
						echo "Send! Tx id:"."<br />";
						echo "<a style='color:red;' href='atlas.phoenixcoin.org:1080/tx/".$send_payment_to_manay."'>".$send_payment_to_manay.'</a>';
					}
					
				}
			} else {
				echo "Insufficient Balance!";
			}
			}
		} else {
			echo is_amount($amount);
		}
	} else {
		echo is_empty($orb_adrs, $coin);
	}
}

function generate_new($coin, $session, $con) {
	$sqli = mysqli_query($con, "SELECT Username FROM users WHERE Session_id='".$session."'");
	if(mysqli_num_rows($sqli) == 1) {
		$fet = mysqli_fetch_array($sqli);
		$newadr = $coin->getnewaddress($fet["Username"]);
		if($newadr) {
			?>
			<tr>
                        <td>
                        <?php echo $newadr; ?>                       
						</td>
						<td>
						</td>
						<td>
						<img width="30px" height="30px" src="http://api.qrserver.com/v1/create-qr-code/?color=FFFFFF&amp;bgcolor=497BE8&amp;data=<?php echo $newadr; ?>&amp;qzone=1&amp;margin=0&amp;size=150x150&amp;ecc=L" alt="qr code" /> 
						</td>
            </tr>
					<?php
		}
	} else {
		echo "ERROR! Reload page";
	}
}

function Change_Account_setting($con, $session, $email, $old_pass, $password) {
	$npassword = sha1($password);
	$old_pass = sha1($old_pass);
	if(!filter_var($email, FILTER_VALIDATE_EMAIL)) {
		echo "Incorrect Email!";
	} else {
		if($old_pass == $npassword) {
			echo "Please Enter Different New Password!";
		} else {
			$chk_old = mysqli_query($con, "SELECT Password FROM users WHERE Password='".$old_pass."' AND Session_id='".$session."'");
			if(mysqli_num_rows($chk_old) == 1) {
				$sqli_update = mysqli_query($con, "UPDATE users SET Password='".$npassword."' WHERE Session_id='".$session."'");
				if($sqli_update) {
					$sql = mysqli_query($con, "UPDATE users SET Email='".$email."' WHERE Session_id='".$session."'");
					if($sql) {
						echo "Email And Password Change Successfully!";
					}
				}
			} else {
				echo "Incorrect Account Password!";
			}
		}
	}
	
}

function Change_Account_Password($con, $session, $old_pass, $password) {
	$old_pass = sha1($old_pass);
	$password = sha1($password);
	$sql_chk = mysqli_query($con, "SELECT Password FROM users WHERE Password='".$old_pass."' AND Session_id='".$session."'");
	if(mysqli_num_rows($sql_chk) == 1) {
		if($old_pass == $password) {
			echo "Please Enter Different New Password!";
		} else {
			$sqli_up = mysqli_query($con, "UPDATE users SET Password='".$password."' WHERE Session_id='".$session."'");
			if($sqli_up) {
				echo "Password Successfully Changed!";
			}
		}
	} else {
		echo "Incorrect Password!";
	}
}

function Change_Account_Email($con, $session, $email, $session) {
	if(filter_var($email, FILTER_VALIDATE_EMAIL)) {
		$sqli_chk = mysqli_query($con, "SELECT Email FROM users WHERE Email='".$email."'");
		if(mysqli_num_rows($sqli_chk) == 1) {
			echo "Email Already Taken!";
		} else {
			$sqli_update = mysqli_query($con, "UPDATE users SET Email='".$email."' WHERE Session_id='".$session."'");
			if($sqli_update) {
				echo "Email has been Change Successfully!";
			}
		}
	} else {
		echo "Incorrect Email!";
	}
}

function Dump_address($con, $coin, $session, $dump_adr, $dump_pass) {
	$password = sha1($dump_pass);
	$sqli_chk = mysqli_query($con, "SELECT Password,Username FROM users WHERE Password='".$password."' AND Session_id='".$session."'");
	if(mysqli_num_rows($sqli_chk) == 1) {
		$is_vid = $coin->validateaddress($dump_adr);
		if($is_vid["isvalid"] == 1) {
			$getuser = mysqli_fetch_array($sqli_chk);
			$list_adrs = $coin->getaddressesbyaccount($getuser["Username"]);
			function chk($list_adrs, $dump_adr) {
				foreach($list_adrs as $index => $key) {
					if($list_adrs[$index] == $dump_adr) {
						return "Ok!";
					}
				}
			}
			
			if(chk($list_adrs, $dump_adr) == "Ok!") {
				$dmp = $coin->dumpprivkey($dump_adr);
				echo $dmp;
			} else {
				echo "ERROR! Reload page";
			}
			
		} else {
			echo "ERROR! Invalid Address!";
		}
	} else {
		echo "Incorrect Account Password!";
	}
}

function Balance($con, $coin, $session) {
	$get_data = mysqli_query($con, "SELECT Username FROM users WHERE Session_id='".$session."'");
	$user_data = mysqli_fetch_array($get_data);
	
	$address = $coin->getaddressesbyaccount($user_data["Username"]);
	$balance = 0;
	$list_accounts = $coin->listaddressgroupings();
	$count = $list_accounts;
	for($i=count($count)-1;$i>=0;$i--) {
	$llops = $count[$i];
		for($o=count($llops)-1;$o>=0;$o--) {
			for($adr=count($address)-1;$adr>=0;$adr--) {
				if($count[$i][$o][0] == $address[$adr]) {
					$balance += $count[$i][$o][1];
				}
			}
		}
	}
	echo $balance;
}

function total_receive($con, $coin, $session) {
	$data = mysqli_query($con, "SELECT Username FROM users WHERE Session_id='".$session."'");
	$usr = mysqli_fetch_array($data);
	$total = $coin->getreceivedbyaccount($usr["Username"]);
	echo $total;
}

function receive_txs($con, $session) {
	$sel_user = mysqli_query($con, "SELECT Username FROM users WHERE Session_id='".$session."'");
	$user_data = mysqli_fetch_array($sel_user);
	?>
	<?php $sqli_sel = mysqli_query($con, "SELECT * FROM user_receive_transations WHERE Tx_username='".$user_data["Username"]."' ORDER BY Date DESC");
if(mysqli_num_rows($sqli_sel) > 0) {
?>
	<table class="table" id="r_txs">
                    <tr>
                        <td style="color:white">
                           From
                        </td>
                        <td style="color:white">
                           Estimate Time / Date
                        </td>
                        <td style="color:white">
                           Amount
                        </td>
                    </tr>
	<?php
while($tx=mysqli_fetch_array($sqli_sel)) {
	$sqli_sel_adr = mysqli_query($con, "SELECT * FROM sender_address WHERE Username='".$user_data["Username"]."' AND next_tx_id='".$tx["Tx_id"]."' LIMIT 1");
	while($ars=mysqli_fetch_array($sqli_sel_adr)) {
	?>
                    <tr>
                        <td >
                         <?php
							 if($ars["next_tx_id"] == $tx["Tx_id"]) {
							 echo "<a style='text-decoration:none;color:black;' href='http://atlas.phoenixcoin.org:1080/tx/".$ars["next_tx_id"]."' target=__blank>".$ars["Sender"]."</a>"."<br />";
							 }
						 ?>
                        </td>
						<td >
						<?php
						$estimate_like = time() - $tx["Date"];
							$mins = round($estimate_like / 60);
							$hours = round($estimate_like / 3600);
							$days = round($estimate_like / 86400);
							$weeks = round($estimate_like / 604800);
							$months = round($estimate_like / 2600640);
							$years = round($estimate_like / 31207680);
							
							if($estimate_like <= 60) {
								echo "$estimate_like Secs ago";
							} elseif($mins <= 60) {
								echo "$mins mins ago";
							} elseif($hours <= 23) {
								echo "$hours hours ago ";
							} elseif($days == 1) {
								echo "$days Day ago";
							} elseif($days <= 6) {
								echo "$days Days";
							} elseif($weeks == 1) {
								echo "$weeks Week ago";
							} elseif($weeks <= 4.3) {
								echo "$weeks weeks ago";
							} elseif($months) {
								echo "$months month ago";
							} elseif($months) {
								echo "$months months ago";
							} elseif($years == 1) {
								echo "$years year ago";
							} else {
								echo "$years years ago";
							}
							
						?>
						</td>
						<td >
						<?php
						echo "+".$tx["Amount"];
						
						?>
						</td>
                    </tr>
	<?php 
	}
	}
	} else {
		?>
		<div id="r_txs" style="text-align:center;">
		No Receive transation found!
		</div>
		<?php
	}
	?>
                </table>
	
	<?php
}

function send_txs($con, $session) {
	$sqli_sel = mysqli_query($con, "SELECT Username FROM users WHERE Session_id='".$session."'");
	$user_data = mysqli_fetch_array($sqli_sel);
	?>
	<?php $sqli_sel = mysqli_query($con, "SELECT * FROM send_transations WHERE Account='".$user_data["Username"]."' ORDER BY Date_send DESC"); 
if(mysqli_num_rows($sqli_sel) > 0) {
?>
	<table class="table" id="s_tx">
	                <tr>
                        <td style="color:white">
                           To
                        </td>
                        <td style="color:white">
                           Estimate Time / Date
                        </td>
                        <td style="color:white">
                           Amount
                        </td>
                    </tr>
	<?php
while($tx=mysqli_fetch_array($sqli_sel)) {
	?>
                    <tr>
                        <td >
                         <?php
							echo "<a style='text-decoration:none;color:black;' href='http://atlas.phoenixcoin.org:1080/tx/".$tx["Tx_id"]."' target=__blank>".$tx["To_address"]."</a>"."<br />";
						 ?>
                        </td>
						<td >
						<?php
							$estimate_like = time() - $tx["Date_send"];
							$mins = round($estimate_like / 60);
							$hours = round($estimate_like / 3600);
							$days = round($estimate_like / 86400);
							$weeks = round($estimate_like / 604800);
							$months = round($estimate_like / 2600640);
							$years = round($estimate_like / 31207680);
							
							if($estimate_like <= 60) {
								echo "$estimate_like Secs ago";
							} elseif($mins <= 60) {
								echo "$mins mins ago";
							} elseif($hours <= 23) {
								echo "$hours hours ago ";
							} elseif($days == 1) {
								echo "$days Day ago";
							} elseif($days <= 6) {
								echo "$days Days";
							} elseif($weeks == 1) {
								echo "$weeks Week ago";
							} elseif($weeks <= 4.3) {
								echo "$weeks weeks ago";
							} elseif($months) {
								echo "$months month ago";
							} elseif($months) {
								echo "$months months ago";
							} elseif($years == 1) {
								echo "$years year ago";
							} else {
								echo "$years years ago";
							}
							
						?>
						</td>
						<td >
						<?php
						echo "-".$tx["Amount_send"];
						
						?>
						</td>
                    </tr>
	<?php 
	}
	} else {
		?>
		<div class="table" id="s_tx" style="text-align:center;">
		No Send transation found!
		</div>
		<?php
	}
	?>
        </table>
		<?php
}

if(!empty($_POST["Username"])) {
	echo Username($con, $session);
}

if(!empty($_POST["Vali_orbitcoin_address"])) {
	$orb_address_for_vali = Clean(mysqli_real_escape_string($con, $_POST["Vali_orbitcoin_address"]));
	echo Is_validate_address($coin, $orb_address_for_vali);
}

if(!empty($_POST["orbitcoin_send_payment_password"])) {
	$user_input_pass_in_send_payment = Clean(mysqli_real_escape_string($con, sha1($_POST["orbitcoin_send_payment_password"])));
	echo chk_user_pass_during_send_payment($con, $user_input_pass_in_send_payment, $session);
}

if(!empty($_POST["Check_amount"])) {
	$chk_amount = Clean(mysqli_real_escape_string($con, $_POST["Check_amount"]));
	echo chk_amount($con,$coin, $chk_amount, $session);
}

if(isset($_POST["orb_address"])) {
if(!empty($_POST["orb_address"])) {
	if(!empty($_POST["payment"])) {
		if(!empty($_POST["account_pass"])) {
	$orbitcoin_address = Clean(mysqli_real_escape_string($con, $_POST["orb_address"]));
	$amnount = Clean(mysqli_real_escape_string($con, $_POST["payment"]));
	$password_orb = Clean(mysqli_real_escape_string($con, $_POST["account_pass"]));
	echo send_payment($con, $coin, $orbitcoin_address, $amnount, $password_orb, $session);
		} else {
			echo "Please Enter Account Password!";
		}
	} else {
		echo "Please Enter Amount!";
	}
} else {
	echo "Please Enter Orbitcoin To send payment!";
}
}

if(isset($_POST["Orb_addresses"])) {
	if(!preg_match("/^[a-zA-Z0-9,]+$/i", implode(",", $_POST["Orb_addresses"]))) {
		echo "Please Enter Orbitcoin Address to send payment!";
	} else {
		if(empty($_POST["coustom_amount"])) {
			echo "Please Enter Amount!";
		} else {
			if(isset($_POST["tx_comments"])) {
				if(!empty($_POST["coustom_pass"])) {
					if(isset($_POST["fee"])) {
						$orbitcoin = Clean(mysqli_real_escape_string($con, implode(",", $_POST["Orb_addresses"])));
						$amount = Clean(mysqli_real_escape_string($con, implode(",", $_POST["coustom_amount"])));
						$tx_comment = Clean(mysqli_real_escape_string($con, $_POST["tx_comments"]));
						$password = Clean(mysqli_real_escape_string($con, $_POST["coustom_pass"]));
						$fee = Clean(mysqli_real_escape_string($con, $_POST["fee"]));
						coustom_send($con, $coin, $session, $orbitcoin, $amount, $tx_comment, $password, $fee);
					}
				} else {
					echo "Please Enter Password!";
				}
			}
		}
	}
}

if(isset($_POST["generate_new"])) {
	if(!empty($_POST["generate_new"])) {
		echo generate_new($coin, $session, $con);
	}
}

if(isset($_POST["Email"]) OR isset($_POST["New_pass"]) OR isset($_POST["Password"])) {
if(!empty($_POST["Email"]) OR !empty($_POST["New_pass"])) {
	if(!empty($_POST["Password"])) {
			if(!empty($_POST["Email"]) AND !empty($_POST["New_pass"])) {
				$email = Clean(mysqli_real_escape_string($con, $_POST["Email"]));
				$old_pass = Clean(mysqli_real_escape_string($con, $_POST["Password"]));
				$password = Clean(mysqli_real_escape_string($con, $_POST["New_pass"]));
				if(strlen($_POST["New_pass"]) >= 6) {
				echo Change_Account_setting($con, $session, $email, $old_pass, $password);
				} else {
					echo "Please enter minimum 6 Characters In Password!";
				}
			} else {
				if(!empty($_POST["New_pass"])) {
					if(strlen($_POST["New_pass"]) >= 6) {
					$old_pass = Clean(mysqli_real_escape_string($con, $_POST["Password"]));
					$password = Clean(mysqli_real_escape_string($con, $_POST["New_pass"]));
					echo Change_Account_Password($con, $session, $old_pass, $password);
					} else {
						echo "Please enter minimum 6 Characters In Password!";
					}
				} else {
					if(!empty($_POST["Email"])) {
						$email = Clean(mysqli_real_escape_string($con, $_POST["Email"]));
						echo Change_Account_Email($con, $session, $email, $session);
					}
				}
			}
	} else {
		echo "Please Enter Account Password to change Setting!";
	}
} else {
	echo "Please Enter Fields to change Account Setting!";
}
}

if(!empty($_POST["Dump_address"])) {
	if($_POST["Dump_address"] != "Choose Orb Address") {
		if(!empty($_POST["Dump_password"])) {
			$dump_adr = Clean(mysqli_real_escape_string($con, $_POST["Dump_address"]));
			$dump_pass = Clean(mysqli_real_escape_string($con, $_POST["Dump_password"]));
			echo Dump_address($con, $coin, $session, $dump_adr, $dump_pass);
		} else {
			echo "Please Enter Password To dump address!";
		}
	} else {
		echo "Please Select Orbitcoin For Dumpinging Address!";
	}
}

if(!empty($_POST["Balance"])) {
	echo Balance($con, $coin, $session);
}

if(!empty($_POST["total_receive"])) {
	echo total_receive($con, $coin, $session);
}

if(!empty($_POST["receive_txs"])) {
	echo receive_txs($con, $session);
}

if(!empty($_POST["Send_txs"])) {
	echo send_txs($con, $session);
}
	} else {
	echo "ERROR! Reload page";
}

} else {
	echo "ERROR! Reload page";
}
?>