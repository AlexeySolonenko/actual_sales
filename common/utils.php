<?php

namespace csv\common;

class Utils
{

    static function pr($a)
    {
        return "<pre>" . print_r($a, true) . "</pre>";
    }
}
