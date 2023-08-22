<?php
// upload.php
//
// Upload a list of images and save them to
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

// Check if images were uploaded
if (! isset($_FILES['images'])) {
  exit('Expected images=');
}

// The form has multiple="multiple"
$num_files = count($_FILES['images']['name']);
// error_log("num files $num_files");

$body = '';

// Loop through each file
for ($i = 0; $i < $num_files; $i++) {

  $tmp_name = $_FILES['images']['tmp_name'][$i];
  // error_log("uploaded file $tmp_name");

  if (! is_uploaded_file($tmp_name)) {
    exit('Expected images= to be a file (dev server rejected big file?)');
  }

  $error = $_FILES['images']['error'][$i];
  if ($error !== UPLOAD_ERR_OK) {
    exit("Upload failed with error $error");
  }

  $filename = $_FILES['images']['name'][$i];
  // Check if images were uploaded
  if (! isset($filename)) {
    exit('Expected image name');
  }

  $new_filename = unique_id() . '__' . sanitize($filename);
  $upload_path  = "$UPLOAD_DIR/$new_filename";

  error_log("$tmp_name -> $upload_path");
  move_uploaded_file($tmp_name, $upload_path);

  // TODO: Add redirect=0 or redirect=1 to avoid redirection, which allow you
  // to copy and paste
  $example = "resize?name=$new_filename&max-width=600";

  // Append a snippet to the body.
  // TODO: Show original image size, etc.
  $body .= <<<EOF
  <p>Saved <code><a href="$upload_path">$upload_path</a></code></p>

  <p><b>Serve small (and rotated) versions with URLs like this:</b></p>

  <code><a href="$example">$example</a></code> (redirects to a static file)

  <form action="resize" method="GET">
    <table>
      <tr>
        <td>Image Name</td>
        <td><input type="text" name="name" value="$new_filename" /></td>
      </tr>

      <tr>
        <td>Max Width</td>
        <td><input type="text" name="max-width" value="600" /></td>
      </tr>

      <tr>
        <td>Rotation</td>
        <td><input type="text" name="rotation"/></td>
      </tr>

      <tr>
        <td></td>
        <td><input type="submit" value="Show Image" /></td>
      </tr>
    </table>

  </form>

  <hr/>

EOF;

}

header("Content-type: text/html", $replace = true, 200);

html_header();

echo($body);

echo('<p><a href=".">Back to home page</a></p>');

html_footer();

?>
