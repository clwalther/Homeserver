<?php
class Server
{
    private $EXTENSIONS;

    function __construct() {
        $this->EXTENSIONS = $this->getExtensions();
    }

    function getExtensions() {
        $command = "cd ../Frontend/extensions && ls";
        $folders = shell_exec($command);
        $extensions = explode("\n", $folders);
        array_pop($extensions);
        return $extensions;
    }

    function getExtensionName($extension) {
        $capital = strtoupper(substr($extension, 0, 1));
        $body    = substr($extension, 1, strlen($extension));
        return $capital.$body;
    }

    function visulizeExtensions() {
        $string = "";
        foreach($this->EXTENSIONS as $extension) {
            $extensionName = $this->getExtensionName($extension);
            $string .= "<a href='/extensions/".$extension."'>".$extensionName."</a>";
        }
        echo $string;
    }
}
?>