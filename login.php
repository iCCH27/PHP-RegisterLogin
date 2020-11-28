<?php
ob_start();
session_start();
include('db_connect.php');
?>
<!DOCTYPE html>
<html>
<head>
	<title>Login</title>
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
	<style>
* {box-sizing: border-box}

/* Add padding to containers */
.container {
  padding: 16px;
}

/* Full-width input fields */
input[type=email], input[type=password],input[type=text] {
  width: 100%;
  padding: 15px;
  margin: 5px 0 22px 0;
  display: inline-block;
  border: none;
  background: #f1f1f1;
}

input[type=email]:focus, input[type=password]:focus,input[type=text]:focus {
  background-color: #ddd;
  outline: none;
}

/* Overwrite default styles of hr */
article hr {
  border: 1px solid #f1f1f1;
  margin-bottom: 25px;
}
article label{
	font-weight: bold;
}

/* Set a style for the submit/register button */
.loginbtn {
  background-color: #4CAF50;
  color: white;
  padding: 16px 20px;
  margin: 8px 0;
  border: none;
  cursor: pointer;
  width: 100%;
  opacity: 0.9;
  font-weight: bold;
}

.loginbtn:hover {
  opacity:1;
}

/* Add a blue text color to links */
article a {
  color: dodgerblue;
}


	</style>
</head>
<body>
<article>
	<h1>Login</h1>
	<div class="container">
	<form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="post">
		<label><i class="fa fa-envelope"></i> Email :</label>
		<input type="email" name="email" required>
		<label><i class="fa fa-lock"></i> Password :</label>
		<input id="password_input" type="password" name="password" required>
		<br>
		<input type="checkbox" onclick="showpassword()">Show Password
		<br><br>
		<input type="submit" name="submit" value="login" class="loginbtn">
<?php
if(isset($_POST['submit'])){
	if(!isset($_POST['email'])||empty($_POST['email']) || !isset($_POST['password'])||empty($_POST['password']) ){
echo "Please fill all fields!";
}
else{
	//Now we check if the user is already registered
	$stmt=$con->prepare("SELECT username,email,password,activation_status FROM users WHERE email=?");
	$stmt->bind_param('s',$_POST['email']);
	$stmt->execute();
	$stmt->store_result();
	if($stmt->num_rows()== 0){
		echo "this email isn't registered yet, click <a href='register.php'>here</a> to register";
	}
	elseif($stmt->num_rows()>0){ //user with the entered email exists
		$stmt->bind_result($Username,$Email,$Password,$Activation_status);// we bind the results of the selected columns respectively and we store them on these variables
		$stmt->fetch(); //do not forget this function fetch after bind_result
		// we verify the password 
		if(!password_verify($_POST['password'],$Password)){ //if the password is incorrect
			echo "the entered password is incorrect, please try again!";
	}else{
		//correct password
		//We check activation_status
		if($Activation_status!="activated"){
			echo "Your account isn't activated, please check your email box to activate it.";
		}
		elseif($Activation_status=="activated"){

		//we create session
		session_regenerate_id(); //to create a new session id
		$_SESSION['logged_in']=TRUE;
		$_SESSION['username']=$Username;
		$_SESSION['email']=$Email;
		$_SESSION['debut_datetime']= time(); 
		//we redirect user to profile page for example
		header("Location: profile.php"); //l should be uppercase and without a space after
		exit();
	}
	}

	}
	$stmt->close();
}
}
?>
		
	</form>
</div>
</article>
<script>
	function showpassword() {
  var password_input = document.getElementById("password_input");
  if (password_input.type === "password") {
    password_input.type = "text";
    //input will be shown as text (visible)
  } else {
    password_input.type = "password";
  }
}
</script>
</body>
</html>