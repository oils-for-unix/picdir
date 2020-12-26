<?php

// NOTE: The web server has to be configured to serve out of both of these
// dirs.
$upload_dir = getenv('IMAGEBIN_UPLOAD_DIR');
$cache_dir = getenv('IMAGEBIN_CACHE_DIR');

// Default header (for errors)
header('content-type: text/html; charset=utf-8', true, 400);

// Check if file was uploaded
if (! isset($_FILES['image']) || ! is_uploaded_file($_FILES['image']['tmp_name'])) {
  exit('No file uploaded.');
}

// And if it was ok
if ($_FILES['image']['error'] !== UPLOAD_ERR_OK) {
  exit('Upload failed. Error code: '.$_FILES['image']['error']);
}

$tmp_name = $_FILES['image']['tmp_name'];
$filename = $_FILES['image']['name'];

// Create image from file
switch (strtolower($_FILES['image']['type'])) {
case 'image/jpeg':
  $image = imagecreatefromjpeg($tmp_name);
  break;
case 'image/png':
  $image = imagecreatefrompng($tmp_name);
  break;
case 'image/gif':
  $image = imagecreatefromgif($tmp_name);
  break;
default:
  exit('Unsupported type: '.$_FILES['image']['type']);
}

// TODO: protect ovewrite with a timestamp?
$dest = $filename;

error_log('upload_dir = ' . $upload_dir);
error_log('cache_dir = ' . $cache_dir);
error_log('tmp_name = ' . $tmp_name);
error_log('filename = ' . $filename);
error_log('dest = ' . $dest);

move_uploaded_file($tmp_name, $dest);

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
