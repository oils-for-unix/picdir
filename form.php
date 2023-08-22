<?php

include('lib.php');

$filename = $_GET['filename'];
if (!isset($filename)) {
  exit("Expected filename= param\n");
}
$filename = sanitize($filename);

$body = <<<EOF
  <form action="resize" method="GET">
    <table>
      <tr>
        <td>Image Name</td>
        <td><input type="text" name="name" value="$filename" /></td>
      </tr>

      <tr>
        <td>Max Width</td>
        <td><input type="text" name="max-width" value="600" /></td>
      </tr>

      <tr>
        <td>Rotation (counter-clockwise)</td>
        <td><input type="text" name="rotation"/></td>
      </tr>

      <tr>
        <td>Serve Slowly Through PHP (to copy URL)</td>
        <td><input type="checkbox" name="serve-slowly-through-php" value="1"/></td>
      </tr>

      <tr>
        <td></td>
        <td><input type="submit" value="Show Image" /></td>
      </tr>
    </table>
EOF;


header("Content-type: text/html", $replace = true, 200);

html_header();

echo($body);

echo('<p><a href=".">Back to home page</a></p>');

html_footer();

?>
