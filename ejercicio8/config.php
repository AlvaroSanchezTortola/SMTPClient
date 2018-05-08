

    <?php

    /* Database credentials. Assuming you are running MySQL

    server with default setting (user 'root' with no password) */

    define('DB_SERVER', 'localhost');

    define('DB_USERNAME', 'alvaro');

    define('DB_PASSWORD', 'toor');

    define('DB_NAME', 'ejercicio');

     

    /* Attempt to connect to MySQL database */

    $link = mysqli_connect(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);

     

    // Check connection

    if($link === false){

        die("ERROR: Could not connect. " . mysqli_connect_error());

    }else {
    echo "Connection Successful";
}

    ?>


