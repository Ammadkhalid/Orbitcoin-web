<?php
error_reporting(0);
require_once "php/config.php";
require_once "php/jsonRPCClient.php";
session_start();
$get = date_default_timezone_get();
date_default_timezone_set($get);
if(isset($_SESSION["Usr_c"])) {
	$session = $_SESSION["Usr_c"];
	$sqli_chk_session_true = mysqli_query($con, "SELECT Username,Password,Email,Wallet_address FROM users WHERE Session_id='".$session."'");
	if(mysqli_num_rows($sqli_chk_session_true) == 1) {
	$user_data = mysqli_fetch_array($sqli_chk_session_true);
?>
<!DOCTYPE html>
<html >
  <head>
    <meta charset="UTF-8">
	<link rel="icon"  type="image/png" href="https://github.com/ghostlander/Orbitcoin/blob/master/src/qt/res/icons/orbitcoin.png?raw=true"/>
	<meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>Welcome <?php echo $user_data["Username"] ?></title>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.3/jquery.min.js"></script>
        <link rel="stylesheet" href="css/wallet.css">
        <link rel="stylesheet" href="css/notifIt.css">
        <link rel="stylesheet" href="css/animation.css">
        <link rel="stylesheet" href="css/icons.css">
		<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.4.0/css/font-awesome.min.css">
		<script src="js/notifIt.min.js"></script>
		<script src="js/notifIt.js"></script>
		<link href='http://fonts.googleapis.com/css?family=Lato&subset=latin,latin-ext' rel='stylesheet' type='text/css'>
        
  </head>
  <div class="topbar">
  <ul>
	<li> <a href="logout.php">Logout</a></li>
  </ul>
  <div id="con"><span id="refresher"><i class="fa fa-refresh "></i></span></div>
  <span id="top_balance"><p><?php 
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
	
	?> ORB</p></span>
  </div>
	<header>
	<ul>
	<li id="home" class="home_active">Wallet</li>
	<li id="tx">Transactions</li>
	<li id="send">Send Payment</li>
	<li id="rx">Receive</li>
	<li id="account">Account Setting</li>
	</ul>
	</header>
  <body>
	<div class="container" id="home_content">
	<span id="total_b">Total Receive: <?php 
	echo $coin->getreceivedbyaccount($user_data["Username"]);
	?> ORB</span>
	<p id="balance">Balance: <?php 
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
	
	?> ORB</p><br />
	<div id="qt_code">
	<?php
	$_Wallet_address = $user_data["Wallet_address"];
	$command_rpc_address = $coin->getaddressesbyaccount($user_data["Username"]);
	if($address = $command_rpc_address) {
		
	?>
	<img width="100px" height="100px" src="http://api.qrserver.com/v1/create-qr-code/?color=FFFFFF&amp;bgcolor=497BE8&amp;data=<?php echo $_Wallet_address; ?>&amp;qzone=1&amp;margin=0&amp;size=150x150&amp;ecc=L" alt="qr code" /> 
	<?php } ?>
	</div><br />
	
	<span id="wallet_address">
	<span><?php

	if($address = $command_rpc_address) {
		print_r($_Wallet_address);
	}
	?></span>
	</span>
	</div>
	
	<div class="container_tx" id="tx_content">
	<span >Toggle Transactions</span>
	<div id="receive_txs">
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
				</div>
				<div id="send_txs">
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
		
	</div>
	</div>
	
	<div class="container_send" id="send_content">
	<div id="send_menu">
	<h2>Menu</h2>
	<ul>
	<li id="active_menu">Quick Send</li>
	</ul>
	</div>
	
	<div id="simple_send">
	<h1>Quick Send</h1>
	<p>Use the form below to send a payment to a Orbitcoin Address</p>
	<div class="send_form">
	<input type="text" placeHolder="Orbitcoin Address"><br />
	<input type="text" placeHolder="Amount" id="simple_amount"><br />
	<input type="Password" placeHolder="Password"><br />
	<button type="submit" id="button_simple">Send</button><br />
	
	<div id="error">
	
	
	</div>
	
	</div>
	
	</div>
	
	
	</div>
	<div id="receive_content" class="container">
	<table class="table">
                    <tr>
                        <td style="color:white">
                            Orbitcoin Address
                        </td>
                        <td style="color:white">
                            Balance
                        </td>
                        <td style="color:white">
                            qr Code
                        </td>
                    </tr>
	<?php 
	$count = $coin->getaddressesbyaccount($user_data["Username"]); 
	for($i=count($count)-1;$i>=0;$i--) {
	?>
                    <tr>
                        <td >
                         <?php echo $count[$i]; ?>
                        </td>
						<td >
						<?php
						$cg = $coin->listaddressgroupings();
						
						for($l=count($cg)-1;$l>=0;$l--) {
							for($k=count($cg[$l])-1;$k>=0;$k--) {
								if($cg[$l][$k]["0"] ==  $count[$i]) {
									print_r($cg[$l][$k]["1"]." ORB");
								}
							}
						}
						?>
						</td>
						<td >
						<a href="http://api.qrserver.com/v1/create-qr-code/?color=FFFFFF&amp;bgcolor=497BE8&amp;data=<?php echo $count[$i]; ?>&amp;qzone=1&amp;margin=0&amp;size=150x150&amp;ecc=L" target="__blank"><img width="30px" height="30px" src="http://api.qrserver.com/v1/create-qr-code/?color=FFFFFF&amp;bgcolor=497BE8&amp;data=<?php echo $count[$i]; ?>&amp;qzone=1&amp;margin=0&amp;size=150x150&amp;ecc=L" alt="qr code" /></a>
						</td>
                    </tr>
					<?php }?>
                </table>
	<button id="c_adr">Create New Address</button><p>Note: The address with empty balance field means 0 balance!</p>
	</div>
	
	<div class="container" id="account_content">
	<div id="account_menu"> 
	<h2>Menu</h2>
	<ul>
	<li id="active_menu">Account Setting</li>
	<li>Dump Address</li>
	</ul>
	</div>
	
	<div id="simple_send">
	<h1>Account Setting</h1>
	<p>Using this form to change email or Password And both</p>
	<div class="form">
	<input type="password" placeHolder="Change Password" id="password"><br />
	<input type="text" placeHolder="Change Email" id="email"><br />
	<input type="Password" placeHolder="Password" id="apass"><br />
	<button type="submit" id="change_simple">Change</button><br />
	
	<div id="error">
	
	
	</div>
	
	</div>
	
	</div>
	
	<div id="import_send">
	<h1>Dump address</h1>
	<p>Using this form to dump your Orbitcoin addresses!</p>
	<input type="password" Placeholder="Password" id="password"><br />
	<select id="adr_option">
	<option value="Choose Orb Address">Choose Orb Address</option>
	<?php
	$option = $coin->getaddressesbyaccount($user_data["Username"]); 
	for($i=count($option)-1;$i>=0;$i--) {
	?>
	<option value="<?php echo $option[$i]; ?>"><?php echo $option[$i]; ?></option>
	<?php }
	?>
	</select><br />
	<button type="submit" id="dump_adr">Dump Address</button><br />
	<textarea id="dump_keys">
	
	
	</textarea>
	</div>
	
	</div>
	</div>
	<script src="js/wallet.js"></script>
	<link type="text/css" rel="stylesheet" href="css/tooltipster-shadow.css" />
	<link type="text/css" rel="stylesheet" href="css/tooltipster.css" />
	<script type="text/javascript" src="js/jquery.tooltipster.min.js"></script>
  </body>
</html>
<?php
} else {
	session_destroy();
	header("location:login.php");
}
} else {
	header("location:login.php");
}