<?php
    include "./__test__.php";
    include "../Frontend/global.php";

    class ClientTest
    {
        
        // singIn()
        function testSignIn() {
            // get
            // when
            // then
            return 1;
        }

        // signUp()
        function SuccessfulSignUp() {
            // get
            $client   = new Client(NULL, NULL, NULL, NULL, NULL);
            $database = $GLOBALS["database"];
            $USERNAME = "John Smith";
            $EMAIL    = "john@smith.uk";
            $PASSWORD = "password12345";
            // when
            $client->signUp($USERNAME, $EMAIL, $PASSWORD, $PASSWORD);
            // then
            $LIKE = [ "EMAIL" => $EMAIL ];
            $result = $database->select("TEMP", ["*"], $LIKE);
            
            if ($result->num_rows == 1) {
                return 0;
            } else {
                return 1;
            }
        }

        // authEmailToAccount()
        function testAuthEmailToAccount() {
            // get
            $client   = new Client(NULL, Null, NULL, NULL, NULL);
            $database = $GLOBALS["database"];
            $result   = $database->select("TEMP", ["CODE"], NULL);
            $code = $result->fetch_assoc()["CODE"];
            // when
            $client->authEmailToAccount($code);
            // then
            $LIKE = [ "CODE" => $code ];
            $result = $database->select("TEMP", ["*"], $LIKE);
            $this->cleanUpAuthEmailToAccount();
            if ($result->num_rows == 0) {
                return 0;
            } else {
                return 1;
            }
        }
        function cleanUpAuthEmailToAccount() {
            $database = $GLOBALS["database"];
            $LIKE = [ "EMAIL" => "john@smith.uk" ];
            $database.delete("USERS", $LIKE);
        }

        // generateCode()
        function testGenerateCode() {
            // get
            $client = new Client(NULL, Null, NULL, NULL, NULL);
            // when
            $result = $client->generateCode();
            // then
            if ($result > 99999 && $result < 1000000) {
                return 0;
            } else {
                return 1;
            }
        }

        // generateImg()
        function testGenerateImg() {
            // get
            $client = new Client(NULL, Null, NULL, NULL, NULL);
            $FILENAME = "3";
            $CONTEXT  = "Hello World!";
            // when
            $client->generateImg($FILENAME, $CONTEXT);
            // then
            $result = $this->checkForSVG($FILENAME);
            if ($result) {
                return 0;
            }
            return 1;
        }
        function checkForSVG($FILENAME) {
            $command = "cd ../Frontend/assets/DATABASE/USERIMG && ls";
            $files = shell_exec($command);
            $files = explode('.svg', $files);
            foreach ($files as $fileName) {
                if (trim($fileName) == $FILENAME) {
                    return TRUE;
                }
            }
            return FALSE;
        }
    }

    $clientTest = new ClientTest();
    $unitTest = new UnitTests();
    $unitTest->run($clientTest);
?>