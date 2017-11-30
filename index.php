<?php
if(empty($_SERVER['HTTPS']) || $_SERVER['HTTPS'] == "off"){
    $redirect = 'https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
    header('HTTP/1.1 301 Moved Permanently');
    header('Location: ' . $redirect);
    exit();
}
?>
<!DOCTYPE html>
<html>
<head>
<title> Team CyberCrypts </title>
<link rel="stylesheet" type="text/css" href="styles.css">
</head>
<body>

<?php
session_start();
$_SESSION['logon'] = false;

// Load database credentials
$servername = include 'config/servername.php';	
$data_username = include 'config/data_username.php';
$data_password = include 'config/data_password.php';
$db = include 'config/db.php';

// define variables and set to empty
$username = $password = $usernameErr = $passwordErr = $hashed_password = $raw_password = $raw_username = "";
$limit_attempt = 10;
$cost = 14;
$fail = false;

// Create connection
$link = new mysqli($servername, $data_username, $data_password, $db);

// Check connection
if ($link->connect_error) {
    die("Connection failed: " . $link->connect_error);
} 

// Prevent SQL injection with real_escape_string()
$safe_username = $link->real_escape_string($_POST["username"]);
$safe_password = $link->real_escape_string($_POST["password"]);

//password_verify();
if($_SERVER["REQUEST_METHOD"] == "POST"){
	//while(/*user doesn't hit quit*/) {
	
	  if(empty($safe_username)) 
	    echo "Username is required.";

	  else { //username field not empty
	    $raw_username = strtolower($safe_username);
	    $username = test_input($raw_username);
        $_SESSION['username'] = $username;

	    if(empty($safe_password))
	      echo "Password is required";
	    else { //password is not empty
	    	if($username == 'admin' || $username == 'macklin' || $username == 'guest' || $username == 'lampcougars' || $username == 'ligers' || $username == 'cyberlinux' || $username == 'cybercrypts' || $username == 'mortalwombat' || $username == 'team6' || $username == 'team7'){
	    		$query = "SELECT password, phrase FROM logins WHERE username = ?";
	      $password = test_input($safe_password);
	      $stmt = $link->prepare($query);
	      $stmt->bind_param("s", $username);
	      $stmt->execute(); //execute preferred statement
	      $stmt->bind_result($postPass, $postPhrase); //grab pwd from db
	      $stmt->fetch();
	      $pass_correct = password_verify($password, $postPass);
		  	if($pass_correct === TRUE) {
                $_SESSION['phrase'] = $postPhrase;
                $_SESSION['logon'] = true;
                header("Location: menu/test.php");
                exit();
                
		  	}
		  	else {

		    	$stmt->close();
		    	$queryForUnsuccessfulAttempt = "UPDATE nonadm, adm SET nonadm." . "unsuccessfulAttempt = nonadm." . "unsuccessfulAttempt + 1, adm." . "unsuccessfulAttempt = nonadm." . "unsuccessfulAttempt  WHERE nonadm." . "username = ? and adm." . "username = ?";
				$stmt = $link->prepare($queryForUnsuccessfulAttempt);
				$stmt->bind_param('ss', $username, $username);

				if($stmt->execute()){
					$query = "SELECT unsuccessfulAttempt FROM nonadm WHERE username = ?";
					$stmt->close();
					$stmt = $link->prepare($query);
					$stmt->bind_param("s",$username);
					$stmt->bind_result($unsuccessfulAttempt);

					if($stmt->execute()){
						$stmt->fetch();
						if(($unsuccessfulAttempt % 10) == 0){
							echo "You have exceeded the maximum number of consecutive login attempts. System is locked for 30s.";
							ob_end_flush();
							flush();
							sleep(30); 
							echo("<meta http-equiv='refresh' content='0'>");
						}
						else{
							echo "Invalid credentials. <br>" . "</br>";
						}// end of not 10 attempt
					}// end of second execute
					else{
						echo "Fail to get number of unsuccessful attempt." . "</br>";
					}
				}// end of first execute
				else{//fail to update unsuccessful attempt
					echo "Fail to update unsuccessful attempt." . "</br>";
				}// end of fail to update unsuccessful attempt		
		  	}//end else incorrect password
		  }//end of valid username
		  else{
		  	echo "Invalid credentials. <br>" . "</br>";
		  }
	    }//end else password is not empty
	  }//end username field is not empty
	//}//end while 
	//OUT OF WHILE, NOW WE QUIT
 }//end if server post

function test_input($data){
  $data = trim($data);
  $data = stripslashes($data);
  $data = htmlspecialchars($data);
  return $data;
}
$link->close();
?>

<center id='center'><img src="logo.png"></center>
<div class="login">
	<form method = "post" action = "<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
	    <label for="username">Username</label>
        <div id="error"><span class:"error">* <?php echo $usernameErr;?></span></label></div>
        <input type="text" id="username" name="username">
        <label for="password">Password</label>
        <div id="error">
        <span class:"error">* <?php echo $passwordErr;?></span></div>
        <input type="password" id="password" name="password">
        <input type="submit" value="Login">
    </form>
</div>
</body>
</html>
