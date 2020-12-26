<?php

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

error_log('tmp_name = ' . $tmp_name);

// Delete original file
@unlink($tmp_name);

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
imagejpeg($new, null, 90);
$data = ob_get_clean();

// Destroy resources
imagedestroy($image);
imagedestroy($new);

// Output image data
header("Content-type: image/jpeg", true, 200);
echo $data;
