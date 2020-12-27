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
$maxwidth = $_GET['maxwidth'];

error_log("name = " . $name . "\n");

if (! isset($name)) {
  exit("Expected name= param\n");
}

if (! isset($maxwidth)) {
  $to_url = $upload_dir . '/' . $name;
	header('Location: ' . $to_url);
	exit();
}

header('Location: ' . sanitize($name));
exit();

echo '<pre>';


echo "name = " . $name . "\n";
echo "width = " . $width . "\n";
echo "height  = " . $height . "\n";

echo '</pre>';


// Delete original file
// @unlink($tmp_name);

// Target dimensions
$max_width = 240;
$max_height = 180;

// Calculate new dimensions
$old_width      = imagesx($image);
$old_height     = imagesy($image);
$scale          = min($max_width/$old_width, $max_height/$old_height);
$new_width      = ceil($scale*$old_width);
$new_height     = ceil($scale*$old_height);

// Create new empty image
$new = imagecreatetruecolor($new_width, $new_height);

// Resample old into new
imagecopyresampled(
  $new,
  $image,
  0,
  0,
  0,
  0,
  $new_width,
  $new_height,
  $old_width,
  $old_height
);

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



