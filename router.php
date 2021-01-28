<?php

// Simple router used for the dev server AND in production.
// Get rid of .php extensions, so we can rewrite it in a different language.

$uri = $_SERVER['REQUEST_URI'];
// error_log("uri = $uri");

// get rid of query string
$path = parse_url($uri, PHP_URL_PATH);

// get rid of /picdir prefix
if (strpos($path, '/picdir') === 0) {
  $path = substr($path, strlen('/picdir'));
}
// error_log("path = $path");

// /upload, /resize -> $1.php  This matches the rewrite in .htaccess.
// Note: index.php is automatically mapped to /
$matches = array();
if (preg_match('/^\/([a-z_\-]+)$/', $path, $matches)) {
  $name = $matches[1];
  $script = "$name.php";
  if (file_exists($script)) {
    require $script;
    exit();
  }
}

error_log("[router] Not handling '$path'");

// upload.php, resize.php: NOT handled here.  Dev servers falls back to its
// normal logic.

// TODO: This should serve a 404 under Apache.

return false;

?>
