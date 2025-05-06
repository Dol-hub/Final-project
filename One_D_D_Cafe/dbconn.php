<?php

define("HOSTNAME", "localhost");
define("USERNAME", "root");
define("PASSWORD", "");
define("DATABASE", "crud_student");

$connect = mysqli_connect(HOSTNAME, USERNAME, PASSWORD, DATABASE);

if(!$connect){
    die("Connection Failed");
}

else{
    //echo "Connect Success";
}


?>