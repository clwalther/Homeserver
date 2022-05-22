<?php
class Env
{
    private $ENV_PATH;

    function __construct() {
        $this->ENV_PATH = $GLOBALS["PATH"]."env";
    }

    function getEnv($key) {
        $read = file($this->ENV_PATH);

        foreach ($read as $line) {
            $list = explode(":", $line);
            $readKey   = trim($list[0]);
            $readValue = trim($list[1]);
            if ($readKey == $key) {
                return $readValue;
            }
        }
        return NULL;
    }
}
?>