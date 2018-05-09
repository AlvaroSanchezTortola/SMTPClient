<?php

// Initialize the session
session_start();

// If session variable is not set it will redirect to login page
if(!isset($_SESSION['username']) || empty($_SESSION['username'])){
  header("location: login.php");
  exit;
}


$msg_status = "";
function SocketConnect(){
	set_time_limit(5);
	 
	if (($socket = socket_create(AF_INET, SOCK_STREAM, 0)) === false) {
	    die("Could not create socket\n");
	}else{
		echo "Socket created succesfuly!\n";
	}
	 
	if (($connection = socket_connect($socket, "127.0.0.1", 6666)) === false) {
	    die("Could not connect to server\n");
	}else{
		echo "Succesfully connected!!\n";
	}
	 
	$data = "Hello World";
	socket_write($socket, $data, strlen($data));
	 
	if (($data = socket_read($socket, 1024)) === false) {
	    die("Could not read input\n");
	} else {
	    echo "Server sent: \"" . $data . "\"";
	}
	 
	socket_close($socket);
}

if($_SERVER["REQUEST_METHOD"] == "POST"){
	// validation expected data exists
    if(!isset($_POST['first_name']) ||
        !isset($_POST['last_name']) ||
        !isset($_POST['email']) ||
        !isset($_POST['telephone']) ||
        !isset($_POST['comments'])) {
        died('We are sorry, but there appears to be a problem with the form you submitted.');       
    }
    $first_name = $_POST['first_name']; // required
    $last_name = $_POST['last_name']; // required
    $email_from = $_POST['email']; // required
    $telephone = $_POST['telephone']; // not required
    $comments = $_POST['comments']; // required

    echo "$first_name\n";
    echo "$comments\n";
    $msg_status = "SENT!";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Send a Mail!</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.css">
    <style type="text/css">
        body{ font: 14px sans-serif; background-image: url("background.png");  background-color: #cccccc;}
        .wrapper{ width: 350px; padding: 20px; }
    </style>
</head>
<body>
    <p><a style="position: absolute; left: 85%; top: 1%; width: 10em;" href="logout.php" class="btn btn-danger">Log Out</a></p>
    <div class="wrapper" style="position:relative; top: 53%; left: 30%; margin-top: 4%; width: 500px;">
    	<h2 style="color: white; text-shadow:0 0 4px black, 0 0 4px black, 0 0 4px black, 0 0 4px black, 0 0 4px black, 0 0 4px black, 0 0 4px black, 0 0 4px black, 0 0 4px black, 0 0 4px black, 0 0 4px black, 0 0 4px black, 0 0 4px black, 0 0 4px black, 0 0 4px black, 0 0 4px black, 0 0 4px black, 0 0 4px black, 0 0 4px black, 0 0 4px black; font-weight: bold; text-align: center; margin-left: 130px;">Send a Mail</h2>
    	<p style="color: white; text-align: left">Fill the inputs, and press Send Mail when you are ready!.</p>
    	<form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
    	<table width="550px">
    	<tr>
    	 <td valign="top">
    	  <label style="color: white;" for="first_name">First Name *</label>
    	 </td>
    	 <td valign="top">
    	  <input  class="form-control" type="text" name="first_name" maxlength="50" size="30">
    	 </td>
    	</tr>
    	<tr>
    	 <td valign="top"">
    	  <label style="color: white;" for="last_name">Last Name *</label>
    	 </td>
    	 <td valign="top">
    	  <input  class="form-control" type="text" name="last_name" maxlength="50" size="30">
    	 </td>
    	</tr>
    	<tr>
    	 <td valign="top">
    	  <label style="color: white;" for="email">Email Address *</label>
    	 </td>
    	 <td valign="top">
    	  <input  class="form-control" type="text" name="email" maxlength="80" size="30">
    	 </td>
    	</tr>
    	<tr>
    	 <td valign="top">
    	  <label style="color: white;" for="telephone">Telephone Number</label>
    	 </td>
    	 <td valign="top">
    	  <input  class="form-control" type="text" name="telephone" maxlength="30" size="30">
    	 </td>
    	</tr>
    	<tr>
    	 <td valign="top">
    	  <label style="color: white;" for="comments">Comments *</label>
    	 </td>
    	 <td valign="top">
    	  <textarea  class="form-control" name="comments" maxlength="1000" cols="25" rows="6"></textarea>
    	 </td>
    	</tr>
    	<tr>
    	 <td colspan="2" style="text-align:center">
    	  <input style="position: relative; left: 80px; width: 200px;" type="submit" class="btn btn-primary" value="Send Mail">
    	 </td>
    	</tr>
    	</table>
    	</form>
    	<span style="position: relative;left: 70%;font-weight: bold; color: #4cda4f;" class="text-success"><?php echo $msg_status; ?>   
        </span>
    </div>    
</body>
</html>