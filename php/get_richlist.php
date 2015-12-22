<?php
require_once "config.php";
require_once "jsonRPCClient.php";

$test = $coin->listaddressgroupings();
foreach($test as $keys) {
	foreach($keys as $indexs => $key) {
			$address = $key["0"];
			$balance = $key["1"];
			$sqli_sel = mysqli_query($con, "SELECT ID FROM addresses_balance WHERE Address='".$address."'");
			if(mysqli_num_rows($sqli_sel) == 0) {
			$sqli_ = mysqli_query($con, "INSERT INTO addresses_balance (Address,Balance) VALUES ('".$address."','".$balance."')");
			} else {
				$sqli_update = mysqli_query($con, "UPDATE addresses_balance SET Balance='".$balance."' WHERE address='".$address."'");
			}
	}
}
?>