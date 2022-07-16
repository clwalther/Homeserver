<?php
    include "error.php";
    include "client.php";
    include "database.php";
    include "env.php";
    include "server.php";
    include "utils.php";

    $PATH = __DIR__."/../";

    $utils  = new Utils("/");
    $env    = new Env();
    $error  = new Errors();
    $server = new Server();

    $NAME  = $utils->getCookie("USERNAME");
    $ID    = $utils->getCookie("USERID");
    $EMAIL = $utils->getCookie("EMAIL");
    $AUTH  = $utils->getCookie("AUTH");
    $TYPE  = $utils->getCookie("TYPE");

    $client = new Client($NAME, $ID, $EMAIL, $AUTH, $TYPE);

    $SERVERNAME   = $env->getEnv("SERVERNAME");
    $USERLOGIN    = $env->getEnv("USERLOGIN");
    $USERPASSWORD = $env->getEnv("USERPASSWORD");
    $DATABASENAME = $env->getEnv("DATABASENAME");

    $database = new Database($SERVERNAME, $DATABASENAME, $USERLOGIN, $USERPASSWORD);
    $database->connect();
?>