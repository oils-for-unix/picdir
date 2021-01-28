<?php
// NOTE: The web server has to be configured to serve out of both of these
// dirs.

// TODO: Assert that they're both relative?
$UPLOAD_DIR = getenv('PICDIR_UPLOAD_DIR');
if ($UPLOAD_DIR === false) {
  $UPLOAD_DIR = 'uploads';
}

$RESIZED_DIR = getenv('PICDIR_RESIZED_DIR');
if ($RESIZED_DIR === false) {
  $RESIZED_DIR = 'resized';
}

// $HASHED_PASSWORD may be false.  Suppress error.log warning.
$HASHED_PASSWORD = @file_get_contents('password');

// TODO: Password here to avoid DoS with disk space, or do it on the server
// level?

//
// Free Functions for Testing
//

function sanitize($filename) {
  return preg_replace('/[^a-zA-Z0-9._-]/', '_', $filename);
}

function unique_id() {
  // This isn't foolproof, but it should be enough to discourage attackers from
  // trying to overwrite files.
  //
  // It also seems better than the builtin uniqid(), which can return the same
  // value in a tight loop.
  // https://www.php.net/manual/en/function.uniqid.php
  //
  // And the string is short (unlike an md5sum, which also isn't foolproof)
  return base_convert(time() + rand(), 10, 36);
}

//
// Templates
//

function html_header() {
  echo <<<EOF
<!DOCTYPE html>
<html>
  <head>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>picdir</title>

    <link rel="stylesheet" type="text/css" href="picdir.css" />
  </head>
  <body>
    <p>
      <span id="picdir-header"><a href=".">picdir</a></span>
      serves dynamically resized images (<a href="https://github.com/oilshell/picdir">source code</a>)
    </p>
    <hr/>

EOF;
}

function html_footer() {
  echo <<<EOF
  </body>
</html>

EOF;
}

?>
