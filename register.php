<?php
ob_start();
session_start();
//even spaces before these 2 functions can cause some problems
//ob_start(); to avoid some session errors like (headers already sent ..etc)
 //session_start(); to start session, because we will work with sessions (after login)
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'PHPMailer-master/src/Exception.php';
require 'PHPMailer-master/src/PHPMailer.php';
require 'PHPMailer-master/src/SMTP.php';
include('db_connect.php'); /*include will import the code from this file so it's preferable to use it for header.php, footer.php db_connect.php head.php .. etc because they will be repeated on multipe pages*/
?>
<!DOCTYPE html>
<html>
<head>
	<title>Register</title>

</head>
<body>
	<header>
	</header>
	<article>
		<h1>Register</h1>
		<div class='container'>
		<form method="post" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" class="register_form">
			<label for="username">Username :</label>
			<input name="username" type="text" required />
			<label for="email">Email :</label>
			<input name="email" type="email" required />
			<label for="password">Password :</label>
			<input id="password_input" name="password" type="password" required />
			<label for="password_confirmation">Password confirmation :</label>
			<input name="password_confirmation" type="password" required />
			<input type="submit" name="submit" class="registerbtn">
<?php
if (isset($_POST["submit"])){
if(!isset($_POST['username'])||empty($_POST['username']) || !isset($_POST['email'])||empty($_POST['email']) || !isset($_POST['password'])||empty($_POST['password']) ){
echo "Please fill all fields!";
}
elseif(strlen($_POST['password']) < 4 || strlen($_POST['password']) > 35){ //password length should be > 4 and < 35
	echo "Password length should be between 4 and 35!";
}
elseif($_POST['password_confirmation'] != $_POST['password']){
echo "the passwords doesn't match!";
}
else{
//We will check if the entered username is used, or the email is already registered 
	$stmt = $con->prepare("SELECT * from users WHERE username=?"); //if nothing happen when cliking register check these $con->prepare(..) because we didn't use if else here
	$stmt->bind_param('s',$_POST['username']); //s for String, i Integer, b Blob
	$stmt->execute();
	$stmt->store_result(); // we stored the result of the query, now we verify if there's result WHERE username = $_POST['username'] entered by the user
	if($stmt->num_rows()>0){ //results >0 (username already used)
		echo "This username is already taken please choose another";
	}
	else{ //username not taken now we verify the email
		$stmt=$con->prepare("SELECT * from users WHERE email=?");
		$stmt->bind_param('s',$_POST['email']);
	    $stmt->execute();
	    $stmt->store_result();
	    if($stmt->num_rows > 0){ //num_rows work both as a function and without (), but sometimes it defer depending on versions
	    	echo "This email is already registered, click <a href='login.php'>here</a> to connect!";
	    }
	    else{
	    //email not registered so here we insert the new user
	    	$stmt=$con->prepare("INSERT INTO users (username,email,password,activation_code,activation_status) VALUES (?,?,?,?,?)");
	    	//We hash the password so it will not be exposed in database
	    	$password = password_hash($_POST['password'],PASSWORD_DEFAULT);
	    	$activation_code= uniqid(); //unique id
	    	$default_activation_status = "not activated";
	    	$stmt->bind_param("sssss",$_POST['username'],$_POST['email'],$password,$activation_code,$default_activation_status);
	    	if($stmt->execute()){
	    	//user registered successfuly
	    	//We send confirmation email
	    	$mail = new PHPMailer(true);

try {
    //Server settings
    $mail->SMTPDebug = 0;  //0 will not show debug messages because if it shows them the user will be able to see message body here                     // Enable verbose debug output
    $mail->isSMTP();                                            // Send using SMTP
    $mail->Host       = 'smtp.gmail.com';
    $mail->SMTPAuth   = true;  // Enable SMTP authentication
    $mail->Username   = 'bibliodzmail@gmail.com'; 
    $mail->Password   = "BiblioDZ27000";
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;         // Enable TLS encryption; `PHPMailer::ENCRYPTION_SMTPS` encouraged
    $mail->Port       = 587;                                    // TCP port to connect to, use 465 for `PHPMailer::ENCRYPTION_SMTPS` above

    //Recipients
    $mail->setFrom('bibliodzmail@gmail.com', 'Register login tutorial');
    $mail->addAddress($_POST['email'], $_POST['username']);     // Add a recipient

    // Content
    $mail->isHTML(true);                                  // Set email format to HTML
    $mail->Subject = 'Account activation required';
    $Activation_link = "localhost/registerloginsystem/activate.php?email=".$_POST['email']."&code=".$activation_code;
    $mail->Body    = "Account activation is required to use your account,<br><b><a href='".$Activation_link."'>click here to activate your account</a> </b> if the link doesn't redirect click here <a href='".$Activation_link."'>".$Activation_link."</a> <br>Do not reply to this email.";
    $mail->AltBody = "Account activation is required to use your account, click here to activate your account : ".$Activation_link." Do not reply to this email.";

    $mail->send();
    echo 'Confirmation email has been sent, please verify your email box';
    echo "You have registered successfuly, click <a href='login.php'>here</a> to connect";
} catch (Exception $e) {
    echo "Confirmation email could not be sent. Mailer Error: {$mail->ErrorInfo}";
    //we can delete user because he didn't receive activation email
}

	    	}
	    	else{
	    		//error while registring
	    		echo "An error occured, please try again! "; //add .$con->error to see more details about the error if it happened
	    	}

	    }
	}
$stmt->close(); //not necessary
}
}
?>
		</form>
	</div>
	</article>

</body>
</html>