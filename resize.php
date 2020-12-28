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

error_log("resize.php $name");

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

$cache_path = "$cache_dir/w{$max_width}__$name";

if (!file_exists($cache_path)) {
  $scale = $max_width / $orig_width;

  $new_width = ceil($scale * $orig_width);
  $new_height = ceil($scale * $orig_height);

  // Create new empty image
  $resized = imagecreatetruecolor($new_width, $new_height);

  // Resample old into new

  // TODO:
  // - Respect EXIF orientation data.

  imagecopyresampled(
    $resized,
    $image,
    0, 0,  // dest x, y
    0, 0,  // src x, y
    $new_width, $new_height,
    $orig_width, $orig_height
  );

  error_log("$orig_width x $orig_height -> $new_width x $new_height");
  error_log("cache path = $cache_path");

  // need a temp path in case two requests are resizing the same file
  $tmp_path = tempnam(".", "resized");
  $f = fopen($tmp_path, "w");
  // error_log("f = $f");
  imagejpeg($resized, $f, 90);
  fclose($f);

  rename($tmp_path, $cache_path);
} else {
  error_log("resize.php using $cache_path");
}

header("Content-type: image/jpeg", true, 200);
readfile($cache_path);  // write the whole file out

error_log("resize.php Done");
?>
