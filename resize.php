<?php
// resize.php
//
// Accept requests like:
//
//   picdir/resize.php?name=i6ac90__myfile.jpg&w=400
//
// And then redirect to a static file, rendering it if necessary:
//
//   picdir/resized/w600__i6ac90_myfile.jpg

include('lib.php');

// Default header (for errors)
header('content-type: text/html; charset=utf-8', true, 400);

// TODO:
// 1. Check if the file exists in the data dir
// 2. Check if the resized version already exists in the resized dir
//    If not, create it.
// 3. 301 Permanent Redirect to the resized version
//    https://www.seoclarity.net/resources/knowledgebase/use-301-redirect-vs-302-redirect-15683/
//
// The web server has to be configured to serve it.

// name= params from upload.php will already be sanitized, but we must sanitize
// it again.
$name = $_GET['name'];
if (!isset($name)) {
  exit("Expected name= param\n");
}
$name = sanitize($name);

// Serve through PHP so you can copy the URL
$serve_slowly = $_GET['serve-slowly-through-php'];

$max_width = $_GET['max-width'];
$rotation = $_GET['rotation'];
if (isset($rotation)) {
  $rotation = floatval($rotation);
} else {
  $rotation = 0;
}

error_log("resize.php $name");

$orig_path = "$UPLOAD_DIR/$name";

if (! isset($max_width)) {
  // relative path is the URL
  header('Location: ' . $orig_path);
  exit();
}

$ext = strtolower(pathinfo($orig_path, PATHINFO_EXTENSION));
switch ($ext) {
case "jpg":
case "jpeg":
  $image = imagecreatefromjpeg($orig_path);
  break;
case "png":
  $image = imagecreatefrompng($orig_path);
  break;
default:
  exit("Invalid filename $orig_path");
}

if ($image === false) {
  exit('Invalid image');
}

# iOS bug on resizing!
#
# https://stackoverflow.com/questions/66661973/iphone-os-image-photo-resizing-tool-changing-exif-orientation-data
#
# To work around this, we have a &rotation=90 param, and use that as part of
# the cache key.

if ($rotation != 0) {
  $image = imagerotate($image, $rotation, 0);
} else {
  $exif = exif_read_data($orig_path);
  $orientation = $exif['Orientation'];
  switch ($orientation) {
    case 3:
      $image = imagerotate($image, 180, 0);
      break;
    case 6:
      $image = imagerotate($image, -90, 0);
      break;
    case 8:
      $image = imagerotate($image, 90, 0);
      break;
  }
}

$orig_width = imagesx($image);
$orig_height = imagesy($image);

// No resizing necessary
if ($rotation == 0 && $orig_width <= $max_width) {
  // relative path is the URL
  header('Location: ' . $orig_path);
  exit();
}

$resized_path = "$RESIZED_DIR/w{$max_width}__r{$rotation}__$name";

if (!file_exists($resized_path)) {
  $scale = $max_width / $orig_width;

  $new_width = ceil($scale * $orig_width);
  $new_height = ceil($scale * $orig_height);

  // Create new empty image
  $resized = imagecreatetruecolor($new_width, $new_height);

  // Resample old into new

  imagecopyresampled(
    $resized,
    $image,
    0, 0,  // dest x, y
    0, 0,  // src x, y
    $new_width, $new_height,
    $orig_width, $orig_height
  );

  error_log("$orig_width x $orig_height -> $new_width x $new_height");
  error_log("resized path = $resized_path");

  // need a temp path in case two requests are resizing the same file
  $tmp_path = tempnam(".", "resized");
  $f = fopen($tmp_path, "w");
  // error_log("f = $f");
  imagejpeg($resized, $f, 90);
  fclose($f);

  chmod($tmp_path, 0644);  // make the file servable
  rename($tmp_path, $resized_path);
} else {
  error_log("resize.php using $resized_path");
}

if (isset($serve_slowly)) {
  $size = filesize($resized_path);
  header("content-length: $size");

  $image_type = exif_imagetype($resized_path);
  $mime_type = image_type_to_mime_type($image_type);
  header("content-type: $mime_type");

  $f = fopen($resized_path, 'rb');

  // Just do the whole thing at once, since the image is supposed to be small
  // now!
  echo fread($f, $size);

  fclose($f);

  exit();
}

header('Location: ' . $resized_path);

error_log("resize.php Done");
?>
