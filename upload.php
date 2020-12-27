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

// Safe for HTML
$new_filename = unique_id() . '__' . sanitize($filename);
$upload_path  = $upload_dir . '/' . $new_filename;

error_log('upload_dir = ' . $upload_dir);
error_log('cache_dir = ' . $cache_dir);
error_log('tmp_name = ' . $tmp_name);
error_log('filename = ' . $filename);
error_log('upload_path  = ' . $upload_path );
error_log('sanitized = ' . sanitize($filename));

move_uploaded_file($tmp_name, $upload_path );

$example = "resize.php?name=$new_filename&maxwidth=600";
$example2 = "resize.php?name=$new_filename";

header("Content-type: text/html", $replace = true, 200);

include('header.php');

echo "<h1>Uploaded</h1>";

echo "Original: <a href=\"$upload_path\">$upload_path</a> <br/>\n";

echo "Resize with a URL like this: <a href=\"$example\">$example</a> <br/>\n";

echo "Plain: <a href=\"$example2\">$example2</a> <br/>\n";
?>

<p>
  hello there
</p>

<?php include('footer.php'); ?>
