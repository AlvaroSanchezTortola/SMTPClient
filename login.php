
    <?php
    openlog('smtp_php_login', LOG_CONS | LOG_NDELAY | LOG_PID, LOG_USER | LOG_PERROR);
    session_start();
    // Include config file
    require_once 'config.php';

    // Define variables and initialize with empty values
    $username = $password = "";
    $username_err = $password_err = "";
    $regex = '/[^a-z_0-9]/i';
    
    if($_SESSION['banned']=== true){
        sleep(30);
        $_SESSION['banned']=false;
    }

    if($_SESSION['tries'] === null){
        $_SESSION['tries']=3;
    }

    if ($_SESSION['tries']==0) {
        syslog(LOG_WARNING, 'WARNING: Maximum number of login attempts reached, timing out.');
        $password_err = "Max number of tries reached, try again later!";
        $username_err = "Max number of tries reached, try again later!";
        $_SESSION['banned']=true;
        $_SESSION['tries']=3;
    }

    // Processing form data when form is submitted
    if($_SERVER["REQUEST_METHOD"] == "POST" && $_SESSION['tries']>0){
        // Check if username is empty
        if(empty(trim($_POST["username"]))){
            syslog(LOG_WARNING, 'WARNING: Empty field in username.');
            $_SESSION['tries']--;
            $username_err = 'Please enter username.';
        }elseif (preg_match($regex, trim($_POST["username"]))) {
            syslog(LOG_WARNING, 'WARNING: Extraneous characters in username field.');
            $_SESSION['tries']--;
            $username_err = 'Please, only letters, numbers and underscores.';
        } 
        else{
            $username = trim($_POST["username"]);
        }

        // Verificar que se ingreso el pass
        if(empty(trim($_POST['password']))){
            syslog(LOG_WARNING, 'WARNING: Empty field in password.');
            $_SESSION['tries']--;
            $password_err = 'Please enter your password.';
        }elseif (preg_match($regex, trim($_POST["password"]))) {
            syslog(LOG_WARNING, 'WARNING: Extraneous characters in password field.');
            $_SESSION['tries']--;
            $password_err = 'Please, only letters, numbers and underscores.';
        }  
        else{
            $password = trim($_POST['password']);
        }

        // Validar credenciales D:
        if(empty($username_err) && empty($password_err)){
            syslog(LOG_INFO, 'INFO: Initializing validation of credentials.');
            // quiery a realizar
            //    $sql = "SELECT username, password FROM users WHERE username = '$username';";
            $sql = "SELECT username, password FROM users WHERE username = ?";
                if($stmt = mysqli_prepare($link, $sql)){
                // Bind variables al statemnt como parametros
                mysqli_stmt_bind_param($stmt, "s", $param_username);
                // Setear parametros
                $param_username = $username;
                // Ejecutar el statement
                if(mysqli_stmt_execute($stmt)){
                    // Almacenar el resultado
                    mysqli_stmt_store_result($stmt);
                    // Verificar si existe el user, si si, validar pass
                    if(mysqli_stmt_num_rows($stmt) == 1){
                        // Bind resultado
                        mysqli_stmt_bind_result($stmt, $username, $hashed_password);
                        //$hashed=md5($password);
                        // echo "$hashed";
                        echo "$hashed_password";
                        if(mysqli_stmt_fetch($stmt)){
                            if(password_verify($password, $hashed_password)){
                                syslog(LOG_INFO, 'INFO: User succesfully logged in.');
                                $_SESSION = array();
                                session_destroy();
				                session_start();
                                $_SESSION['username'] = $username;
                                header("location: SMTPClient.php");
                            } else{
                                // Display an error message if password is not valid
                                syslog(LOG_ERR, 'ERROR: Attempt of login with invalid password.');
                                $_SESSION['tries']--;
                                $password_err = 'Invalid password, try again.';
                            }
                        }
                    } else{
                        // Display an error message if username doesn't exist
                        syslog(LOG_ERR, 'ERROR: Attempt of login with invalid username.');
                        $_SESSION['tries']--;
                        $username_err = 'Wrong username, try again.';
                    }
                } else{
                    syslog(LOG_ERR, 'ERROR: Attempt of login failed.');
                    $_SESSION['tries']--;
                    $password_err =  "Oops! Something went wrong, try again.";
                }
            }
            // Close statement
            mysqli_stmt_close($stmt);
        }
        // Close connection
        mysqli_close($link);
        echo $_SESSION['tries'];
    }
    ?>
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <title>Login</title>
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.css">
        <style type="text/css">
            body{ font: 14px sans-serif;  background-image: url("background.png");  background-color: #cccccc;}
            .wrapper{ width: 350px; padding: 20px; }
        </style>
    </head>
    <body>
        <div class="wrapper" style="position:relative; top: 53%; left: 37%; margin-top: 6%;">
            <h2 style="color: white;">Login</h2>
            <p style="color: white;">Enter your username and password to send a mail.</p>
            <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                <div class="form-group <?php echo (!empty($username_err)) ? 'has-error' : ''; ?>">
                    <label style="color: white;">Username</label>
                    <input type="text" name="username"class="form-control" value="<?php echo $username; ?>">
                    <span style="color: red;" class="help-block"><?php echo $username_err; ?></span>
                </div>    
                <div class="form-group <?php echo (!empty($password_err)) ? 'has-error' : ''; ?>">
                    <label style="color: white;">Password</label>
                    <input type="password" name="password" class="form-control">
                    <span style="color: red;" class="help-block"><?php echo $password_err; ?></span>
                </div>
                <div class="form-group">
                    <input type="submit" class="btn btn-primary" value="Login">
                </div>
                <p style="color: white;">No tienes cuenta? <a href="register.php">Registrate</a>.</p>
            </form>
        </div>    
    </body>
    </html>


