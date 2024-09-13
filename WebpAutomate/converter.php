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

function ConvertImageToWebp($source, $quality = 75, $width = 0, $height = 0) {
    $webpDirectory = dirname($source) . DIRECTORY_SEPARATOR . 'webp';

    if (!is_dir($webpDirectory)) {
        if (!mkdir($webpDirectory, 0755, true)) {
            logError("Nie udało się utworzyć katalogu '$webpDirectory'.");
            return false;
        }
    }

    // Adjusted destination path to include width and height
    $destination = $webpDirectory . DIRECTORY_SEPARATOR 
        . pathinfo($source, PATHINFO_FILENAME) 
        . "_q{$quality}_w{$width}_h{$height}.webp";

    if (shouldConvert($destination)) {
        if (Convert($source, $destination, $quality, $width, $height)) {
            return $destination; 
        } else {
            logError("Nie udało się skonwertować pliku do '$destination'.");
            return false;
        } 
    } else {
        echo "pominięto konwertowanie";
        return $destination;
    }
}

function ConvertFolderToWebp($directory, $quality = 75, $width = 0, $height = 0) {
    if (!is_dir($directory)) {
        logError("Nie udało się znaleźć '$destination'.");
        return false;
    }

    $webpDirectory = dirname($directory) . DIRECTORY_SEPARATOR . 'webp';

    $files = array_diff(scandir($directory), array('.', '..'));

    foreach ($files as $file) {
        $filePath = $directory . DIRECTORY_SEPARATOR . $file;
        
        if (is_file($filePath) && in_array(strtolower(pathinfo($filePath, PATHINFO_EXTENSION)), ['jpeg', 'jpg', 'png', 'gif', 'webp'])) {
            $destination = ConvertImageToWebp($filePath, 80, 0, 0);
            
            if (!is_file($destination)) {
                logError("Nie udało się znaleźć przekonwertowanego pliku: '$destination'.");
                return false;
            }
        }
    }

    return $webpDirectory;
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
            logError("Nieobsługiwany format pliku: '$source'.");
            return false;
    }

    if (!$image) {
        logError("Nie udało się załadować obrazu z '$source'.");
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
