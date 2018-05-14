<?php
openlog('smtp_php_logout', LOG_CONS | LOG_NDELAY | LOG_PID, LOG_USER | LOG_PERROR);
// Initialize the session
session_start();
    
// Unset all of the session variables
$_SESSION = array();

// Destroy the session.
session_destroy();
syslog(LOG_INFO, 'INFO: User logged out.');
// Redirect to login page
header("location: login.php");
exit;

?>

