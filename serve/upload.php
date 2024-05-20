<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Allow from any origin
if (isset($_SERVER['HTTP_ORIGIN'])) {
    header("Access-Control-Allow-Origin: {$_SERVER['HTTP_ORIGIN']}");
    header('Access-Control-Allow-Credentials: true');
    header('Access-Control-Max-Age: 86400');    // cache for 1 day
}

// Access-Control headers are received during OPTIONS requests
if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_METHOD']))
        header("Access-Control-Allow-Methods: GET, POST, OPTIONS");

    if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']))
        header("Access-Control-Allow-Headers: {$_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']}");

    exit(0);
}
include_once '../config/connect.php';
include_once '../classes/users.php';
$database = new Database();
$db = $database->getConnection();
 
$user = new Users($db);

// Define target directory and allowed extensions
$target_dir = "../upload/";  // Updated to use a server file path
$allowTypes = array('image/jpeg', 'image/png');
$put = 'img_' . time();  // Generate a unique name using time()

// Check if form submitted
if (isset($_FILES["image"])) {
    $message = "";

    // Check if a file is uploaded
    if (!isset($_FILES["image"]["name"])) {
        echo json_encode("Please select an image to upload.");
    } else {
        $image = $_FILES["image"];
        $image_type = exif_imagetype($image["tmp_name"]);

        // Check if it's actually an image
        if ($image_type !== false) {
            // Check file size (10MB)
            if ($image["size"] > 10000000) {
                echo json_encode("Sorry, your file is too large (max 10MB).");
            } else {
                // Check allowed file types
                if (in_array(image_type_to_mime_type($image_type), $allowTypes)) {
                    // Generate a unique filename
                    $extension = pathinfo($image["name"], PATHINFO_EXTENSION);
                    $new_filename = $put . "." . $extension;
                    $full_path = $target_dir . $new_filename;

                    // Resize image to 300px width (maintaining aspect ratio)
                    $image_data = getimagesize($image["tmp_name"]);
                    $width = $image_data[0];
                    $height = $image_data[1];
                    $new_width = 300;
                    $new_height = ($height / $width) * $new_width;

                    if ($image_type == IMAGETYPE_JPEG) {
                        $source = imagecreatefromjpeg($image["tmp_name"]);
                    } elseif ($image_type == IMAGETYPE_PNG) {
                        $source = imagecreatefrompng($image["tmp_name"]);
                    }

                    $thumb = imagecreatetruecolor($new_width, $new_height);

                    // Resize while maintaining transparency (if PNG)
                    if ($image_type == IMAGETYPE_PNG) {
                        imagealphablending($thumb, false);
                        imagesavealpha($thumb, true);
                        $transparent = imagecolorallocatealpha($thumb, 255, 255, 255, 127);
                        imagefilledrectangle($thumb, 0, 0, $new_width, $new_height, $transparent);
                    }

                    imagecopyresampled($thumb, $source, 0, 0, 0, 0, $new_width, $new_height, $width, $height);

                    // Move the resized image to target directory
                    if ($image_type == IMAGETYPE_JPEG) {
                        $result = imagejpeg($thumb, $full_path);
                    } elseif ($image_type == IMAGETYPE_PNG) {
                        $result = imagepng($thumb, $full_path);
                    }

                    if ($result) {
                        // set product property values
                        $user->upload($new_filename);
                        echo json_encode("Image uploaded successfully!");
                    } else {
                        echo json_encode("Sorry, there was an error uploading your file.");
                    }
                } else {
                    echo json_encode("Sorry, only JPG and PNG files are allowed.");
                }
            }
        } else {
            echo json_encode("File is not an image.");
        }
    }
}
?>
