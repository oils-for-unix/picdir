<?php
include('config.php');

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

EOF;



# https://stackoverflow.com/questions/11923235/scandir-to-sort-by-date-modified

function scan_dir($dir) {
  $ignored = array('.', '..', '.svn', '.htaccess');

  $files = array();    
  foreach (scandir($dir) as $file) {
    if (in_array($file, $ignored)) continue;
    $files[$file] = filemtime($dir . '/' . $file);
  }

  arsort($files);
  $files = array_keys($files);

  return $files;
}

$i = 0;
foreach (scan_dir($upload_dir) as $file) {
  echo "<p> <img src=\"resize.php?name=$file&max-width=400\"> ";  // sanitized on the file system

  $i++;
  if ($i === 10) {
    break;
  }
}

# And then <img src="resize.php?name=$name&max-width=100"> for small thumbnails

include('footer.php');
?>

