
    <?php
    // Include config file
    require_once 'config.php';

    // Define variables and initialize with empty values
    $username = $password = "";
    $username_err = $password_err = "";

    // Processing form data when form is submitted
    if($_SERVER["REQUEST_METHOD"] == "POST"){
        // Check if username is empty
        if(empty(trim($_POST["username"]))){
            $username_err = 'Please enter username.';
        } else{
            $username = trim($_POST["username"]);
        }
        // Verificar que se ingreso el pass
        if(empty(trim($_POST['password']))){
            $password_err = 'Please enter your password.';
        } else{
            $password = trim($_POST['password']);
        }

        // Validar credenciales D:
        if(empty($username_err) && empty($password_err)){
            // quiery a realizar
            $sql = "SELECT username, password FROM users WHERE username = '$username';";
            if($stmt = mysqli_prepare($link, $sql)){
                // Bind variables al statemnt como parametros
                //mysqli_stmt_bind_param($stmt, "s", $param_username);
                // Setear parametros
                //$param_username = $username;
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
				            //if($password==$hashed_password){
                                /* Password is correct, so start a new session and
                                save the username to the session */
                                echo "<H1>EXITO! Ingresado como <b>$username</b></H1>";
				                session_start();
                                $_SESSION['username'] = $username;
                                header("location: SMTPClient.php");
                            } else{
                                // Display an error message if password is not valid
                                $password_err = 'Password ingresado no es valido.';
                            }
                        }
                    } else{
                        // Display an error message if username doesn't exist
                        $username_err = 'No se encuentra ese username.';
                    }
                } else{
                    echo "Oops! ALgo salio mal. Intenta de nuevo D:.";
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
            <p style="color: white;">Ingresa tus credenciales para iniciar sesion.</p>
            <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                <div class="form-group <?php echo (!empty($username_err)) ? 'has-error' : ''; ?>">
                    <label style="color: white;">Username</label>
                    <input type="text" name="username"class="form-control" value="<?php echo $username; ?>">
                    <span class="help-block"><?php echo $username_err; ?></span>
                </div>    
                <div class="form-group <?php echo (!empty($password_err)) ? 'has-error' : ''; ?>">
                    <label style="color: white;">Password</label>
                    <input type="password" name="password" class="form-control">
                    <span class="help-block"><?php echo $password_err; ?></span>
                </div>
                <div class="form-group">
                    <input type="submit" class="btn btn-primary" value="Login">
                </div>
                <p style="color: white;">No tienes cuenta? <a href="register.php">Registrate</a>.</p>
            </form>
        </div>    
    </body>
    </html>


