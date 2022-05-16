<?php
class Error
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
        $errorMsg = "Unable to Connect to the Database please conntact an admin immediately.";
        die($errorMsg);
    }

    function mysqlCouldNotChangeDatabase($action) {
        $this->errorFeedback("mysqlCouldNotDelteError", 2);
        $errorMsg = "Unable to perform action (".$action.") in database.";
        die($errorMsg);
    }
    // CLIENT
    function clientEmailPasswordIncorrect() {
        $errorMsg = "Given email and password do not match.";
        die($errorMsg);
    }

    function clientEmailAuthIncorrect() {
        $errorMsg = "Given authorization code is incorrect.";
        die($errorMsg);
    }

    function clientEmailAlreadyUsed() {
        $errorMsg = "Given email alredy recoginzed.";
        die($errorMsg);
    }

    function clientPasswordIsNotRepeatedPassword() {
        $errorMsg = "Given passwords are not equal.";
        die($errorMsg);
    }
}
?>