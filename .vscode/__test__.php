<?php
    class UnitTests
    {
        private $fatal  = 0; # the test could not be fulle run [2]*
        private $failed = 0; # the test received an unexpected result [1]
        private $passed = 0; # the test passed as excpected [0]
        private $invalidReturn = 0; #  if the returened value is not one of the above*
        private $total  = 0; # all tests

        private $errors = []; # logs all error messages if fatal or failed

        # *) this error code should not actually be returned from the testing function.

        function __construct() {}

        function run($CLASS) {
            $METHODS = $this->getMethods($CLASS);
            $this->executeMethods($CLASS, $METHODS);
            $this->report($CLASS);
        }

        function getMethods($CLASS) {
            $methods = [];
            foreach (get_class_methods($CLASS) as $methodName) {
                if ($methodName != get_class($CLASS)) {
                    if (substr($methodName, 0, 4) === "test") {
                        array_push($methods, $methodName);
                    }
                }
            }
            return $methods;
        }

        function executeMethods($CLASS, $METHODS) {
            foreach ($METHODS as $method) {
                $ERRORMSG = NULL;
                try {
                    $result = $CLASS->{$method}();
                } catch (Exception $errorMsg) {
                    $ERRORMSG = $errorMsg;
                    $result   = 2;
                }
                $this->returnMethodEval($result, $method, $ERRORMSG);
            }
        }

        function returnMethodEval($result, $method, $errorMsg) {
            if ($result == 0) {
                $this->passed += 1;
            } elseif ($result == 1) {
                $this->failed += 1;
                $this->errors[$method] = $errorMsg;
            } elseif ($result == 2) {
                $this->fatal += 1;
                $this->errors[$method] = $errorMsg;
            } else {
                $this->invalidReturn += 1;
            }
            $this->total += 1;
        }

        function report($CLASS) {
            $REPORT  = ">>> UNIT TEST REPORT FOR ".get_class($CLASS)."<<<\n\n";

            $REPORT .= "    METHODS FATAL:   ".$this->fatal.",\n";
            $REPORT .= "    METHODS FAILED:  ".$this->failed.",\n";
            $REPORT .= "    METHODS PASSED:  ".$this->passed.",\n";
            $REPORT .= "    METHODS INVALID: ".$this->invalidReturn.",\n\n";

            $REPORT .= "    METHODS TOTAL:   ".$this->total."\n\n";

            $REPORT .= ">>> ERROR MESSAGE(S) <<<\n\n";
            $REPORT .= $this->generateErrorMsgs($CLASS)."\n";

            print("\n\n");
            print($REPORT);
        }

        function generateErrorMsgs($CLASS) {
            $ERRORMSG = "";
            foreach ($this->errors as $method => $error) {
                $ERRORMSG .= "METHOD: ".get_class($CLASS).".".$method."()\nERROR:  ".$error."\n\n";
            }
            return $ERRORMSG;
        }
    }



?>