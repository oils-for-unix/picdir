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

$name = $_GET['name'];
$width = $_GET['w'];
$height = $_GET['h'];

error_log("name = " . $name . "\n");

if (! isset($name)) {
  exit("Expected name= param\n");
}

header('Location: ' . $name);
exit();

echo '<pre>';


echo "name = " . $name . "\n";
echo "width = " . $width . "\n";
echo "height  = " . $height . "\n";

echo '</pre>';

?>



