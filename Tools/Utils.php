<?php

namespace Tools;

class Utils
{
    public static function getRequestAgent()
    {
        return $_SERVER['HTTP_USER_AGENT'] ?? 'Unknown';
    }

    public static function getRequestIp()
    {
        $ipSources = [
            'HTTP_CLIENT_IP',
            'HTTP_X_FORWARDED_FOR',
            'HTTP_X_FORWARDED',
            'HTTP_X_CLUSTER_CLIENT_IP',
            'HTTP_FORWARDED_FOR',
            'HTTP_FORWARDED',
            'REMOTE_ADDR'
        ];

        foreach ($ipSources as $key) {
            if (!empty($_SERVER[$key])) {
                $ipList = explode(',', $_SERVER[$key]);
                foreach ($ipList as $ip) {
                    $ip = trim($ip);

                    // verify IPv4 and IPv6
                    if (filter_var($ip, FILTER_VALIDATE_IP)) {
                        if ($ip === '127.0.0.1' || $ip === '::1') {
                            return $ip;
                        }

                        if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE)) {
                            return $ip;
                        }
                    }
                }
            }
        }

        return 'Unknown';
    }
}