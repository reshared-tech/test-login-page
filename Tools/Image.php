<?php

namespace Tools;

use Exception;

/**
 * Image processing utility class
 * Handles image uploads, resizing, format conversion, and path management
 */
class Image
{
    /**
     * Process uploaded image: resize, convert to JPEG, and save to target directory
     *
     * @param string $name Target filename for the processed image
     * @param string $path Temporary path of the uploaded image file
     * @return bool True on successful processing, false if file not found
     * @throws Exception If image type is unsupported
     */
    public function formatUpload($name, $path)
    {
        // Return false if the temporary uploaded file does not exist
        if (!file_exists($path)) {
            return false;
        }

        // Get image metadata (width, height, MIME type)
        $info = getimagesize($path);

        // Create image resource from uploaded file based on MIME type
        switch ($info['mime']) {
            case 'image/jpeg':
                $sourceImage = imagecreatefromjpeg($path);
                break;
            case 'image/png':
                $sourceImage = imagecreatefrompng($path);
                break;
            case 'image/gif':
                $sourceImage = imagecreatefromgif($path);
                break;
            case 'image/webp':
                $sourceImage = imagecreatefromwebp($path);
                break;
            default:
                // Throw exception for unsupported image types
                throw new Exception('invalid type: ' . $info['mime']);
        }

        // Calculate target dimensions (resize to max width, maintain aspect ratio)
        $maxWidth = Config::upload['max_width'] ?? 1024;
        $width = min($info[0], $maxWidth); // Use original width if smaller than max width
        $height = (int)($info[1] * ($width / $info[0])); // Calculate proportional height

        // Create a new true-color image canvas with target dimensions
        $image = imagecreatetruecolor($width, $height);

        // For PNG images: fill canvas with white background (prevents transparent areas from turning black)
        if ($info['mime'] === 'image/png') {
            imagefill($image, 0, 0, imagecolorallocate($image, 255, 255, 255));
        }

        // Resample and copy the original image to the new canvas (maintains quality)
        imagecopyresampled($image, $sourceImage, 0, 0, 0, 0, $width, $height, $info[0], $info[1]);

        // Clean up: destroy original image resource to free memory
        imagedestroy($sourceImage);

        // Save the processed image as JPEG to the target upload path
        imagejpeg($image, $this->uploadPath($name));

        // Clean up: destroy new image resource to free memory
        imagedestroy($image);

        // Delete the temporary uploaded file (no longer needed after processing)
        unlink($path);

        return true;
    }

    /**
     * Move an uploaded file to the target upload directory (without processing)
     *
     * @param string $name Target filename for the uploaded file
     * @param string $path Temporary path of the uploaded file
     * @return bool True on successful move, false if file not found or move fails
     */
    public function upload($name, $path)
    {
        // Return false if the temporary uploaded file does not exist
        if (!file_exists($path)) {
            return false;
        }

        // Move temporary file to the target upload path (uses PHP's built-in upload handler)
        return move_uploaded_file($path, $this->uploadPath($name));
    }

    /**
     * Generate a unique filename for uploaded images (avoids overwrites)
     *
     * @param string $name Original filename (to extract file extension)
     * @return string Unique filename (microtime-based ID + original extension)
     */
    public function uniqFilename($name)
    {
        // Split original filename into parts to extract extension
        $ext = explode('.', $name);

        // Generate unique ID (based on microtime for uniqueness) + original file extension
        return uniqid(microtime(true)) . '.' . end($ext);
    }

    /**
     * Get the full server path to the target upload file
     * Creates upload directory if it does not exist
     *
     * @param string $name Filename of the uploaded file
     * @return string Full server path to the upload file
     */
    public function uploadPath($name)
    {
        // Build full target directory path (APP_ROOT + base upload path)
        $target = APP_ROOT . '/' . $this->basePath();

        // Create upload directory (and parent directories) if it does not exist
        if (!file_exists($target)) {
            mkdir($target, 0755, true); // 0755 = read/write/execute for owner, read/execute for others
        }

        // Return full path to the uploaded file (directory + filename)
        return $target . '/' . $name;
    }

    /**
     * Get the public URL path to the uploaded image (for frontend display)
     *
     * @param string $name Filename of the uploaded image
     * @return string Public URL path (base upload path + filename)
     */
    public function fileSrc($name)
    {
        return $this->basePath() . '/' . $name;
    }

    /**
     * Get the base upload path from configuration (trimmed of leading/trailing slashes)
     *
     * @return string Base upload path (e.g., "assets/uploads")
     */
    private function basePath()
    {
        // Get upload path from Config, default to "assets/uploads" if not set
        $path = Config::upload['path'] ?? 'assets/uploads';

        // Remove leading/trailing slashes to ensure consistent path formatting
        return trim($path, '/');
    }
}