<?php
include('header.php');

echo <<<EOF
<h1>picdir</h1>

<form action="upload.php" method="post" enctype="multipart/form-data">
  <input type="file" name="image" />
  <input type="submit" value="Upload" />
</form>

EOF;

include('footer.php');
?>

