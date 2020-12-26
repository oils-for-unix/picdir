<?php
// upload.php
//
// Upload an image and save it like 2020-12-26-${RANDOM}__$[sanitize(ORIG)].jpg
//
// TODO:
// - Support ?response=json with {"serving_at": "/picdir/2020-20-abc-foo.jpg"}
//   e.g. for JS clients.

include('config.php');

// Default header (for errors)
header('content-type: text/html; charset=utf-8', true, 400);

// Check if file was uploaded
if (! isset($_FILES['image'])) {
  exit('Expected image=');
}

$tmp_name = $_FILES['image']['tmp_name'];
if (! is_uploaded_file($tmp_name)) {
  exit('Expected image= to be a file');
}

$error = $_FILES['image']['error'] ;
if ($error !== UPLOAD_ERR_OK) {
  exit('Upload failed with error ' . $error);
}

$filename = $_FILES['image']['name'];
$file_type = strtolower($_FILES['image']['type']);

// Create image from file
switch ($file_type) {
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
  exit('Unsupported file type '. $file_type);
}

$dest = $upload_dir . '/' . unique_id() . '__' . sanitize($filename);

error_log('upload_dir = ' . $upload_dir);
error_log('cache_dir = ' . $cache_dir);
error_log('tmp_name = ' . $tmp_name);
error_log('filename = ' . $filename);
error_log('dest = ' . $dest);
error_log('sanitized = ' . sanitize($filename));

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
