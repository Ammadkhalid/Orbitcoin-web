<?php
require_once "jsonRPCClient.php";
// config database i.e the localhost is host, root is username, and the empty filed is password, orbitcoin is database
$con = mysqli_connect("localhost", "root", "", "orbitcoin");
/// here put ur orbitcoin wallet with user pass and port
$coin = new jsonRPCClient("http://Orb:Orbitcoin_password@127.0.0.1:15299");
?>
