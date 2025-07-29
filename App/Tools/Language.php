<?php

namespace App\Tools;

class Language
{
    static $config;

    const JP = 'japanese';

    const Languages = [
        'japanese' => [
            ''
        ],
    ];

    public static function setLang($config)
    {
        self::$config = self::Languages[$config] ?? [];
    }

    public function show($text)
    {
        return self::$config[$text] ?? $text;
    }
}