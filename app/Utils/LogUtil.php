<?php

namespace App\Utils;

use Log;

class LogUtil
{
    public static function warn($msg, $echo = true)
    {
        if($echo){ echo self::addEOL($msg); }
        Log::warning($msg);
    }
    public static function info($msg, $echo = true)
    {
        if($echo){ echo self::addEOL($msg); }
        Log::info($msg);
    }

    public static function error($msg, $echo = true)
    {
        if($echo){ echo self::addEOL($msg); }
        Log::error($msg);
    }

    private static function addEOL($msg): String {
        if(is_string($msg) && substr($msg, -1) !== "\n"){
            $msg .= PHP_EOL;
        }
        return $msg;
    }
}
