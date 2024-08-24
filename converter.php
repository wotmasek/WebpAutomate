<?php

function logError($message) {
    $source = __FILE__;
    $logFile = dirname($source) . DIRECTORY_SEPARATOR . 'webp' . DIRECTORY_SEPARATOR . 'converter_error.log'; // Ścieżka do pliku logów
    $timestamp = date('Y-m-d H:i:s'); // Dodanie znacznika czasowego
    file_put_contents($logFile, "[$timestamp] $message\n", FILE_APPEND);
}

function ConvertionToWebp($source) {
    $quality = 75;  // Set quality (0-100, default = 75)

    $width = 0;
    $height = 0;

    $webpDirectory = dirname($source) . DIRECTORY_SEPARATOR . 'webp';

    // Sprawdzenie istnienia katalogu i jego utworzenie
    if (!is_dir($webpDirectory)) {
        if (!mkdir($webpDirectory, 0755, true)) {
            logError("Nie udało się utworzyć katalogu '$webpDirectory'.");
            return false;
        }
    }

    // Przygotowanie ścieżki do pliku docelowego
    $destination = $webpDirectory . DIRECTORY_SEPARATOR . pathinfo($source, PATHINFO_FILENAME) . '.webp';

    // Konwersja pliku
    if (Convert($source, $destination, $quality, $width, $height)) {
        return $destination; // Zwraca ścieżkę do pliku WebP
    } else {
        logError("Nie udało się skonwertować pliku do '$destination'.");
        return false;
    }
}

function Convert($source, $destination, $quality, $width, $height) {
    $extension = strtolower(pathinfo($source, PATHINFO_EXTENSION));

    // Ładowanie obrazu w zależności od formatu
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

    // Sprawdzenie, czy obraz został poprawnie załadowany
    if (!$image) {
        logError("Nie udało się załadować obrazu z '$source'.");
        return false;
    }

    // Skalowanie obrazu, jeśli funkcja jest zdefiniowana
    if (function_exists('rescale')) {
        $origWidth = imagesx($image);
        $origHeight = imagesy($image);

        $image = rescale($image, $width, $height, $origWidth, $origHeight, $extension);
    }

    // Zapisywanie obrazu w formacie WebP
    $result = imagewebp($image, $destination, $quality);
    
    imagedestroy($image);

    return $result ? true : false;
}

function rescale($image, $width, $height, $origWidth, $origHeight, $extension) {
    if ($width == 0 && $height == 0) {
        return $image;
    }

    // Jeśli tylko jeden z wymiarów jest ustawiony, oblicz drugi, zachowując proporcje
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
