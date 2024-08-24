<?php
include 'converter.php';

function processAndDisplayImages($directory) {
    if (!is_dir($directory)) {
        echo "Katalog '$directory' nie istnieje.<br>";
        return;
    }

    // Upewnij się, że katalog 'webp' istnieje
    $webpDirectory = $directory . DIRECTORY_SEPARATOR . 'webp';
    if (!is_dir($webpDirectory)) {
        if (mkdir($webpDirectory, 0755, true)) {
            echo "Utworzono katalog '$webpDirectory'.<br>";
        } else {
            echo "Nie udało się utworzyć katalogu '$webpDirectory'.<br>";
            return;
        }
    } else {
        echo "Katalog '$webpDirectory' już istnieje.<br>";
    }

    // Otwórz katalog i przetwórz pliki
    $files = array_diff(scandir($directory), array('.', '..')); // Usunięcie '.' i '..'
    
    foreach ($files as $file) {
        $filePath = $directory . DIRECTORY_SEPARATOR . $file;
        
        if (is_file($filePath) && in_array(strtolower(pathinfo($filePath, PATHINFO_EXTENSION)), ['jpeg', 'jpg', 'png', 'gif', 'webp'])) {
            echo "Przetwarzanie pliku: $filePath<br>";
            $destination = ConvertionToWebp($filePath);
            
            if ($destination) {
                echo "Plik WebP został utworzony: $destination<br>";
                
                // Sprawdź, czy plik WebP istnieje
                if (file_exists($destination)) {
                    echo '<img src="' . $destination . '" alt="' . htmlspecialchars($file) . '" height="300px" width="auto"><br>';
                } else {
                    echo 'Nie udało się znaleźć pliku WebP: ' . htmlspecialchars($destination) . '<br>';
                }
            } else {
                echo 'Nie udało się skonwertować pliku: ' . htmlspecialchars($filePath) . '<br>';
            }
        } else {
            echo "Nieprawidłowy plik lub format: $filePath<br>";
        }
    }
}

processAndDisplayImages('images');
?>
