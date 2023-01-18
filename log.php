<?php

class Log {

    public $fileLog;

    function __construct($path)
    {
        $this->fileLog = fopen($path, "a");
    }
    
    // function close(){
    //     fclose($this->fileLog);
    // }

    function writeLine($type,$message,$id){
        $date = new DateTime();
        fputs($this->fileLog, "[".$type."][".$id."][".$date->format("d-m-Y H:i:s")."]: ". $message . "\n");
        fclose($this->fileLog);
    }


}

?>