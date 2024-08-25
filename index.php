<?php
include 'WebpAutomate/converter.php';

function processAndDisplayImages($directory) {
    if (!is_dir($directory)) {
        echo "Katalog '$directory' nie istnieje.<br>";
        return;
    }

    $files = array_diff(scandir($directory), array('.', '..'));
    
    foreach ($files as $file) {
        $filePath = $directory . DIRECTORY_SEPARATOR . $file;
        
        if (is_file($filePath) && in_array(strtolower(pathinfo($filePath, PATHINFO_EXTENSION)), ['jpeg', 'jpg', 'png', 'gif', 'webp'])) {
            $destination = ConvertionToWebp($filePath, 80, 0, 0);
            
            if ($destination) {
                if (file_exists($destination)) {
                    echo '<img src="' . $destination . '" alt="' . htmlspecialchars($file) . '"><br>';
                }
            }
        }
    }
}

processAndDisplayImages('images');
?>
