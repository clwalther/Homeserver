<?php
class Client
{
    public $NAME;
    public $ID;
    public $EMAIL;
    public $AUTH;
    public $TYPE;

    private $PATH;
    private $PATH_STORE_IMG;
    private $PATH_STORE_IDB;

    function __construct($NAME, $ID, $EMAIL, $AUTH, $TYPE) {
        # VALS
        $this->NAME  = $NAME;
        $this->ID    = $ID;
        $this->EMAIL = $EMAIL;
        $this->AUTH  = $AUTH;
        $this->TYPE  = $TYPE;

        # CONSTS
        $this->PATH = $GLOBALS["PATH"];
        $this->PATH_STORE_IMG = $this->PATH."Frontend/assets/database/user-img/";
        $this->PATH_STORE_DB  = $this->PATH."Frontend/assets/database/share";
    }

    # SIGN IN
    function signIn($EMAIL, $PASSWORD) {
        $database = $GLOBALS["database"];
        
        $TABLE   = "USERS";
        $COLUMNS = ["*"];
        $LIKE    = [
            "EMAIL"    => $EMAIL,
            "PASSWORD" => $PASSWORD
        ];

        $queryAns = $database->select($TABLE, $COLUMNS, $LIKE);
        $feedback = $this->signInQueryAnswerHanler($queryAns);
        return $feedback;
    }
    function signInQueryAnswerHanler($queryAns) {
        $error = $GLOBALS["error"];
        $utils = $GLOBALS["utils"];
        
        if ($queryAns->num_rows == 1) {
            // AUTH SUCCESS
            while ($answer = $queryAns->fetch_assoc()) {
                $this->getDataFromQuery($answer);
                $utils->changeLocation("/", "_self");
                return 0;
            }
        } else {
            // INVALID ANSWER
            $error->clientEmailPasswordIncorrect();
            return 1;
        }
    }

    # SIGN UP
    function signUp($USERNAME, $EMAIL, $PASSWORD, $REPEATEPASSWORD) {
        $database = $GLOBALS["database"];
        $error    = $GLOBALS["error"];
        $utils    = $GLOBALS["utils"];

        if ($PASSWORD == $REPEATEPASSWORD) {  
            // PASSWORD IS GIVEN TWICE
            $TABLE   = "SIGN_UP";
            $COLUMNS = ["*"];
            $LIKE    = [
                "EMAIL" => $EMAIL
            ];

            $queryAns = $database->select($TABLE, $COLUMNS, $LIKE);
            $feedback = $this->signUpQueryAnswerHandler($queryAns);

            $CODE  = $this->generateAuthCode();
            $AUTH  = 0;
            $ARRAY = [
                "CODE"     => $CODE,
                "USERNAME" => $USERNAME,
                "EMAIL"    => $EMAIL,
                "PASSWORD" => $PASSWORD,
                "AUTH"     => $AUTH
            ];

            $database->insert("SIGN_UP", $ARRAY);
            $utils->setCookie("EMAIL", $EMAIL);
            $utils->changeLocation("./auth", "_self");
            return 0;
        } else {
            // PASSWORD AND REPEATED PASSWORD ARE NOT THE SAME
            $error->clientPasswordIsNotRepeatedPassword();
            return 1;
        }
    }
    function signUpQueryAnswerHanler($queryAns) {
        $database = $GLOBALS["database"];

        if ($queryAns->num_rows == 1) {
            // EMAIL ALREADY TRYING TO LOGIN
            $database->delete("SIGN_UP", $LIKE);
        }
    }
    function generateCode() { # utils
        $database = $GLOBALS["database"];

        $code = NULL;

        $TABLE   = "SIGN_UP";
        $COLUMNS = ["*"];
        $LIKE    = [
            "CODE" => $code
        ];

        while ($code == NULL or $database->select("SIGN_UP", ["*"], $LIKE)->num_rows > 0) {
            $code = rand(100000, 999999);
            $LIKE = [
                "CODE" => $code
            ];
        }
        return $code;
    }

    # SIGN UP EMAIL AUTH
    function signUpEmailAuth($CODE) {
        $database = $GLOBALS["database"];
        $error    = $GLOBALS["error"];
        $utils    = $GLOBALS["utils"];

        $TABLE   = "SIGN_UP";
        $COLUMNS = ["*"];
        $LIKE    = [
            "CODE" => $CODE
        ];

        $queryAns = $database->select($TABLE, $COLUMNS, $LIKE);
        
        if ($queryAns->num_rows == 1) {
            // EAMIL AUTHORIZED
            while ($answer = $queryAns->fetch_assoc()) {
                $TABLE   = "USERS";
                $COLUMNS = ["*"];
                $ARRAY    = [
                    "USERNAME" => $answer["USERNAME"],
                    "EMAIL"    => $answer["EMAIL"],
                    "TYPE"     => "USER",
                    "PASSWORD" => $answer["PASSWORD"],
                    "AUTH"     => intval($answer["AUTH"])
                ];

                $database->insert($TABLE, $ARRAY);
                $database->delete("SIGN_UP", $LIKE);
                $queryAns = $database->select($TABLE, $COLUMNS, $ARRAY);

                while ($answer = $queryAns->fetch_assoc()) {
                    $this->getDataFromQuery($answer);
                    $this->generateImg();
                    $this->generateDB();
                    $utils->changeLocation("/", "_self");
                    return 0;
                }
            }
        } else {
            $error->clientEmailAuthIncorrect();
            return 1;
        }
    }

    # SIGN OUT
    function signOut() {
        $utils = $GLOBALS["utils"];

        $utils->removeCookie("AUTH");
        $utils->removeCookie("EMAIL");
        $utils->removeCookie("USERID");
        $utils->removeCookie("USERNAME");
    }

    # RECOVER
    function recover($EMAIL) {
        $database = $GLOBALS["database"];
        $error    = $GLOBALS["error"];
        $utils    = $GLOBALS["utils"];

        

    }

    function sendEmail($TYPE, $RECEIVER, $CONTEXT) {
        $command = "/bin/python ".$this->PATH."App/Auth.py ".$TYPE." ".$RECEIVER." ".$CONTEXT;
        shell_exec($command);
    }
    
    # DATA INIT
    function getDataFromQuery($answer) {
        $utils = $GLOBALS["utils"];   
        // GLOBALS
        $this->NAME  = $answer["USERNAME"];
        $this->ID    = $answer["USERID"];
        $this->EMAIL = $answer["EMAIL"];
        $this->AUTH  = $answer["AUTH"];
        $this->TYPE  = $answer["TYPE"];
        // COOKIES
        $utils->setCookie("USERNAME", $this->NAME);
        $utils->setCookie("USERID",   $this->ID);
        $utils->setCookie("EMAIL",    $this->EMAIL);
        $utils->setCookie("AUTH",     $this->AUTH);
        $utils->setCookie("TYPE",     $this->TYPE);
    }


    function generateImg($FILENAME, $CONTEXT) {
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

    # IS PROPERTY...
    function isLogedIn() {
        if ($this->NAME != NULL and $this->ID != NULL and $this->EMAIL != NULL and $this->AUTH != NULL) {
            return TRUE;
        } else {
            return FALSE;
        }
    }
    function isAdmin() {
        if ($this->isLogedIn() and $this->TYPE == "ADMIN") {
            return TRUE;
        } else {
            return FALSE;
        }
    }
}
?>