<?php
require_once("../bootstrap.php");
$em = initializeEntityManager("../");

//Check if there is an active session
$session = \Authentication\SessionFactory::getInstance()->detectCurrentSession($em, new \Authentication\HttpSessionHandler());
if (!is_null($session) && $session->isValid($_SERVER['REMOTE_ADDR'])) {
    header("Location: home.php");
}

$EXCEPTION_THROWN = null;

//Check if client has submitted login form
if (isset($_POST['email']) && isset($_POST['password']) && $_POST['email'] != "" && $_POST['password'] != "") {

    $loginManager = new \Authentication\PasswordLoginManager($em);

    $session = $loginManager->doLogin(array(
        "email" => $_POST['email'],
        "password" => $_POST['password']
    ));

    if (!is_null($session))
        header("Location: home.php");
    else
        $SHOW_INVALID_CREDENTIALS = true;

}

?>
<!DOCTYPE HTML>
<html>
	<head>
		<title>Application Login</title>
		<link rel="stylesheet" href="css/pure-min.css">
		<link rel="stylesheet" href="css/forms-min.css">
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
            .errorMessage p {
                color: red;
                text-align: center;
                margin-top: -7px;
                margin-bottom: 3px;
                font-height: 0.8em;
            }
		</style>
	</head>
	<body>
		<div class="container" style="">
			<img class="logo" src="logo.png">
			<form class="pure-form" method="POST">
				<!-- <legend><b>Application Login</b></legend> -->
				<fieldset class="pure-group">
					<input type="text" name="email" class="pure-input-1" placeholder="Email">
					<input type="password" name="password" class="pure-input-1" placeholder="Password">
				</fieldset>
                <?php
                if ($SHOW_INVALID_CREDENTIALS) {
                ?>
                    <div class="errorMessage"><p>Invalid Credentials! Try Again...</p></div>
                <?php } ?>
				<button type="submit" class="pure-button pure-input-1 pure-button-primary">Sign In</button>
			</form>
		</div>
	</body>
</html>