<?php

namespace App\Utils;

use Config;

class CdnUtil
{
    public static function url(): string{
        return Config::get('utils.CDN_URL');
    }
}
