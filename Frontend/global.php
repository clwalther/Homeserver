<?php
    $PATH      = "/home/pi/Public/Home/Backend/DATABASE/SHARE/";
    $ERROR_LOG = "/home/pi/Public/Home/Backend/error.log";

    $SERVERNAME   = 'localhost';
    $USERLOGIN    = 'YSbNNzN8p#ndxMe?';
    $USERPASSW    = 'GBB8@K6G&ka!xnrX4&msDD?CAjtxGo';
    $DATABASENAME = 'HOMESERVERDB';
    $TABLENAME0  = 'USERS';
    $TABLENAME1  = 'TEMP';

    $CONNECTION = new mysqli($SERVERNAME, $USERLOGIN, $USERPASSW, $DATABASENAME);
    if($CONNECTION->connect_error) {
        die($CONNECTION->connect_error); // TODO: ERROR_HANDLING send feedback to the server with the error-msg
    }

    function ERROR_FEEDBACK($feedback, $serverity) {
        echo $feedback;


        $file = fopen($GLOBALS["ERROR_LOG"], "a");
        fwrite($file, date("Y-m-d H:s:i").", ".$serverity.", ERROR: ".$feedback."\n");
        fclose($file);
    }

    function SIGNUP($USERNAME, $EMAIL, $PASSWORD, $RPASSWORD) {
        // PASSWORD CHECK
        if($PASSWORD == $RPASSWORD) {
            // DUBLICATE EMAIL CHECK TEMP
            $query  = "SELECT * FROM ".$GLOBALS["TABLENAME1"]." WHERE EMAIL='".$EMAIL."';";
            $return = $GLOBALS["CONNECTION"]->query($query);
            
            if($return->num_rows > 0) {
                $query  = "DELETE FROM ".$GLOBALS["TABLENAME1"]." WHERE EMAIL='".$EMAIL."';";
                $return = $GLOBALS["CONNECTION"]->query($query);
            }

            // DUBLICATE EMAIL CHECK USERS
            $query  = "SELECT * FROM ".$GLOBALS["TABLENAME0"]." WHERE EMAIL='".$EMAIL."';";
            $return = $GLOBALS["CONNECTION"]->query($query);

            if($return->num_rows == 0) {
                // GENERATE AUTHENTCATION CODE
                $CODE = NULL;
                while($GLOBALS["CONNECTION"]->query("SELECT * FROM ".$GLOBALS["TABLENAME1"]." WHERE CODE=".$CODE.";")->num_rows > 0 or $CODE == NULL) {
                    $CODE = rand(100000, 999999);
                }
                // WRITE TO TABLE TEMP
                $query = "INSERT INTO ".$GLOBALS["TABLENAME1"]." VALUES (".$CODE.", '".$USERNAME."', '".$EMAIL."', '".$PASSWORD."', 0);";
                $return = $GLOBALS["CONNECTION"]->query($query);

                // RETURN
                $GLOBALS["CONNECTION"]->close();
                echo "<script>
                        client.setCookie('EMAIL',    '".$EMAIL."')
                        client.setCookie('USERNAME', '".$USERNAME."')
                        client.setCookie('PASSWORD', '".$PASSWORD."')
                        window.open('/signup/auth', '_self');
                    </script>";
            } else {
                // CLIENT ERROR
                // TODO: ERROR_HANDLING email already in use
            }
        } else {
            // CLIENT ERROR
            // TODO: ERROR_HANDLING passwords do not match
        }
    }

    function SEND_EMAIL($TYPE, $RECEIVER, $CONTEXT) {
        if($CONTEXT > 99999) {
            shell_exec("/bin/python /home/pi/Public/Home/App/Auth.py ".$TYPE." ".$RECEIVER." ".$CONTEXT."");
        }
    }
    
    function GENERATE_IMG($USERID, $USERNAME) {
        $WIDTH  = 4;
        $HEIGHT = 4;
        
        $SIZE = 10;
        $COLOR_DEPTH_B = 4;
        
        $result = hash("sha256", $USERNAME);
        $result = bin2bit(hex2bin($result));
        
        $CHUNKS = array();
        for($i = 0; $i < (strlen($result) / $COLOR_DEPTH_B); $i++) {
            $DATA = "";
            for($k = 0; $k < $COLOR_DEPTH_B; $k++) {
                $DATA .= $result[$i+$k];
            }
            array_push($CHUNKS, bit2dec($DATA));
        }
        $DATA  = '<svg xmlns="http://www.w3.org/2000/svg" enable-background="new 0 0 '.$WIDTH*$SIZE.'  '.$HEIGHT*$SIZE.'" height="'.$WIDTH*$SIZE.'px" viewBox="0 0 '.$WIDTH*$SIZE.' '.$WIDTH*$SIZE.'" width="'.$WIDTH*$SIZE.'px">';
        for($y = 0; $y < $HEIGHT; $y++) {
            for($x = 0; $x < $WIDTH; $x++) {
                $index = $y*$WIDTH*3 + $x*3;
                $DATA .= '<rect x="'.$SIZE*$x.'" y="'.$SIZE*$y.'" height="'.$SIZE.'" width="'.$SIZE.'" style="fill: rgb('.($CHUNKS[$index+0]/(pow(2, $COLOR_DEPTH_B)-1)*255).', '.($CHUNKS[$index+1]/(pow(2, $COLOR_DEPTH_B)-1)*255).', '.($CHUNKS[$index+2]/(pow(2, $COLOR_DEPTH_B)-1)*255).')"/>';
            }
        }
        $DATA .= '</svg>';
        
        $file = fopen('/home/pi/Public/Home/Frontend/assets/DATABASE/USERIMG/'.$USERID.'.svg', "w");
        fwrite($file, $DATA);
        fclose($file);
    }
    function bin2bit($input) {
        $bitseq = "";
        $end    = strlen($input);
        for($i = 0; $i < $end; $i++) {
            $byte = decbin(ord($input[$i]));
            $bitseq .= substr("00000000",0,8 - strlen($byte)) . $byte;
        }
        return $bitseq;
    }
    function bit2dec($input) {
        $return = 0;
        $input  = strrev($input);

        for($i = 0; $i < strlen($input); $i++) {
            if($input[$i] == "1") {
                $return += pow(2, $i);
            }
        }
        return $return;
    }

    function AUTH_EMAIL($CODE) {
        // READ FROM TABLE TEMP
        $query  = "SELECT * FROM ".$GLOBALS["TABLENAME1"]." WHERE CODE=".$CODE." AND EMAIL='".$_COOKIE["EMAIL"]."';";
        $return = $GLOBALS["CONNECTION"]->query($query);
        
        if($return->num_rows == 1) {
            // SUCCESS
            while($row = $return->fetch_assoc()) {
                // WRITE TO TABLE USERS
                $query  = 'INSERT INTO '.$GLOBALS["TABLENAME0"].' (USERNAME, EMAIL, TYPE, PASSWORD, AUTH) VALUES ("'.$row["USERNAME"].'", "'.$row["EMAIL"].'", "USER", "'.$row["PASSWORD"].'", '.$row["AUTH"].');';
                $return = $GLOBALS["CONNECTION"]->query($query);
                // DELETE FROM TABLE TEMP
                $query  = 'DELETE FROM '.$GLOBALS["TABLENAME1"].' WHERE CODE="'.$CODE.'" AND USERNAME="'.$row["USERNAME"].'" AND EMAIL="'.$row["EMAIL"].'";';
                $return = $GLOBALS["CONNECTION"]->query($query);
                // GET USERID
                $query  = 'SELECT * FROM '.$GLOBALS["TABLENAME0"].' WHERE EMAIL="'.$_COOKIE["EMAIL"].'";';
                $return = $GLOBALS["CONNECTION"]->query($query);
                while($row = $return->fetch_assoc()) {
                    $USERID = $row["USERID"];
                }
                // CREATE FILE *USERID*.db
                $file = fopen($GLOBALS["PATH"].$USERID.".db", "w") or die(
                    ERROR_FEEDBACK("Cound not create file ".$GLOBALS["PATH"].$USERID.".db".$GLOBALS["CONNECTION"]->query("DELETE FROM ".$GLOBALS["TABLENAME0"]." WHERE USERID='".$USERID."'")." -> RESULT: ".$GLOBALS["CONNECTION"]->error, 1)
                );
                fclose($file);
                // CREATE USERIMG
                GENERATE_IMG($USERID, $_COOKIE["USERNAME"]);

                // RETURN and LOGIN
                $GLOBALS["CONNECTION"]->close();
                echo "<script>
                        client.setCookie('USERID', '".$USERID."')
                        window.open('/', '_self')
                    </script>";
            }
        } else {
            // CLIENT ERROR
            // TODO: ERROR_HANDLIN code is incorrect
        }
    }
    
    function LOGIN($EMAIL, $PASSWORD) {
        // AUTH
        $query  = "SELECT * FROM ".$GLOBALS["TABLENAME0"]." WHERE EMAIL='".$EMAIL."' AND PASSWORD='".$PASSWORD."'";
        $result = $GLOBALS["CONNECTION"]->query($query);
        
        if($result->num_rows == 1) {
            // AUTH SUCCESS
            while($row = $result->fetch_assoc()) {
                // GET [USERID, USERNAME]
                $USERID   = $row["USERID"];
                $USERNAME = $row["USERNAME"];

                // RETURN
                $GLOBALS["CONNECTION"]->close();
                echo "<script>
                    client.setCookie('EMAIL',    '".$EMAIL."')
                    client.setCookie('USERID',   '".$USERID."')
                    client.setCookie('USERNAME', '".$USERNAME."')
                    client.setCookie('PASSWORD', '".$PASSWORD."')
                    window.open('/', '_self');</script>";
            }
        } else {
            // CLIENT ERROR
            // TODO: ERROR_HANDLING email or password incorrect
        }
    }
?>