<?php
ob_start();
session_start();
include('db_connect.php');
if(!isset($_SESSION['logged_in']) || empty($_SESSION['logged_in'])){
	//show a message or redirect user to login page, because it's nrequired to login to access to this page
	header("Location: login.php");
}
?>
<!DOCTYPE html>
<html>
<head>
	<title>My profile</title>
</head>
<body>
<article>
	<section>
		<h1>My profile</h1>
		<?php echo "Welcome ".$_SESSION['username']."<br>"; ?>
		<a href="logout.php">Logout</a>
		<?php echo "Your informations :<br><b>Email :</b> ".$_SESSION['email']."<br><b>Username :</b> ".$_SESSION['username']; ?>
	</section>
</article>
</body>
</html>