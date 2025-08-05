<?php

namespace Tools;

/**
 * AWS S3 Interaction Class
 *
 * Provides basic operations for Amazon S3 service including
 * uploading, downloading, deleting objects, and listing objects in a bucket.
 * Implements AWS Signature Version 4 for request authentication.
 */
class AwsS3
{
    /**
     * @var string AWS access key ID
     */
    private $accessKey;

    /**
     * @var string AWS secret access key
     */
    private $secretKey;

    /**
     * @var string AWS region (e.g., us-east-1)
     */
    private $region;

    /**
     * @var string AWS service name (fixed as 's3' for this class)
     */
    private $service = 's3';

    /**
     * @var string S3 endpoint template
     */
    private $endpoint = 's3.%s.amazonaws.com';

    /**
     * Constructor
     */
    public function __construct()
    {
        $config = Config::awsS3;
        $this->accessKey = $config['access_key'];
        $this->secretKey = $config['secret_key'];
        $this->region = $config['region'];
    }

    /**
     * Upload an object to S3 bucket
     *
     * @param string $bucket Bucket name
     * @param string $key Object key (path/filename in bucket)
     * @param string $content Object content
     * @param string $contentType MIME type of content (default: application/octet-stream)
     * @return bool True on success, false on failure
     */
    public function put($bucket, $key, $content, $contentType = 'application/octet-stream')
    {
        $host = $this->getHost($bucket);
        $uri = $this->normalizeKey($key);
        $headers = [
            'Host' => $host,
            'Content-Type' => $contentType,
            'x-amz-content-sha256' => hash('sha256', $content)
        ];

        $signedHeaders = $this->signRequest('PUT', $uri, $headers, $content);
        [$status] = $this->executeCurlRequest(
            "https://{$host}{$uri}",
            'PUT',
            $this->buildHeaders($headers, $signedHeaders),
            $content
        );

        return $status === 200;
    }

    /**
     * Download an object from S3 bucket
     *
     * @param string $bucket Bucket name
     * @param string $key Object key (path/filename in bucket)
     * @return string|false Object content on success, false on failure
     */
    public function get($bucket, $key)
    {
        $host = $this->getHost($bucket);
        $uri = $this->normalizeKey($key);
        $headers = ['Host' => $host];

        $signedHeaders = $this->signRequest('GET', $uri, $headers);
        [$status, $response] = $this->executeCurlRequest(
            "https://{$host}{$uri}",
            'GET',
            $this->buildHeaders($headers, $signedHeaders)
        );

        return $status === 200 ? $response : false;
    }

    /**
     * Delete an object from S3 bucket
     *
     * @param string $bucket Bucket name
     * @param string $key Object key (path/filename in bucket)
     * @return bool True on success, false on failure
     */
    public function delete($bucket, $key)
    {
        $host = $this->getHost($bucket);
        $uri = $this->normalizeKey($key);
        $headers = ['Host' => $host];

        $signedHeaders = $this->signRequest('DELETE', $uri, $headers);
        [$status] = $this->executeCurlRequest(
            "https://{$host}{$uri}",
            'DELETE',
            $this->buildHeaders($headers, $signedHeaders)
        );

        return $status === 204;
    }

    /**
     * List objects in S3 bucket with optional prefix
     *
     * @param string $bucket Bucket name
     * @param string $prefix Optional prefix to filter objects
     * @param int $maxKeys Maximum number of keys to return (default: 1000)
     * @return array|false Array of objects on success, false on failure
     */
    public function list($bucket, $prefix = '', $maxKeys = 1000)
    {
        $host = $this->getHost($bucket);
        $uri = '/';

        $query = [
            'list-type' => 2,
            'max-keys' => $maxKeys
        ];

        if ($prefix) {
            $query['prefix'] = $prefix;
        }

        $uri .= '?' . http_build_query($query);
        $headers = ['Host' => $host];

        $signedHeaders = $this->signRequest('GET', $uri, $headers);
        [$status, $response] = $this->executeCurlRequest(
            "https://{$host}{$uri}",
            'GET',
            $this->buildHeaders($headers, $signedHeaders)
        );

        if ($status !== 200) {
            return false;
        }

        // Parse XML response
        $xml = simplexml_load_string($response);
        if (!$xml) {
            return false;
        }

        $result = [];
        foreach ($xml->Contents as $content) {
            $result[] = [
                'Key' => (string)$content->Key,
                'LastModified' => (string)$content->LastModified,
                'Size' => (int)$content->Size
            ];
        }

        return $result;
    }

    /**
     * Sign an AWS S3 request using Signature Version 4
     *
     * @param string $method HTTP method (GET, PUT, DELETE, etc.)
     * @param string $uri Request URI
     * @param array $headers Associative array of headers
     * @param string $payload Request payload (default: empty string)
     * @return array Signed headers including Authorization
     */
    private function signRequest($method, $uri, $headers, $payload = '')
    {
        $amzDate = gmdate('Ymd\THis\Z');
        $dateStamp = gmdate('Ymd');

        // Add required AWS headers
        $headers['x-amz-date'] = $amzDate;
        $headers['x-amz-content-sha256'] = $headers['x-amz-content-sha256'] ?? hash('sha256', $payload);

        // Prepare canonical headers and signed headers
        $canonicalHeaders = '';
        $signedHeaders = '';

        ksort($headers); // Sort headers alphabetically
        foreach ($headers as $key => $value) {
            $canonicalHeaders .= strtolower($key) . ':' . trim($value) . "\n";
            $signedHeaders .= strtolower($key) . ';';
        }
        $signedHeaders = rtrim($signedHeaders, ';');

        // Create canonical request
        $canonicalRequest = implode("\n", [
            $method,
            $uri,
            '', // No query string in canonical request for S3
            $canonicalHeaders,
            $signedHeaders,
            $headers['x-amz-content-sha256']
        ]);

        // Create string to sign
        $credentialScope = implode('/', [$dateStamp, $this->region, $this->service, 'aws4_request']);
        $stringToSign = implode("\n", [
            'AWS4-HMAC-SHA256',
            $amzDate,
            $credentialScope,
            hash('sha256', $canonicalRequest)
        ]);

        // Calculate signature
        $signingKey = $this->getSigningKey($dateStamp);
        $signature = hash_hmac('sha256', $stringToSign, $signingKey);

        // Return signed headers
        return [
            'Authorization' => 'AWS4-HMAC-SHA256 ' . implode(', ', [
                    'Credential=' . $this->accessKey . '/' . $credentialScope,
                    'SignedHeaders=' . $signedHeaders,
                    'Signature=' . $signature
                ]),
            'x-amz-date' => $amzDate,
            'x-amz-content-sha256' => $headers['x-amz-content-sha256']
        ];
    }

    /**
     * Generate AWS signing key for Signature Version 4
     *
     * @param string $dateStamp Date in Ymd format
     * @return string Binary signing key
     */
    private function getSigningKey(string $dateStamp): string
    {
        $kSecret = 'AWS4' . $this->secretKey;
        $kDate = hash_hmac('sha256', $dateStamp, $kSecret, true);
        $kRegion = hash_hmac('sha256', $this->region, $kDate, true);
        $kService = hash_hmac('sha256', $this->service, $kRegion, true);
        return hash_hmac('sha256', 'aws4_request', $kService, true);
    }

    /**
     * Build headers array for cURL
     *
     * @param array $headers Original headers
     * @param array $signedHeaders Signed headers including Authorization
     * @return array Formatted headers for cURL
     */
    private function buildHeaders(array $headers, array $signedHeaders): array
    {
        $result = [];

        // Add original headers
        foreach ($headers as $key => $value) {
            $result[] = "{$key}: {$value}";
        }

        // Add authorization header
        $result[] = "Authorization: {$signedHeaders['Authorization']}";

        return $result;
    }

    /**
     * Get S3 host for a bucket
     *
     * @param string $bucket Bucket name
     * @return string Full S3 host name
     */
    private function getHost(string $bucket): string
    {
        return sprintf('%s.%s', $bucket, sprintf($this->endpoint, $this->region));
    }

    /**
     * Normalize object key to ensure proper URI formatting
     *
     * @param string $key Object key
     * @return string Normalized URI path
     */
    private function normalizeKey(string $key): string
    {
        // Remove leading slash if present and encode special characters
        return '/' . ltrim(rawurlencode(ltrim($key, '/')), '/');
    }

    /**
     * Execute cURL request with error handling
     *
     * @param string $url Request URL
     * @param string $method HTTP method
     * @param array $headers Request headers
     * @param string $postFields Request body (optional)
     * @return array [HTTP status code, response body]
     */
    private function executeCurlRequest(string $url, string $method, array $headers, string $postFields = ''): array
    {
        $ch = curl_init($url);

        // Set common cURL options
        $options = [
            CURLOPT_CUSTOMREQUEST => $method,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER => $headers,
            CURLOPT_HEADER => false,
            CURLOPT_SSL_VERIFYPEER => true, // Enable SSL verification for security
            CURLOPT_CONNECTTIMEOUT => 10,
            CURLOPT_TIMEOUT => 30
        ];

        // Add post fields if provided
        if ($postFields !== '') {
            $options[CURLOPT_POSTFIELDS] = $postFields;
        }

        curl_setopt_array($ch, $options);

        // Execute request and get response
        $response = curl_exec($ch);
        $status = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        // Check for cURL errors
        if (curl_errno($ch)) {
            error_log("cURL error: " . curl_error($ch));
            $response = false;
        }

        curl_close($ch);

        return [$status, $response];
    }
}
