<?php
require_once "config.php";
require_once "jsonRPCClient.php";
$sqli_sel = mysqli_query($con, "SELECT Username FROM users");
while($ysk = mysqli_fetch_array($sqli_sel)) {
	$usrs = $ysk["Username"];
	$store = $coin->listtransactions($usrs);
	foreach($store as $li => $jh) {
	$uusr_tx = ($store[$li]["account"]);
	$tx = $store[$li]["txid"];
	$c = $coin->getrawtransaction($tx);
	$d = $coin->decoderawtransaction($c);
	$store_time = $d["time"];
	
	$est = array_splice($jh, 1);
	$amount = $est["amount"];
	$sql_chk_ids = mysqli_query($con, "SELECT Tx_id,Tx_username FROM User_receive_transations WHERE Tx_id='".$tx."' AND Tx_username='".$uusr_tx."'");
	if(mysqli_num_rows($sql_chk_ids) == 0) {
	$sqli_sert = mysqli_query($con, "INSERT INTO User_receive_transations (Tx_username,Tx_id,Date,Amount) VALUES ('".$uusr_tx."','".$tx."','".$store_time."','".$amount."')");
	if($sqli_sert) {
		echo "Ok!";
	}
	}
	
	}
}
?>

<?php
require_once "config.php";
require_once "jsonRPCClient.php";
error_reporting(0);
$sqli_inert_usr = mysqli_query($con, "SELECT Username FROM users");
while($user=mysqli_fetch_array($sqli_inert_usr)) {
$sqli_sel = mysqli_query($con, "SELECT Tx_username,Tx_id,Date FROM user_receive_transations Where Tx_username='".$user["Username"]."' ORDER BY ID DESC");
while($tx=mysqli_fetch_array($sqli_sel)) {
	$jj = $coin->getrawtransaction($tx["Tx_id"]);
	$test = $coin->decoderawtransaction($jj);
	echo "<pre>";
	$tx = $test["txid"];
	foreach($test as $test1 => $testk) {
		$time = $test["time"];
		foreach($testk as $kk) {
			$prev_txs = ($kk["txid"]);
			
			if(!empty($prev_txs) && !empty($tx) && !empty($user["Username"])) {
				$username = $user["Username"];
				$sqli_chk = mysqli_query($con, "SELECT ID FROM user_previous_tx_id WHERE Username='".$username."' AND Prev_tx_id='".$prev_txs."' AND next_tx_id='".$tx."'");
				if(mysqli_num_rows($sqli_chk) == 0) {
					$sqli_insert = mysqli_query($con, "INSERT INTO user_previous_tx_id (Username,Prev_tx_id,next_tx_id) VALUES ('".$username."','".$prev_txs."','".$tx."')");
					if($sqli_insert) {
						echo true;
					}
				}
			}
			
		}
		
	}
}
}
?>

<?php
require_once "config.php";
require_once "jsonRPCClient.php";
error_reporting(0);
$sqli_inert_usr = mysqli_query($con, "SELECT Username FROM users");
while($user=mysqli_fetch_array($sqli_inert_usr)) {
echo "<pre>";
$sqli_get_txs = mysqli_query($con, "SELECT * FROM user_previous_tx_id WHERE Username='".$user["Username"]."'");
while($tx_data=mysqli_fetch_array($sqli_get_txs)) {
	$raw = $coin->getrawtransaction($tx_data["Prev_tx_id"]);
	$decods = $coin->decoderawtransaction($raw);
	
	foreach($decods as $craps) {
		foreach($craps as $hits) {
			foreach($hits as $shit) {
				$yy = (array_splice($shit, 3));
				$adrs = ($yy["addresses"]["0"]);
				if(!empty($adrs) && !empty($tx_data["Prev_tx_id"]) && !empty($tx_data["next_tx_id"])) {
				$sqli_chk = mysqli_query($con, "SELECT ID FROM sender_address WHERE Username='".$user["Username"]."' AND Prev_tx_id='".$tx_data["Prev_tx_id"]."' AND next_tx_id='".$tx_data["next_tx_id"]."' AND Sender='".$adrs."'");
				if(mysqli_num_rows($sqli_chk) == 0) {
					$sqli_ = mysqli_query($con, "INSERT INTO sender_address (Username,Prev_tx_id,next_tx_id,Sender) Values('".$user["Username"]."','".$tx_data["Prev_tx_id"]."','".$tx_data["next_tx_id"]."','".$adrs."')");
					if($sqli_) {
						echo "Ok!";
					}
				}
				}
			}
			
		}
	}
	
	
}
}
?>