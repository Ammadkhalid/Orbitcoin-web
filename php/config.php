<?php
require_once "jsonRPCClient.php";
$con = mysqli_connect("localhost", "root", "", "orbitcoin");
$coin = new jsonRPCClient("http://Orb:Orbitcoin_password@127.0.0.1:15299");
?>