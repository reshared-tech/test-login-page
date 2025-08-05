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

    const awsS3 = [
        'access_key' => '',
        'secret_key' => '',
        'region' => 'us-east-1',
    ];

    const timezone = 'Asia/Shanghai';

    const domain = 'http://localhost/test-login-page';
}