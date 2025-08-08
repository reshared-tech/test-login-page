<?php

namespace Tools;
class Config
{
    const database = [
        'host' => '',
        'port' => '',
        'dbname' => '',
        'username' => '',
        'password' => '',
    ];

    const timezone = 'Asia/Shanghai';

    const domain = 'http://localhost/test-login-page';

    const upload = [
        'path' => 'assets/uploads',
        'max_width' => 1024,
        'max_size' => 8 * 1024 * 1024,
    ];
}