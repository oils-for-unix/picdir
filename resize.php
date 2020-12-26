<?php
include('config.php');

// TODO:
// 1. Check if the file exists in the data dir
// 2. Check if the resized version already exists in the cache dir
//    If not, create it.
// 3. 301 Permanent Redirect to the cached version
//    https://www.seoclarity.net/resources/knowledgebase/use-301-redirect-vs-302-redirect-15683/
//
// The web server has to be configured to serve it.

echo '<pre>';

$name = $_GET['name'];
$width = $_GET['w'];
$height = $_GET['h'];

echo "name = " . $name . "\n";
echo "width = " . $width . "\n";
echo "height  = " . $height . "\n";

echo '</pre>';

?>



