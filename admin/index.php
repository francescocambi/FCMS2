<!DOCTYPE HTML>
<html>
	<head>
		<title>Application Login</title>
		<link rel="stylesheet" href="http://yui.yahooapis.com/pure/0.5.0/pure-min.css">
		<link rel="stylesheet" href="http://yui.yahooapis.com/pure/0.5.0/forms-min.css">
		<link rel="icon" href="favicon.gif" type="image/gif">
		<style>
			body {
				background: rgb(25,24,24);
			}
			.container {
				background: #F2F2F2;
				padding: 10px;
				margin: auto;
				width: 400px;
				margin-top: 10%;
				border-radius: 5px;
			}
			.logo {
				width: 399px;
				height: 118px;
				margin: 2px;
			}
		</style>
	</head>
	<body>
		<div class="container" style="">
			<img class="logo" src="logo.png">
			<form class="pure-form" action="home.php">
				<!-- <legend><b>Application Login</b></legend> -->
				<fieldset class="pure-group">
					<input type="text" name="user" class="pure-input-1" placeholder="Username">
					<input type="password" name="pass" class="pure-input-1" placeholder="Password">
				</fieldset>
				<button type="submit" class="pure-button pure-input-1 pure-button-primary">Sign In</button>
			</form>
		</div>
	</body>
</html>