<?php

class Logger{

    private static function getDate($format = null){

        if($format === null){

            return date("Y-m-d h:i:s") . " - ";

        } else {

            return date($format) . " - ";

        }

    }

    public static function writeLog($message, $fileName = false){

        if(is_array($message) || is_object($message)){
            $message = json_encode($message);
        }
        if(!$fileName) $fileName = "logs/log.txt";
        return file_put_contents($fileName, self::getDate() . $message . "\n", FILE_APPEND);

    }

    public static function writeExceptionLog($message, $fileName = false){

        if(is_object($message) && ($message instanceof Exception)) {
            if (!$fileName) $fileName = "logs/log.txt";
            return file_put_contents($fileName, self::getDate() . $message->getMessage() . "\n", FILE_APPEND) && file_put_contents($fileName, $message->getTraceAsString() . "\n", FILE_APPEND);
        }
    }

    public static function clearLog($fileName = false){

        if(!$fileName) $fileName = "logs/log.txt";
        return unlink($fileName);

    }

    public static function writeSpecialLog($message, $data, $fileName = false){

        if(is_array($data) || is_object($data)){
            $message = $message . json_encode($data);
        } else {
            $message = $message . $data;
        }
        if(!$fileName) $fileName = "logs/special_log.txt";
        return file_put_contents($fileName, self::getDate() . $message . "\n", FILE_APPEND);

    }

}