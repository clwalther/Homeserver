<?php
class Database
{
    private $SERVERNAME;
    private $DATABASENAME;
    private $USERLOGIN;
    private $USERPASSWORD;
    private $CONNECTION;

    function __construct($SERVERNAME, $DATABASENAME, $USERLOGIN, $USERPASSWORD) {
        $this->SERVERNAME   = $SERVERNAME;
        $this->DATABASENAME = $DATABASENAME;
        $this->USERLOGIN    = $USERLOGIN;
        $this->USERPASSWORD = $USERPASSWORD;
    }

    function helloworld() {
        return "test";
    }

    function connect() {
        for ($tries = 0; $tries < 3; $tries++) {
            $this->CONNECTION = new mysqli($this->SERVERNAME, $this->USERLOGIN, $this->USERPASSWORD, $this->DATABASENAME);
            if ($this->checkValidConnection()) {
                break;
            }
        }
    }

    function checkValidConnection() {
        $error = $GLOBALS["error"];
        if ($this->CONNECTION->connect_error) {
            $error->mysqlConnection();
            return FALSE;
        } else {
            return TRUE;
        }
    }

    function close() {
        $this->CONNECTION->close();
    }

    function select($TABLE, $COLUMNS, $LIKE) {
        $COLUMNS = join(', ', $COLUMNS);
        $LIKE    = $this->formatLike($LIKE);

        $query  = "SELECT ".$COLUMNS." FROM ".$TABLE."".$LIKE.";";
        $result = $this->CONNECTION->query($query);
        
        return $result;
    }

    function insert($TABLE, $ARRAY) {
        $keys   = join(', ', array_keys($ARRAY));

        $values = array_values($ARRAY);
        $values = $this->formatInsertValues($values);
        $values = join(', ', $values);

        $query  = "INSERT INTO ".$TABLE." (".$keys.") VALUES (".$values.");";
        $result = $this->CONNECTION->query($query);
        $this->checkQueryReturn($result, "INSERT INTO");

        return $result;
    }

    function delete($TABLE, $LIKE) {
        $LIKE = $this->formatLike($LIKE);

        $query  = "DELETE FROM ".$TABLE." ".$LIKE.";";
        $result = $this->CONNECTION->query($query);
        $this->checkQueryReturn($result, "DELETE");

        return $result;
    }

    function formatLike($array) {
        if ($array != NULL) {
            $REQUIRED = [];
            foreach($array as $key => $value) {
                array_push($REQUIRED, $key."='".$value."'");
            }
            $LIKE  = " WHERE ";
            $LIKE .= join(' AND ', $REQUIRED);
        } else {
            $LIKE  = "";
        }
        return $LIKE;
    }

    function formatInsertValues($values) {
        $array = [];
        foreach ($values as $item) {
            if (gettype($item) == 'string') {
                $item = "'".$item."'";
            }
            array_push($array, $item);            
        }
        return $array;
    }

    function checkQueryReturn($return, $action) {
        $error = $GLOBALS["error"];

        if ($return == FALSE) {
            $error->mysqlCouldNotChangeDatabase($action);
        }
    }
}
?>