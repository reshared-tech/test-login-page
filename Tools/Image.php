<?php

namespace Tools;

use Exception;

class Image
{
    /**
     * @throws Exception
     */
    public function formatUpload($name, $path)
    {
        if (!file_exists($path)) {
            return false;
        }

        $info = getimagesize($path);
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
                throw new Exception('invalid type: ' . $info['mime']);
        }

        $width = min($info[0], Config::upload['max_width'] ?? 1024);
        $height = (int)($info[1] * ($width / $info[0]));
        $image = imagecreatetruecolor($width, $height);
        if ($info['mime'] === 'image/png') {
            imagefill($image, 0, 0, imagecolorallocate($image, 255, 255, 255));
        }

        imagecopyresampled($image, $sourceImage, 0, 0, 0, 0, $width, $height, $info[0], $info[1]);
        imagedestroy($sourceImage);
        imagejpeg($image, $this->uploadPath($name));
        imagedestroy($image);
        unlink($path);
        return true;
    }

    public function upload($name, $path)
    {
        if (!file_exists($path)) {
            return false;
        }

        return move_uploaded_file($path, $this->uploadPath($name));
    }

    public function uniqFilename($name)
    {
        $ext = explode('.', $name);

        return uniqid(microtime(true)) . '.' . end($ext);
    }

    public function uploadPath($name)
    {
        $target = APP_ROOT . '/' . $this->basePath();

        if (!file_exists($target)) {
            mkdir($target, 0755, true);
        }

        return $target . '/' . $name;
    }

    public function fileSrc($name)
    {
        return $this->basePath() . '/' . $name;
    }

    private function basePath()
    {
        $path = Config::upload['path'] ?? 'assets/uploads';

        return trim($path, '/');
    }
}