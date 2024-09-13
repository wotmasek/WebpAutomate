<?php 
    function logError($message) {
        $source = __FILE__;
        $logFile = dirname($source) . DIRECTORY_SEPARATOR . 'converter_error.log'; 

        if (!file_exists($logFile)) {
            touch($logFile);
        }

        $timestamp = date('Y-m-d H:i:s'); 
        file_put_contents($logFile, "[$timestamp] $message\n", FILE_APPEND);
    }

    function DirElsePath($path) {
        if (is_dir($path)) {
            return true;
        } elseif (is_file($path)) {
            return false;
        }
    }

    function GenerateImageTag($filePath, $directory) {
        $fileInfo = pathinfo($filePath);
        $fileName = $fileInfo['filename']; 
    
        return '<img src="' . $directory . '" alt="' . htmlspecialchars($fileName) . '">';
    }
    
    function shouldConvert($destination) {
        if (file_exists($destination)) {
            return false; 
        }
        return true;
    }
    
    function Convert($source, $destination, $quality, $width, $height) {
        $extension = strtolower(pathinfo($source, PATHINFO_EXTENSION));
        $baseName = pathinfo($source, PATHINFO_FILENAME);
        $webpDirectory = dirname($destination);
    
        removeOldWebpFiles($webpDirectory, $baseName);
    
        switch ($extension) {
            case 'jpeg':
            case 'jpg':
                $image = imagecreatefromjpeg($source);
                break;
            case 'png':
                $image = imagecreatefrompng($source);
                break;
            case 'gif':
                $image = imagecreatefromgif($source);
                break;
            case 'webp':
                if (copy($source, $destination)) {
                    return true;
                } else {
                    return false;
                }
            default:
                logError("Unsupported file format: '$source'.");
                return false;
        }
    
        if (!$image) {
            logError("Failed to load image from '$source'.");
            return false;
        }
    
        if (function_exists('rescale')) {
            $origWidth = imagesx($image);
            $origHeight = imagesy($image);
    
            $image = rescale($image, $width, $height, $origWidth, $origHeight, $extension);
        }
    
        $result = imagewebp($image, $destination, $quality);
        
        imagedestroy($image);
    
        return $result ? true : false;
    }
    
    function removeOldWebpFiles($directory, $baseName) {
        $pattern = $directory . DIRECTORY_SEPARATOR . $baseName . '_q*.webp';
        $files = glob($pattern);
    
        foreach ($files as $file) {
            if (file_exists($file)) {
                unlink($file);
            }
        }
    }
    
    function rescale($image, $width, $height, $origWidth, $origHeight, $extension) {
        if ($width == 0 && $height == 0) {
            return $image;
        }
    
        if ($width == 0) {
            $width = ($height / $origHeight) * $origWidth;
        } elseif ($height == 0) {
            $height = ($width / $origWidth) * $origHeight;
        }
    
        $scaledImage = imagecreatetruecolor($width, $height);
    
        if ($extension === 'png') {
            imagealphablending($scaledImage, false);
            imagesavealpha($scaledImage, true);
            $transparent = imagecolorallocatealpha($scaledImage, 255, 255, 255, 127);
            imagefill($scaledImage, 0, 0, $transparent);
        }
    
        imagecopyresampled($scaledImage, $image, 0, 0, 0, 0, $width, $height, $origWidth, $origHeight);
    
        return $scaledImage;
    }
?>
