<!DOCTYPE html>
<html>
<head>
<title> Team CyberCrypts </title>
<link rel="stylesheet" type="text/css" href="../styles.css">
</head>
<body>
<center id='center'><img src="../logo.png">

<?php
session_start();
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

// Load database credentials
$servername = include '../config/servername.php';	
$data_username = include '../config/data_username.php';
$data_password = include '../config/data_password.php';
$db = include '../config/db.php';

// Create connection
$link = new mysqli($servername, $data_username, $data_password, $db);

echo "<p><br> The phrase for " . $_SESSION['username'] . " is<br>" . $_SESSION['phrase'] . "<br><br></p>"; 
if($_SESSION['logon'] == false) {
	header("Location: ../index.php");
	die();
}

$loggedInUsername = $_SESSION['username'];
$fail = $_SESSION['fail'];
$successLoggedIn = $failLoggedIn = false;

if($_SESSION['username'] == "admin") {
	/*QUERY TABLE FOR ADMIN*/
	if($_SERVER["REQUEST_METHOD"] === "POST"){
		if(isset($_POST['quit'])){
			header("Location: ../index.php");
			die();
		}
		else{
			$queryForSuccessAttempt = "UPDATE nonadm, adm SET nonadm." . "successfulAttempt = nonadm." . "successfulAttempt + 1, adm." . "successfulAttempt = nonadm." . "successfulAttempt  WHERE nonadm." . "username = ? and adm." . "username = ?";
			$stmt = $link->prepare($queryForSuccessAttempt);
			$stmt->bind_param("ss",$loggedInUsername, $loggedInUsername);
			if($stmt->execute()){
				$stmt->close();
				$query = "SELECT * FROM adm";
				$stmt = $link->prepare($query);
				$stmt->bind_result($username, $successfulAttempt, $unsuccessfulAttempt, $password); //grab pwd from db

				if($stmt->execute()){
					echo "
						<table id = 'format' align = 'center'>
							<tr>
								<th>Username</th>
								<th>Successful Logins</th>
								<th>Unsuccessful Logins</th>
								<th>Password (Hash)</th>
							</tr>";
					while($row = $stmt->fetch())
					{
						echo "
							<tr>
								<td align = 'center'>".$username."</td>
								<td align = 'center'>".$successfulAttempt."</td>
								<td align = 'center'>".$unsuccessfulAttempt."</td>
								<td align = 'center'>".$password."</td>
							</tr>";
					}
					echo "
						</table>
					</div>";
				}//end of inner execute
				else{
					echo "Fail to print out table." . "</br>";
				}
			}// end of outer execute
			else{//fail to update successful attempt
				echo "Fail to update successful attempt." . "</br>";
			}
		}// end of list user for admin
	}// end of post 
}// end for admin
else {
	/*QUERY TABLE FOR NONADMINS*/
	if($_SERVER["REQUEST_METHOD"] === "POST"){
		if(isset($_POST['quit'])){
			header("Location: ../index.php");
			die();
		}
		else{
			$queryForSuccessAttempt = "UPDATE nonadm, adm SET nonadm." . "successfulAttempt = nonadm." . "successfulAttempt + 1, adm." . "successfulAttempt = nonadm." . "successfulAttempt  WHERE nonadm." . "username = ? and adm." . "username = ?";
			$stmt = $link->prepare($queryForSuccessAttempt);
			$stmt->bind_param("ss",$loggedInUsername, $loggedInUsername);
			if($stmt->execute()){
				$stmt->close();
				$query = "SELECT * FROM nonadm";
				$stmt = $link->prepare($query);
				$stmt->bind_result($username, $successfulAttempt, $unsuccessfulAttempt); //grab pwd from db

				if($stmt->execute()){
					echo "
					<table border = 1px solide white align = 'center'>
					<tr>
						<th>Username</th>
						<th>Successful Logins</th>
						<th>Unsuccessful Logins</th>
					</tr>";
					while($row = $stmt->fetch()){
						echo "
							<tr>
								<td align = 'center'>".$username."</td>
								<td align = 'center'>".$successfulAttempt."</td>
								<td align = 'center'>".$unsuccessfulAttempt."</td>
							</tr>";
					}
					echo "
						</table>
					</div>";
					$stmt->close();
				}//end of inner execute
				else{
					echo "Fail to print out table." . "</br>";
				}
			}// end of outer execute
			else{//fail to update successful attempt
				echo "Fail to update successful attempt." . "</br>";
			}
		}//end of list user for nonadm
	}//end of post
}// end of non admin
?>
	
<form method = "post" action = "<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
	<div class = "click">
		<input type = "submit" name = "quit" value = "Quit">
		<input type= "submit" name = "listUser" value = "List Users"> 
	</div>
</form>
</center> 
</body>
</html>
