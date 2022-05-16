<?php
class Client
{
    public $NAME;
    public $ID;
    public $EMAIL;
    public $AUTH;

    private $TYPE;
    private $PATH_STORE_IMG = "/home/pi/Public/Homeserver/Frontend/assets/DATABASE/USERIMG/";

    function __construct($NAME, $ID, $EMAIL, $AUTH, $TYPE) {
        $this->$NAME  = $NAME;
        $this->$ID    = $ID;
        $this->$EMAIL = $EMAIL;
        $this->AUTH   = $AUTH;
        $this->TYPE   = $TYPE;
    }

    function signIn($EMAIL, $PASSWORD) {
        $error = $GLOBALS["error"];
        $database = $GLOBALS["database"];
        // AUTH
        $TABLE   = "USERS";
        $COLUMNS = "*";
        $LIKE    = [
            "EMAIL"    => $EMAIL,
            "PASSWORD" => $PASSWORD
        ];

        $result = $database->select($TABLE, $COLUMNS, $LIKE);

        if ($result->num_rows == 1) {
            // AUTH SUCCCESS
            while ($answer = $result->fetch_assoc()) {
                $this->getDataFromQuery($answer);
                // REDIRECT
            }
        } else {
            $error->clientEmailPasswordIncorrect();
        }
    }

    function signUp($USERNAME, $EMAIL, $PASSWORD, $REPEATEPASSWORD) {
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
                $code = $this->generateCode();
                // INSERT
                $ARRAY = [
                    "CODE"     => $code,
                    "USERNAME" => $USERNAME,
                    "EMAIL"    => $EMAIL,
                    "PASSWORD" => $PASSWORD,
                    "AUTH"     => 0
                ];
                $database->insert("TEMP", $ARRAY);
                // REDIRECT
            } else {
                // EMAIL ALREADY USED
                $error->clientEmailAlreadyUsed();
            }
        } else {
            // PASSWORD AND REPEATED PASSWORD ARE NOT EQUAL
            $error->clientPasswordIsNotRepeatedPassword();
        }
    }
    
    function authEmailToAccount($CODE) {
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
                    // REDIRECT
                }
                
            }
        } else {
            $error->clientEmailAuthIncorrect();
        }
    }
    
    function sendEmail($TYPE, $RECEIVER, $CONTEXT) {
        $command = "/bin/python /home/pi/Public/Homeserver/App/Auth.py ".$TYPE." ".$RECEIVER." ".$CONTEXT;
        shell_exec($command);
    }
    
    function getDataFromQuery($answer) {
        $utils = $GLOBALS["utils"];
        // GET
        $this->NAME  = $answer["USERNAME"];
        $this->ID    = $answer["USERID"];
        $this->EMAIL = $answer["EMAIL"];
        $this->AUTH  = $answer["AUTH"];
        // COOKIES
        $utils->setCookie("USERNAME", $this->NAME);
        $utils->setCookie("USERID", $this->ID);
        $utils->setCookie("EMAIL", $this->EMAIL);
        $utils->setCookie("AUTH", $this->AUTH);
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
}
?>