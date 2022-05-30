<?php
    include "error.php";
    include "client.php";
    include "database.php";
    include "env.php";

    class Utils
    {
        private $PATH;
        private $DOMAIN   = "192.168.0.203";
        private $SECURE   = FALSE;
        private $HTTPONLY = FALSE;

        function __construct($PATH) {
            $this->PATH = $PATH;
        }

        function setCookie($key, $value) {
            $expires  = 0; // <------ DEBUG
            $path     = $this->PATH;
            $domain   = $this->DOMAIN;
            $secure   = $this->SECURE;
            $httponly = $this->HTTPONLY;

            setcookie($key,
                      $value,
                      $expires,
                      $path,
                      $domain,
                      $secure,
                      $httponly);
        }

        function getCookie($key) {
            return $_COOKIE[$key];
        }

        function removeCookie($key) {
            $value    = NULL;
            $expires  = -1;
            $path     = $this->PATH;
            $domain   = $this->DOMAIN;
            $secure   = $this->SECURE;
            $httponly = $this->HTTPONLY;

            setcookie($key,
                      $value,
                      $expires,
                      $path,
                      $domain,
                      $secure,
                      $httponly);
        }

        function getExpireDate() {
            $expireDate = time()*24*60*60;
            return $expireDate;
        }

        function bin2bit($input) {
            $bits = "";
            $stop = strlen($input);
            for($index = 0; $index < $stop; $index++) {
                $byte  = decbin(ord($input[$index]));
                $bits .= substr("00000000", 0, 8 - strlen($byte)) . $byte;
            }
            return $bits;
        }

        function bit2dec($input) {
            $dec = 0;
            $input = strrev($input);
    
            for($index = 0; $index < strlen($input); $index++) {
                if($input[$index] == "1") {
                    $dec += pow(2, $index);
                }
            }
            return $dec;
        }

        function changeLocation($location, $target) {
            echo "<script>window.open('".$location."', '".$target."')</script>\n";
        }
    }

    $PATH = __DIR__."/../";

    $utils  = new Utils("/");
    $env    = new Env();
    $error  = new Errors();

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