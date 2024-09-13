# WebPAutomate - Image conversion Library

This PHP library provides functions for converting images to WebP format and managing WebP files. It supports both Windows and Linux environments.

## Installation

To use this library, follow these steps:

1. Download the repo.
2. Include it in your PHP project with the following code:

    ```php
    include 'WebpAutomate/converter.php';
    ```

## Functions

### `GetWebP($FilePathOrDir, $Qality = 75, $WidthProperty = 0, $HeightProperty = 0)`

Converts images in a specified directory to WebP format and generates HTML image tags for these images. If a single file path is provided, it converts that image to WebP and generates a tag for it.

#### Parameters:
- **`$FilePathOrDir`** (`string`): Path to the file or directory containing images to convert.
- **`$Quality`** (`int`, optional): Quality of the WebP image, ranging from 0 to 100. Default is 75.
- **`$WidthProperty`** (`int`, optional): Width to scale the image to. Default is 0 (no scaling).
- **`$HeightProperty`** (`int`, optional): Height to scale the image to. Default is 0 (no scaling).

#### Example Usage:
```php
GetWebP('path/to/images', 80, 800, 600);
```

### `ConvertImageToWebp($source, $quality = 75, $width = 0, $height = 0)`

Converts a single image file to WebP format. Supports various image formats and creates the corresponding WebP file in the `webp` directory.

#### Parameters:
- **`$source`** (`string`): Path to the source image file.
- **`$quality`** (`int`, optional): Quality of the WebP image, ranging from 0 to 100. Default is 75.
- **`$width`** (`int`, optional): Width to scale the image to. Default is 0 (no scaling).
- **`$height`** (`int`, optional): Height to scale the image to. Default is 0 (no scaling).

#### Returns:
- **`string|false`**: Path to the WebP file if the conversion is successful, `false` otherwise.

#### Example Usage:
```php
ConvertImageToWebp('path/to/image.jpg', 90, 1024, 768);
```

### `ConvertFolderToWebp($directory, $quality = 75, $width = 0, $height = 0)`

Converts all images in a specified directory to WebP format. Creates a `webp` directory if it does not exist and processes all supported image files.

#### Parameters:
- **`$directory`** (`string`): Path to the directory containing images.
- **`$quality`** (`int`, optional): Quality of the WebP images, ranging from 0 to 100. Default is 75.
- **`$width`** (`int`, optional): Width to scale the images to. Default is 0 (no scaling).
- **`$height`** (`int`, optional): Height to scale the images to. Default is 0 (no scaling).

#### Returns:
- **`string|false`**: Path to the `webp` directory if the conversion is successful, `false` otherwise.

#### Example Usage:
```php
ConvertFolderToWebp('path/to/images', 85, 800, 600);
```

## Error Logging

The library includes an `logError($message)` function for logging errors. It writes error messages to a `converter_error.log` file located in the same directory as `converter.php`.

## Notes

- Ensure proper error handling to manage any issues that arise during image conversion or directory creation.
