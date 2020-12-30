<?php
include('lib.php');

html_header();

echo <<<EOF
<h1>picdir</h1>

<form action="upload.php" method="post" enctype="multipart/form-data">
  <table>
    <tr>
      <td>Image</td>
      <td><input type="file" name="image" /></td>
    </tr>
EOF;

if ($HASHED_PASSWORD) {
  echo <<<EOF
    <tr>
      <td>Password</td>
      <td><input type="password" name="password" /></td>
    </tr>
EOF;
}

echo <<<EOF
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
foreach (scan_dir($UPLOAD_DIR) as $file) {
  // sanitized on the file system
  $url = "resize.php?name=$file&max-width=400";
  echo <<<EOF
<p> 
  <code> <a href="$url">$file</a> </code> <br/>
  <img src="$url">
</p>
EOF;

  $i++;
  if ($i === 10) {
    break;
  }
}

# And then <img src="resize.php?name=$name&max-width=100"> for small thumbnails

html_footer();
?>

