<?php

namespace Tools;

/**
 * Utility class for common request-related operations
 * Provides methods to retrieve client information (user agent, IP address)
 */
class Utils
{
    /**
     * Get the client's user agent string (browser/device information)
     *
     * @return string Client user agent or 'Unknown' if not available
     */
    public static function getRequestAgent()
    {
        // Retrieve user agent from server superglobal; default to 'Unknown' if missing
        return $_SERVER['HTTP_USER_AGENT'] ?? 'Unknown';
    }

    /**
     * Get the client's real IP address (handles proxies and load balancers)
     * Checks multiple server headers to find the most reliable public IP
     *
     * @return string Client IP address or 'Unknown' if no valid IP found
     */
    public static function getRequestIp()
    {
        // List of server headers that may contain the client's IP (ordered by reliability)
        // Prioritizes headers that pass through proxies/load balancers
        $ipSources = [
            'HTTP_CLIENT_IP',
            'HTTP_X_FORWARDED_FOR',
            'HTTP_X_FORWARDED',
            'HTTP_X_CLUSTER_CLIENT_IP',
            'HTTP_FORWARDED_FOR',
            'HTTP_FORWARDED',
            'REMOTE_ADDR'
        ];

        // Iterate through each IP source header to find a valid IP
        foreach ($ipSources as $key) {
            // Skip if the header is empty
            if (!empty($_SERVER[$key])) {
                // Split header value into an array (handles comma-separated IPs from proxies)
                $ipList = explode(',', $_SERVER[$key]);

                // Check each IP in the list (proxies may pass multiple IPs)
                foreach ($ipList as $ip) {
                    // Remove extra whitespace from the IP address
                    $ip = trim($ip);

                    // Validate if the IP is a valid IPv4 or IPv6 address
                    if (filter_var($ip, FILTER_VALIDATE_IP)) {
                        // Return loopback IPs (localhost) immediately if detected
                        if ($ip === '127.0.0.1' || $ip === '::1') {
                            return $ip;
                        }

                        // Validate non-private, non-reserved IPs (public IPs)
                        // FILTER_FLAG_NO_PRIV_RANGE: Exclude private IP ranges (e.g., 192.168.x.x)
                        // FILTER_FLAG_NO_RES_RANGE: Exclude reserved IP ranges (e.g., 0.0.0.0/8)
                        if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE)) {
                            return $ip;
                        }
                    }
                }
            }
        }

        // Return 'Unknown' if no valid IP address is found
        return 'Unknown';
    }
}