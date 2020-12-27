<?php
// resize.php
//
// Accept requests like:
//
//   resize.php?name=2020-12-26-${RANDOM}__${ORIGINAL_NAME}.jpg&w=600
//
// And then redirect to:
//   imagebin/data/w600__2020-12-26-${RANDOM}__${ORIGINAL_NAME}.jpg
//
// I think we only care about the height for now.

include('config.php');

// Default header (for errors)
header('content-type: text/html; charset=utf-8', true, 400);

// TODO:
// 1. Check if the file exists in the data dir
// 2. Check if the resized version already exists in the cache dir
//    If not, create it.
// 3. 301 Permanent Redirect to the cached version
//    https://www.seoclarity.net/resources/knowledgebase/use-301-redirect-vs-302-redirect-15683/
//
// The web server has to be configured to serve it.

// name= params from upload.php will already be sanitized, but we must sanitize
// it again.
$name = sanitize($_GET['name']);
$max_width = $_GET['max-width'];

error_log("name = " . $name . "\n");

if (! isset($name)) {
  exit("Expected name= param\n");
}

$orig_path = "$upload_dir/$name";

if (! isset($max_width)) {
  // relative path is the URL
  header('Location: ' . $orig_path);
  exit();
}

// TODO: Accept .gif, .png or .jpg|.jpeg extensions
$image = imagecreatefromjpeg($orig_path);
if ($image === false) {
  exit('Invalid image');
}

$orig_width = imagesx($image);
$orig_height = imagesy($image);

if ($orig_width <= $max_width) {
  // relative path is the URL
  header('Location: ' . $orig_path);
  exit();
}

$scale = $max_width / $orig_width;

$new_width = ceil($scale * $orig_width);
$new_height = ceil($scale * $orig_height);

// Create new empty image
$new = imagecreatetruecolor($new_width, $new_height);

// Resample old into new

// TODO:
// - Respect EXIF orientation data.
// - Cache this computation.

imagecopyresampled(
  $new,
  $image,
  0, 0,  // dest x, y
  0, 0,  // src x, y
  $new_width, $new_height,
  $orig_width, $orig_height
);

error_log("$orig_width x $orig_height -> $new_width x $new_height");

// Catch the image data
ob_start();

// TODO: Use imagepng() and imagegif()
imagejpeg($new, null, 90);
$data = ob_get_clean();

// Destroy resources
imagedestroy($image);
imagedestroy($new);

// Output image data
header("Content-type: image/jpeg", true, 200);
echo $data;

?>

