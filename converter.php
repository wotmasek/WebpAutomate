<?php 
    include 'functions/functions.php';

    function GetWebP($FilePathOrDir, $Quality = 75, $WidthProperty = 0, $HeightProperty = 0) {
        if (DirElsePath($FilePathOrDir)) {
            $directory = ConvertFolderToWebp($FilePathOrDir, $Quality, $WidthProperty, $HeightProperty);
    
            if ($directory && is_dir($directory)) {
                // Get all .webp files in the directory
                $webpFiles = glob($directory . '/*.webp');
    
                foreach ($webpFiles as $filePath) {
                    echo GenerateImageTag($filePath, $filePath);
                }
            } else {
                logError("WebP directory not found.");
            }
        } else {
            $directory = ConvertImageToWebp($FilePathOrDir, $Quality, $WidthProperty, $HeightProperty);
    
            if ($directory) {
                echo GenerateImageTag($FilePathOrDir, $directory);
            } else {
                logError("Failed to convert file.");
            }
        }
    }

    function ConvertFolderToWebp($directory, $quality = 75, $width = 0, $height = 0) {
        if (!is_dir($directory)) {
            logError("Failed to find '$directory'.");
            return false;
        }
    
        // Create WebP directory
        $webpDirectory = $directory . '/' . 'webp';
        if (!is_dir($webpDirectory)) {
            if (!mkdir($webpDirectory, 0755, true)) {
                logError("Failed to create directory '$webpDirectory'.");
                return false;
            }
        }

        // Get files to convert
        $files = array_diff(scandir($directory), array('.', '..'));
    
        foreach ($files as $file) {
            $filePath = $directory . '/' . $file;
            
            // Check if the file has one of the extensions to convert
            if (is_file($filePath) && in_array(strtolower(pathinfo($filePath, PATHINFO_EXTENSION)), ['jpeg', 'jpg', 'png', 'gif'])) {
                $destination = ConvertImageToWebp($filePath, $quality, $width, $height);
                
                if (!$destination) {
                    logError("Failed to convert file: '$filePath'.");
                }
            }
        }
    
        return $webpDirectory; // Return WebP directory
    }

    function ConvertImageToWebp($source, $quality = 75, $width = 0, $height = 0) {
        $webpDirectory = dirname($source) . '/' . 'webp';
    
        // Create WebP directory if it does not exist
        if (!is_dir($webpDirectory)) {
            if (!mkdir($webpDirectory, 0755, true)) {
                logError("Failed to create directory '$webpDirectory'.");
                return false;
            }
        }
    
        // Destination path for the WebP file
        $destination = $webpDirectory . '/'
            . pathinfo($source, PATHINFO_FILENAME) 
            . "_q{$quality}_w{$width}_h{$height}.webp";
    
        if (shouldConvert($destination)) {
            if (Convert($source, $destination, $quality, $width, $height)) {
                return $destination; 
            } else {
                logError("Failed to convert file to '$destination'.");
                return false;
            } 
        } else {
            echo "Skipped converting '$source', file already exists.\n";
            return $destination;
        }
    }
?>
