<?php
openlog('smtp_php_register', LOG_CONS | LOG_NDELAY | LOG_PID, LOG_USER | LOG_PERROR);
// Include config file
require_once 'config.php';

// Define variables and initialize with empty values
$username = $password = $confirm_password = "";
$username_err = $password_err = $confirm_password_err = "";
$regex = '/[^a-z_0-9]/i';

// Processing form data when form is submitted
if($_SERVER["REQUEST_METHOD"] == "POST"){
    // Validate username
    if(empty(trim($_POST["username"]))){
        syslog(LOG_WARNING, 'WARNING: Empty field in username.');
        $username_err = "Please enter a username.";
    }elseif (preg_match($regex, trim($_POST["username"]))) {
            syslog(LOG_WARNING, 'WARNING: Extraneous characters in username field.');
            $username_err = 'Please, only letters, numbers and underscores.';
    }  
    else{
        // Prepare a select statement
        $sql = "SELECT id FROM users WHERE username = ?";
        if($stmt = mysqli_prepare($link, $sql)){
            // Bind variables to the prepared statement as parameters
            mysqli_stmt_bind_param($stmt, "s", $param_username);
            // Set parameters
            $param_username = trim($_POST["username"]);
            // Attempt to execute the prepared statement
            if(mysqli_stmt_execute($stmt)){
                /* store result */
                mysqli_stmt_store_result($stmt);
                if(mysqli_stmt_num_rows($stmt) == 1){
                    $username_err = "This username is already taken.";
                    syslog(LOG_WARNING, 'WARNING: Username already taken.');
                } else{
                    $username = trim($_POST["username"]);
                }
            } else{
                echo "Oops! Something went wrong. Please try again later.";
                syslog(LOG_ERR, 'ERROR: Failed validating existing username.');
             }
        }
        // Close statement
        mysqli_stmt_close($stmt);
    }

    // Validate password
    if(empty(trim($_POST['password']))){
        $password_err = "Please enter a password.";  
        syslog(LOG_WARNING, 'WARNING: Empty field in password.');   
    }elseif (preg_match($regex, trim($_POST["password"]))) {
            syslog(LOG_WARNING, 'WARNING: Extraneous characters in password field.');
            $password_err = 'Please, only letters, numbers and underscores.';
    }elseif(strlen(trim($_POST['password'])) < 6){
        $password_err = "Password must have atleast 6 characters.";
        syslog(LOG_WARNING, 'WARNING: Password longer than max characters.'); 
    } else{
        $password = trim($_POST['password']);
    }

    // Validate confirm password
    if(empty(trim($_POST["confirm_password"]))){
        $confirm_password_err = 'Please confirm password.';
        syslog(LOG_WARNING, 'WARNING: Empty field in password.');      
    }elseif (preg_match($regex, trim($_POST["confirm_password"]))) {
            syslog(LOG_WARNING, 'WARNING: Extraneous characters in confirm_password field.');
            $confirm_password_err = 'Please, only letters, numbers and underscores.';
    }else{
        $confirm_password = trim($_POST['confirm_password']);
        if($password != $confirm_password){
            $confirm_password_err = 'Password did not match.';
            syslog(LOG_WARNING, 'WARNING: Confirmation password did not match.');
        }
    }

    // Check input errors before inserting in database
    if(empty($username_err) && empty($password_err) && empty($confirm_password_err)){
        syslog(LOG_INFO, 'INFO: Attempting to create account.');
        // Prepare an insert statement
        $sql = "INSERT INTO users (username, password) VALUES (?, ?)";
        if($stmt = mysqli_prepare($link, $sql)){
            // Bind variables to the prepared statement as parameters
            mysqli_stmt_bind_param($stmt, "ss", $param_username, $param_password);
            // Set parameters
            $param_username = $username;
            $param_password = password_hash($password, PASSWORD_DEFAULT); // Creates a password hash
            // Attempt to execute the prepared statement
            if(mysqli_stmt_execute($stmt)){
                syslog(LOG_INFO, 'INFO: Account created succesfully.');
                // Redirect to login page
                header("location: login.php");
            } else{
                echo "Something went wrong. Please try again later.";
                syslog(LOG_ERR, 'ERROR: Error inserting into database.');
            }
        }
        // Close statement
        mysqli_stmt_close($stmt);
    }
    // Close connection
    mysqli_close($link);
}
?>

    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <title>Sign Up</title>
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.css">
        <style type="text/css">
            body{ font: 14px sans-serif; background-image: url("background.png");  background-color: #cccccc;}
            .wrapper{ width: 350px; padding: 20px; }
        </style>
    </head>
    <body>
        <div class="wrapper" style="position:relative; top: 53%; left: 37%; margin-top: 6%;">
            <h2 style="color: white;">Sign Up</h2>
            <p style="color: white;">Please fill this form to create an account.</p>
            <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                <div class="form-group <?php echo (!empty($username_err)) ? 'has-error' : ''; ?>">
                    <label style="color: white;">Username</label>
                    <input type="text" name="username"class="form-control" value="<?php echo $username; ?>">
                    <span style="color: red;" class="help-block"><?php echo $username_err; ?></span>
                </div>    
                <div class="form-group <?php echo (!empty($password_err)) ? 'has-error' : ''; ?>">
                    <label style="color: white;">Password</label>
                    <input type="password" name="password" class="form-control" value="<?php echo $password; ?>">
                    <span style="color: red;" class="help-block"><?php echo $password_err; ?></span>
                </div>
                <div class="form-group <?php echo (!empty($confirm_password_err)) ? 'has-error' : ''; ?>">
                    <label style="color: white;">Confirm Password</label>
                    <input type="password" name="confirm_password" class="form-control" value="<?php echo $confirm_password; ?>">
                    <span style="color: red;" class="help-block"><?php echo $confirm_password_err; ?></span>
                </div>
                <div class="form-group">
                    <input type="submit" class="btn btn-primary" value="Submit">
                    <input type="reset" class="btn btn-default" value="Reset">
                </div>
                <p style="color: white;">Already have an account? <a href="login.php">Login here</a>.</p>
            </form>
        </div>    
    </body>
    </html>