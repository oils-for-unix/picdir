<?php
// NOTE: The web server has to be configured to serve out of both of these
// dirs.

// TODO: Assert that they're both relative?
$UPLOAD_DIR = getenv('PICDIR_UPLOAD_DIR');
if ($UPLOAD_DIR === false) {
  $UPLOAD_DIR = 'upload';
}

$RESIZED_DIR = getenv('PICDIR_RESIZED_DIR');
if ($RESIZED_DIR === false) {
  $RESIZED_DIR = 'resized';
}

$HASHED_PASSWORD = file_get_contents('password');
// $HASHED_PASSWORD may be false

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

?>
