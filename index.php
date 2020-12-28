<?php
include('header.php');

echo <<<EOF
<h1>picdir</h1>

<form action="upload.php" method="post" enctype="multipart/form-data">
  <table>
    <tr>
      <td>Image</td>
      <td><input type="file" name="image" /></td>
    </tr>

    <tr>
      <td>Or Drag and Drop</td>
      <td>TODO</td>
    </tr>

    <tr>
      <td>Password</td>
      <td><input type="password" name="password" /></td>
    </tr>
    <tr>
      <td></td>
      <td><input type="submit" value="Upload" /></td>
    </tr>
  </table>

</form>

<h2>Recent Uploads</h2>

TODO

EOF;

include('footer.php');
?>

