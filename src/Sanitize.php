<?php
namespace Koshkil\Tools;

class Sanitize {
    public const MASK_PASSWORDS = 0x01;
    public const MASK_CC        = 0x02;
    public const MASK_FULL_CC   = 0x04;
    public const MASK_EMAIL     = 0x08;

    private static $options=0;
    private static function isIterable($data) {
        return (is_array($data) || is_object($data));
    }

    private static function iterateData($data,Closure $callbackFunction) {
        if (!self::isIterable($data)) return $data;
        foreach($data as $key => $value) {
            if (self::isIterable($value)) {
                $value=self::iterateData($value,$callbackFunction);
            } else if (is_callable($callbackFunction)) {
                $value=call_user_func($callbackFunction,$key,$value);
            }
            if (is_array($data))
                $data[$key]=$value;
            else {
                $data->{$key}=$value;
            }
        }
        return $data;
    }
    public static function mask($data,$options=0) {
        if ($options)
            self::$options=$options;
        else
            self::$options=Sanitize::MASK_CC | Sanitize::MASK_PASSWORDS;

        return self::iterateData($data,function($key,$value) {
            if ((self::$options & self::MASK_PASSWORDS) == self::MASK_PASSWORDS) {
                if (preg_match("/(.*)pass(.*)/si",$key)) {
                    $value="**********";
                }
            }
            if ((self::$options & self::MASK_EMAIL) == self::MASK_EMAIL) {
                if (preg_match("/(.*)email(.*)/si",$key) && preg_match("/^\w+@[a-zA-Z_]+?\.[a-zA-Z]{2,3}$/si",$value)) {
                    $value="**********";
                }
            }
            if (((self::$options & self::MASK_CC) == self::MASK_CC) || ((self::$options & self::MASK_FULL_CC) == self::MASK_FULL_CC)) {
                if (preg_match("/(.*)credit(.*)/si",$key) || preg_match("/(.*)card(.*)/si",$key)) {
                    $value=str_replace("-","",$value);
                    $value="xxxx-xxxx-xxxx-".(((self::$options & self::MASK_FULL_CC)==self::MASK_FULL_CC)?'xxxx':substr($value,-4));
                }
            }
            return $value;
        });
    }
}
