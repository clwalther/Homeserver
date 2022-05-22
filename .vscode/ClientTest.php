<?php
    include "./__test__.php";
    include "../Frontend/global.php";

    class ClientTest
    {
        private $CODE     = 123456;
        private $USERNAME = "John Smith";
        private $EMAIL    = "john@smith.uk";
        private $PASSWORD = "password12345";

        // utils
        function createDefaultUser() {
            $database = $GLOBALS["database"];
            $VALUES = [
                "USERNAME" => $this->USERNAME,
                "EMAIL"    => $this->EMAIL,
                "TYPE"     => "USER",
                "PASSWORD" => $this->PASSWORD,
                "AUTH"     => 0
            ];
            $database->insert("USERS", $VALUES);
        }
        function removeDefaultUser() {
            $database = $GLOBALS["database"];
            $VALUES = [ "EMAIL"    => $this->EMAIL ];
            $database->delete("USERS", $VALUES);
        }
        function createTempUser() {
            $database = $GLOBALS["database"];
            $VALUES = [
                "CODE"     => $this->CODE,
                "USERNAME" => $this->USERNAME,
                "EMAIL"    => $this->EMAIL,
                "PASSWORD" => $this->PASSWORD,
                "AUTH"     => 0
            ];
            $database->insert("TEMP", $VALUES);
        }
        function removeTempUser() {
            $database = $GLOBALS["database"];
            $LIKE = [ "EMAIL" => $this->EMAIL ];
            $database->delete("TEMP", $LIKE);
        }
        function removeGeneratedSVG($NAME) {
            $command = "rm ../Frontend/assets/DATABASE/USERIMG/".$NAME.".svg";
            shell_exec($command);
        }

        // singIn()
        function testSucessfulSignIn() {
            // get
            $client = new Client(NULL, NULL, NULL, NULL, NULL);
            $this->createDefaultUser();
            // when
            $errorCode = $client->signIn($this->EMAIL, $this->PASSWORD);
            // then
            $this->removeDefaultUser();
            if ($errorCode == 0) {
                return 0;
            } else {
                return 1;
            }
        }
        function testFailSignIn() {
            // get
            $client = new Client(NULL, NULL, NULL, NULL, NULL);
            $this->createDefaultUser();
            // when
            $errorCode = $client->signIn($this->USERNAME, "incorrect password");
            // then
            $this->removeDefaultUser();
            if ($errorCode == 1) {
                return 0;
            } else {
                return 1;
            }
        }
        
        // signUp()
        function testSuccessfulSignUp() {
            // get
            $client = new Client(NULL, NULL, NULL, NULL, NULL);
            $database = $GLOBALS["database"];
            // when
            $errorCode = $client->signUp($this->USERNAME, $this->EMAIL, $this->PASSWORD, $this->PASSWORD);
            $LIKE   = [ "EMAIL" => $this->EMAIL ];
            $result = $database->select("TEMP", ["*"], $LIKE);
            // then
            $this->removeTempUser();
            if ($result->num_rows == 1 && $errorCode == 0) {
                return 0;
            } else {
                return 1;
            }
        }
        function testEmailAlreadyUsedSignUp() {
            // get
            $client = new Client(NULL, NULL, NULL, NULL, NULL);
            $this->createDefaultUser();
            // when
            $errorCode = $client->signUp($this->USERNAME, $this->EMAIL, $this->PASSWORD, $this->PASSWORD);
            // then
            $this->removeDefaultUser();
            if ($errorCode == 2) {
                return 0;
            } else {
                return 1;
            }
        }
        function testNotDublicatePasswordSignUp() {
            // get
            $client = new Client(NULL, NULL, NULL, NULL, NULL);
            // when
            $errorCode = $client->signUp($this->USERNAME, $this->EMAIL, $this->PASSWORD, "");
            // then
            if ($errorCode == 1) {
                return 0;
            } else {
                return 1;
            }
        }

        // authEmailToAccount()
        function testAuthEmailToAccount() {
            // get
            $client = new Client(NULL, Null, NULL, NULL, NULL);
            $database = $GLOBALS["database"];
            $this->createTempUser();
            // when
            $errorCode = $client->authEmailToAccount($this->CODE);
            $LIKE = [ "CODE" => $this->CODE ];
            $result = $database->select("TEMP", ["*"], $LIKE);
            // then
            $this->removeDefaultUser();
            if ($result->num_rows == 0 && $errorCode == 0) {
                $this->removeGeneratedSVG($client->ID);
                return 0;
            } else {
                return 1;
            }
        }
        function testIncorrectAuthEmailToAccount() {
            // get
            $client = new Client(NULL, Null, NULL, NULL, NULL);
            $this->createTempUser();
            // when
            $errorCode = $client->authEmailToAccount(234567);
            // then
            $this->removeTempUser();
            if ($errorCode == 1) {
                return 0;
            } else {
                return 1;
            }
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
            $FILENAME = "Hello_World";
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
                    $this->removeGeneratedSVG($FILENAME);
                    return TRUE;
                }
            }
            return FALSE;
        }
    }

    $clientTest = new ClientTest();
    $unitTest   = new UnitTests();
    $unitTest->run($clientTest);
?>