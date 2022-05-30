<?php
class Client
{
    public $NAME;
    public $ID;
    public $EMAIL;
    public $AUTH;

    private $TYPE;
    private $PATH_STORE_IMG;
    private $PATH_STORE_DB;

    function __construct($NAME, $ID, $EMAIL, $AUTH, $TYPE) {
        $this->NAME   = $NAME;
        $this->ID     = $ID;
        $this->EMAIL  = $EMAIL;
        $this->AUTH   = $AUTH;
        $this->TYPE   = $TYPE;

        $this->PATH_STORE_IMG = $GLOBALS["PATH"]."Frontend/assets/DATABASE/USERIMG/";
        $this->PATH_STORE_DB  = $GLOBALS["PATH"]."Backend/DATABASE/SHARE/";
    }

    function signIn($EMAIL, $PASSWORD) {
        $utils = $GLOBALS["utils"];
        $error = $GLOBALS["error"];
        $database = $GLOBALS["database"];
        // AUTH
        $TABLE   = "USERS";
        $COLUMNS = ["*"];
        $LIKE    = [
            "EMAIL"    => $EMAIL,
            "PASSWORD" => $PASSWORD
        ];

        $result = $database->select($TABLE, $COLUMNS, $LIKE);
        if ($result->num_rows == 1) {
            // AUTH SUCCCESS
            while ($answer = $result->fetch_assoc()) {
                $this->getDataFromQuery($answer);
                $utils->changeLocation("/", "_self");
                // REDIRECT
                return 0; # 0 => no errors;
            }
        } else {
            $error->clientEmailPasswordIncorrect();
            return 1; # 1 => clientEmailPasswordIncorrect;
        }
    }

    function signUp($USERNAME, $EMAIL, $PASSWORD, $REPEATEPASSWORD) {
        $utils = $GLOBALS["utils"];
        $error = $GLOBALS["error"];
        $database = $GLOBALS["database"];
        
        if ($PASSWORD == $REPEATEPASSWORD) {  
            // PASSWORD IS GIVEN TWICE   
            $LIKE   = [ "EMAIL" => $EMAIL ];
            $result = $database->select("TEMP", ["*"], $LIKE);
            if ($result->num_rows > 0) {
                // EMAIL ALREADY TRYING TO LOGIN
                $database->delete("TEMP", $LIKE);
            }

            $LIKE   = [ "EMAIL" => $EMAIL ];
            $result = $database->select("USERS", ["*"], $LIKE);
            if ($result->num_rows == 0) {
                // EMAIL NOT ALREADY USED
                $CODE = $this->generateCode();
                $AUTH = 0;
                // INSERT
                $ARRAY = [
                    "CODE"     => $CODE,
                    "USERNAME" => $USERNAME,
                    "EMAIL"    => $EMAIL,
                    "PASSWORD" => $PASSWORD,
                    "AUTH"     => $AUTH
                ];
                $database->insert("TEMP", $ARRAY);
                // REDIRECT
                $utils->setCookie("EMAIL", $EMAIL);
                $utils->changeLocation("./auth", "_self");
                return 0; # 0 => no errors;
            } else {
                // EMAIL ALREADY USED
                $error->clientEmailAlreadyUsed();
                return 2; # 2 => clientEmailAlreadyUsed;
            }
        } else {
            // PASSWORD AND REPEATED PASSWORD ARE NOT EQUAL
            $error->clientPasswordIsNotRepeatedPassword();
            return 1; # 1 => clientPasswordIsNotRepeatedPassword;
        }
    }
    
    function signOut() {
        $utils = $GLOBALS["utils"];

        $utils->removeCookie("AUTH");
        $utils->removeCookie("EMAIL");
        $utils->removeCookie("USERID");
        $utils->removeCookie("USERNAME");
    }

    function authEmailToAccount($CODE) {
        $utils = $GLOBALS["utils"];
        $error = $GLOBALS["error"];
        $database = $GLOBALS["database"];
        
        $LIKE = [ "CODE" => $CODE ];
        $result = $database->select("TEMP", ["*"], $LIKE);
        if ($result->num_rows == 1) {
            // EAMIL AUTHORIZED
            while ($answer = $result->fetch_assoc()) {
                $ARRAY = [
                    "USERNAME" => $answer["USERNAME"],
                    "EMAIL"    => $answer["EMAIL"],
                    "TYPE"     => "USER",
                    "PASSWORD" => $answer["PASSWORD"],
                    "AUTH"     => intval($answer["AUTH"])
                ];
                $database->insert("USERS", $ARRAY);
                $database->delete("TEMP", $LIKE);
                
                $result = $database->select("USERS", ["*"], $ARRAY);
                while ($answer = $result->fetch_assoc()) {
                    $this->getDataFromQuery($answer);
                    $this->generateImg($this->ID, $this->NAME);
                    $this->generateDB($this->ID);
                    // REDIRECT
                    $utils->changeLocation("/", "_self");
                    return 0; # 0 => no errors;
                }
                
            }
        } else {
            $error->clientEmailAuthIncorrect();
            return 1; # 1 => clientEmailAuthIncorrect;
        }
    }
    
    function sendEmail($TYPE, $RECEIVER, $CONTEXT) {
        $command = "/bin/python ".$GLOBALS["PATH"]."App/Auth.py ".$TYPE." ".$RECEIVER." ".$CONTEXT;
        shell_exec($command);
    }
    
    function getDataFromQuery($answer) {
        $utils = $GLOBALS["utils"];
        // GET
        $this->NAME  = $answer["USERNAME"];
        $this->ID    = $answer["USERID"];
        $this->EMAIL = $answer["EMAIL"];
        $this->AUTH  = $answer["AUTH"];
        $this->TYPE  = $answer["TYPE"];
        // COOKIES
        $utils->setCookie("USERNAME", $this->NAME);
        $utils->setCookie("USERID", $this->ID);
        $utils->setCookie("EMAIL", $this->EMAIL);
        $utils->setCookie("AUTH", $this->AUTH);
        $utils->setCookie("TYPE", $this->TYPE);
    }

    function generateCode() {
        $database = $GLOBALS["database"];
        $code = NULL;
        $LIKE = [ "CODE" => $code ];

        while ($database->select("TEMP", ["*"], $LIKE)->num_rows > 0 or $code == NULL) {
            $code = rand(100000, 999999);
            $LIKE = [ "CODE" => $code ];
        }
        return $code;
    }

    function generateImg($FILENAME, $CONTEXT) {
        $utils = $GLOBALS["utils"];
        
        $WIDTH  = 4;
        $HEIGHT = 4;
        $SIZE   = 10;
        $COLOR_DEPTH = 4;

        $hash = hash("sha256", $CONTEXT);
        $hash = $utils->bin2bit(hex2bin($hash));

        $chunks = $this->iterateHash($hash, $COLOR_DEPTH);
        $data   = $this->generateSVGData($chunks, $HEIGHT, $WIDTH, $SIZE, $COLOR_DEPTH);

        $file = fopen($this->PATH_STORE_IMG.$FILENAME.".svg", "w");
        fwrite($file, $data);
        fclose($file);
    }

    function iterateHash($hash, $COLOR_DEPTH) {
        $utils  = $GLOBALS["utils"];
        $chunks = array();
        $nBlock = strlen($hash) / $COLOR_DEPTH;
        for ($index = 0; $index < $nBlock; $index++) {
            $data = "";
            for ($depth = 0; $depth < $COLOR_DEPTH; $depth++) {
                $data .= $hash[$index + $depth];
            }
            array_push($chunks, $utils->bit2dec($data));
        }
        return $chunks;
    }

    function generateSVGData($CHUNKS, $HEIGHT, $WIDTH, $SIZE, $COLOR_DEPTH) {
        $data = '<svg xmlns="http://www.w3.org/2000/svg" enable-background="new 0 0 '.$WIDTH*$SIZE.'  '.$HEIGHT*$SIZE.'" viewBox="0 0 '.$WIDTH*$SIZE.' '.$WIDTH*$SIZE.'" height="'.$WIDTH*$SIZE.'px" width="'.$WIDTH*$SIZE.'px">';
        for ($yValue = 0; $yValue < $HEIGHT; $yValue++) {
            for ($xValue = 0; $xValue < $WIDTH; $xValue++) {
                $index = $yValue*$WIDTH*3 + $xValue*3;
                $data .= '<rect x="'.$SIZE*$xValue.'" y="'.$SIZE*$yValue.'" height="'.$SIZE.'" width="'.$SIZE.'" style="fill: rgb('.($CHUNKS[$index+0]/(pow(2, $COLOR_DEPTH)-1)*255).', '.($CHUNKS[$index+1]/(pow(2, $COLOR_DEPTH)-1)*255).', '.($CHUNKS[$index+2]/(pow(2, $COLOR_DEPTH)-1)*255).')"/>';
            }
        }
        $data .= '</svg>';
        return $data;
    }

    function generateDB($FILENAME) {
        $file = fopen($this->PATH_STORE_DB.$FILENAME.".db", "w");
        fwrite($file, "");
        fclose($file);
    }

    function isLogedIn() {
        if ($this->NAME != NULL and $this->ID != NULL and $this->EMAIL != NULL and $this->AUTH != NULL) {
            return TRUE;
        } else {
            return FALSE;
        }
    }
}
?>