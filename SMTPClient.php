<?php
openlog('smtp_php', LOG_CONS | LOG_NDELAY | LOG_PID, LOG_USER | LOG_PERROR);
// Initialize the session
session_start();

// If session variable is not set it will redirect to login page
if(!isset($_SESSION['username']) || empty($_SESSION['username'])){
  syslog(LOG_ERR, 'ERROR: Attempt to access unauthorized session.');
  header("location: login.php");
  exit;
}

$msg_status = "";
$server_ip = "127.0.0.1";
$server_port = 25;

function SocketConnect($server_ip, $server_port){
	set_time_limit(5);
	 
	if (($socket = socket_create(AF_INET, SOCK_STREAM, 0)) === false) {
        syslog(LOG_ERR, 'ERROR: Could not create socket.');
	    $msg_status = $msg_status . "Could not create socket\n";
	}else{
        syslog(LOG_INFO, 'INFO: Socket created succesfully');
		$msg_status = $msg_status . "Socket created succesfully!\n";
	}
	 
	if (($connection = socket_connect($socket, $server_ip, $server_port)) === false) {
        syslog(LOG_ERR, 'ERROR: Could not connect to server.');
	    $msg_status = $msg_status . "Could not connect to server\n";
        return 0;
	}else{
        syslog(LOG_INFO, 'INFO: Socket succesfully connected.');
		$msg_status = $msg_status . "Succesfully connected!!\n";
        return $socket;
	}
}

function send($socket, $message){
    socket_write($socket, $message, strlen($message));
}

function receive($socket){
    if (($data = socket_read($socket, 1024)) === false) {
        syslog(LOG_ERR, 'ERROR: Could not read input from socket.');
        $msg_status = $msg_status . "Could not read input\n";
        return 0;
    } else {
        //echo "Server sent:" . $data . "\n";
        return $data;
    }
}

if($_SERVER["REQUEST_METHOD"] == "POST"){
    syslog(LOG_INFO, 'INFO: Attempting to send mail.');
	// validation expected data exists
    if(!isset($_POST['rcpt_to']) ||
        !isset($_POST['subject']) ||
        !isset($_POST['content'])) {
        syslog(LOG_WARNING, 'WARNING: Empty fields in form.');
        $msg_status = $msg_status . "We are sorry, but there appears to be a problem with the form you submitted\n";       
    }
    $rcpt_to = $_POST['rcpt_to']; // required
    $subject = $_POST['subject']; // required
    $content = $_POST['content']; // required
    $mail_from = $_SESSION['username'];

    $rcpt_to_array = explode(",",$rcpt_to);

    // echo "From: "."$mail_from\n";
    // echo "To: "."$rcpt_to\n";
    // echo "Subject: "."$subject\n";
    // echo "Content: "."$content\n";
    $server_socket = SocketConnect($server_ip, $server_port);
    if($server_socket!==0){
        syslog(LOG_INFO, 'INFO: Starting SMTP transmission.');
        $data = receive($server_socket);
        if (strpos($data, '220') !== false) {
            send($server_socket, "HELO");
            $data = receive($server_socket);
            if (strpos($data, '250') !== false){
                send($server_socket, "MAIL FROM:".$mail_from."@mail.com");
                $data = receive($server_socket);
                if (strpos($data, '250') !== false){
                    for($x = 0; $x < count($rcpt_to_array); $x++) {
                        send($server_socket, "RCPT TO:".$rcpt_to_array[$x]);
                        $data = receive($server_socket);
                    }
                    if (strpos($data, '250') !== false){
                        send($server_socket, "DATA");
                        $data = receive($server_socket);
                        if (strpos($data, '354') !== false){
                            $mail_content = "Subject: ".$subject."\n".$content;
                            send($server_socket, $mail_content);
                            send($server_socket, ".\r\n");
                            $data = receive($server_socket);
                            if (strpos($data, '250') !== false){
                                syslog(LOG_INFO, 'INFO: Finished SMTP transmission succesfully.');
                                $msg_status = $msg_status . "SENT!";
                            }else{
                                syslog(LOG_ERR, 'ERROR: Did not receive answer for finish.');
                            }
                        }else{
                            syslog(LOG_ERR, 'ERROR: Did not receive answer for DATA.');
                        }
                    }else{
                        syslog(LOG_ERR, 'ERROR: Did not receive answer for RCPT TO.');
                    }
                }else{
                    syslog(LOG_ERR, 'ERROR: Did not receive answer for MAIL FROM.');
                }
            }else{
                syslog(LOG_ERR, 'ERROR: Did not receive answer for HELO.');
            }
        }else{
            syslog(LOG_ERR, 'ERROR: Did not receive first message from server.');
        }
        socket_close($server_socket);
    }   
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
    	<p style="color: white; text-align: left">Please, separate mails with ",".</p>
    	<form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
    	<table width="550px">
    	<tr>
    	 <td valign="top">
    	  <label style="color: white;" for="rcpt_to">To:</label>
    	 </td>
    	 <td valign="top">
    	  <input  class="form-control" type="text" name="rcpt_to" maxlength="50" size="30">
    	 </td>
    	</tr>
    	<tr>
    	 <td valign="top"">
    	  <label style="color: white;" for="subject">Subject:</label>
    	 </td>
    	 <td valign="top">
    	  <input  class="form-control" type="text" name="subject" maxlength="50" size="30">
    	 </td>
    	</tr>
    	<tr>
    	 <td valign="top">
    	  <label style="color: white;" for="content">Content:</label>
    	 </td>
    	 <td valign="top">
    	  <textarea  class="form-control" name="content" maxlength="1000" cols="25" rows="6"></textarea>
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