<?php
ob_start();
session_start();
include('db_connect.php');
?>
<!DOCTYPE html>
<html>
<head>
	<title>Activate</title>
</head>
<body>
<article>
	<section>
		<?php
		if(!isset($_GET['email']) || empty($_GET['email']) || !isset($_GET['code']) || empty($_GET['code'])){
			echo "Invalid link";
		}
		else{
			$email_from_get = $_GET['email'];
			$code_from_get = $_GET['code'];
			$stmt=$con->prepare("SELECT * FROM users WHERE email=? AND activation_code=?");
			$stmt->bind_param("ss",$email_from_get,$code_from_get);
			$stmt->execute();
			$stmt->store_result();
			if($stmt->num_rows() == 0){
				echo "Incorrect link";
			}
			else{
				//correct link
				//we check if account is already activated
				$activated_text = "activated";
				$stmt=$con->prepare("SELECT * FROM users WHERE email=? AND activation_code=? AND activation_status=?");
				$stmt->bind_param("sss",$email_from_get,$code_from_get,$activated_text);
			    $stmt->execute();
			    $stmt->store_result();
			    if($stmt->num_rows() > 0){
			    	echo "your account is already activated <a href='login.php'>Click here to connect</a>";
			    }
			    else{
			    	//correct link and account is not already activated so we activate
			    	$stmt=$con->prepare("UPDATE users SET activation_status=? WHERE email=? and activation_code=?");
			        $stmt->bind_param("sss",$activated_text,$email_from_get,$code_from_get);
			        if($stmt->execute()){
			        	echo "You have activated your account successfuly <a href='login.php'>click here to connect</a>";
			        }
			        else{
			        	echo "an error occured while activating your account please retry again";
			        }
			    

			    }


			}
		}
		?>
	</section>
</article>
</body>
</html>