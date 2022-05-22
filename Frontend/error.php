<?php
class Errors
{
    private $ERROR_LOG = "/home/pi/Public/Homeserver/Backend/error.log";

    function __construct() {}
    /*
        SEVERITY LEVELS:
            1) low    [only effects a few users];
            2) medium [effects a high number of users];
            3) high   [system is unusable];
    */
    function errorFeedback($error, $severity) {
        $file = fopen($this->$ERROR_LOG, "a");
        $date = date("Y-m-d H:s:i");
        $msg  = $date."; SEVERITY: ".$severity."; ERROR: ".$error;
        fwrite($file, $msg);
        fclose($file);
    }
    // MYSQL
    function mysqlConnection() {
        $this->errorFeedback("mysqlConnectionError", 3);
        $errorMsg = "ERROR WARNING:  Unable to Connect to the Database please conntact an admin immediately.\n";
        print($errorMsg);
    }

    function mysqlCouldNotChangeDatabase($action) {
        $this->errorFeedback("mysqlCouldNotDelteError", 2);
        $errorMsg = "ERROR WARNING:  Unable to perform action (".$action.") in database.\n";
        die($errorMsg);
    }
    // CLIENT
    function clientEmailPasswordIncorrect() {
        $errorMsg = "ERROR WARNING:  Given email and password do not match.\n";
        print($errorMsg);
    }

    function clientEmailAuthIncorrect() {
        $errorMsg = "ERROR WARNING:  Given authorization code is incorrect.\n";
        print($errorMsg);
    }

    function clientEmailAlreadyUsed() {
        $errorMsg = "ERROR WARNING:  Given email alredy recoginzed.\n";
        print($errorMsg);
    }

    function clientPasswordIsNotRepeatedPassword() {
        $errorMsg = "ERROR WARNING:  Given passwords are not equal.\n";
        print($errorMsg);
    }
}
?>