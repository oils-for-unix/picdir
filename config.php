<?php
// NOTE: The web server has to be configured to serve out of both of these
// dirs.

// TODO: Assert that they're both relative?
$upload_dir = getenv('PICDIR_UPLOAD_DIR');
if ($upload_dir === false) {
  $upload_dir = 'upload';
}

$cache_dir = getenv('PICDIR_CACHE_DIR');
if ($cache_dir === false) {
  $cache_dir = 'cache';
}

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
