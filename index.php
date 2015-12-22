<!DOCTYPE html>
<html >
  <head>
    <meta charset="UTF-8">
	<link rel="icon"  type="image/png" href="https://github.com/ghostlander/Orbitcoin/blob/master/src/qt/res/icons/orbitcoin.png?raw=true"/>
	<meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>Home</title>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.3/jquery.min.js"></script>
    <script src="js/index.js"></script>
        <link rel="stylesheet" href="css/index.css">
		<link href='http://fonts.googleapis.com/css?family=Lato&subset=latin,latin-ext' rel='stylesheet' type='text/css'>
        
  </head>
	<header>
	<ul>
	<li><a href="index.php">Home</li></a>
	<li><a href="reg.php">Create Wallet</li></a>
	<li><a href="login.php">Wallet Login</li></a>
	</ul><br />
	<div id="search_bar">
	<input type="text" placeholder="Search Address/transation/hash">
	</div>
	</header><br />
  <body>
	<div class="body">
	<h1>Rich addresses</h1>
	<div id="richlists">
	<table>
  <thead>
    <tr>
      <th>Rank</th>
      <th>Address</th>
      <th>Amount</th>
    </tr>
  </thead>
  <tbody>
  <?php
  require "php/config.php";
  $sqli_sel = mysqli_query($con, "SELECT * FROM addresses_balance ORDER BY Balance DESC");
  if(mysqli_num_rows($sqli_sel) > 0) {
	  $i = 0;
	  while($list=mysqli_fetch_array($sqli_sel)) {
		  if($list["Balance"] > 0) {
			  $i++;
  ?>
    <tr>
			<td><?php echo $i; ?></td>
			<td><?php echo $list["Address"]; ?></td>
			<td><?php echo $list["Balance"]; ?></td>
		
    </tr>
	<?php
		  }
	  }
  } else {
	  echo "No Address Found!";
  }
	
	?>
  </tbody>
</table>
	</div>
	</div>
  </body>
</html>