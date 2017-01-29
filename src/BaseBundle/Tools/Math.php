<?php

namespace BaseBundle\Tools;

class Math
{
    public static function rand($size = 16)
    {
        $string = '';
        while (strlen($string) < $size) {
            $string .= base_convert(sha1(microtime(true).uniqid(mt_rand(), true)), 16, 36);
        }

        return substr($string, 0, $size);
    }
}
