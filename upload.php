<?php
// upload.php
//
// Upload an image and save it to
//
//   picdir/upload/${unique_id}__$[sanitize(ORIG)].jpg
//
// TODO:
// - Support ?response=json with {"serving_at": "/picdir/2020-20-abc-foo.jpg"}
//   Or the HTML can just have some "microdata"?

include('lib.php');

if ($HASHED_PASSWORD) {
  if (!password_verify($_POST['password'], $HASHED_PASSWORD)) {
    header('content-type: text/html; charset=utf-8', true, 403);
    exit('Invalid password');
  }
} else {
  error_log('Allowing upload without password');
}

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
  exit("Upload failed with error $error");
}

$filename = $_FILES['image']['name'];

// Safe for HTML
$new_filename = unique_id() . '__' . sanitize($filename);
$upload_path  = "$UPLOAD_DIR/$new_filename";

error_log("$tmp_name -> $upload_path");

move_uploaded_file($tmp_name, $upload_path );

$example = "resize?name=$new_filename&max-width=600";
// $example2 = "resize?name=$new_filename";

header("Content-type: text/html", $replace = true, 200);

html_header();

// TODO: Show image size, etc.
echo <<<EOF

<h1><a href=".">picdir</a></h1>

<p>Image saved!</p>

<h2>Original</h2>

<code><a href="$upload_path">$upload_path</a></code>

<h2>Resize it with a URL like this</h2>

<code><a href="$example">$example</a></code>

<!--
<p>
Plain: <code><a href="$example2">$example2</a></code>
-->

EOF;

html_footer();

?>
