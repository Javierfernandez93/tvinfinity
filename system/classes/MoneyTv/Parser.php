<?php

namespace MoneyTv;

class Parser {
    const VALID_ARGS = ['name','from','server_name','names','whatsApp','company'];
    const DEFAULT_ARGS = [
        'name' => '',
        'server_name' => '',
        'names' => 'Usuario MoneyTv',
        'whatsApp' => '',
        'company' => 'MoneyTv'
    ];

    public static function sanitize(array $args = null) : array 
    {
        return array_filter($args,function($arg){
            return in_array($arg,self::VALID_ARGS);
        },ARRAY_FILTER_USE_KEY);
    }

    public static function existArg(string $text = null, string $arg = null) : bool
    {   
        return strpos($text, "{{{$arg}}}") !== false;
    }

    public static function doParser(string $text = null,array $args = null) : string
    {
        $args = self::sanitize($args);
        $args = array_merge(self::DEFAULT_ARGS, $args);

        foreach ($args as $key => $arg)
        {
            if(self::existArg($text,$key))
            {
                $text = str_replace("{{{$key}}}",$arg,$text);
            }
        }

        return $text;
    }
}