<?php
include('lib.php');

html_header();

echo <<<EOF
<form action="upload" method="post" enctype="multipart/form-data">
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
  $ignored = array('.', '..');

  $files = array();    
  foreach (scandir($dir) as $file) {
    // TODO: match extensions instead
    if (in_array($file, $ignored)) {
      continue;
    }
    $files[$file] = filemtime($dir . '/' . $file);
  }

  arsort($files);
  $files = array_keys($files);

  return $files;
}

$i = 0;
foreach (scan_dir($UPLOAD_DIR) as $file) {
  // sanitized on the file system
  $url = "resize?name=$file&max-width=400";
  $orig_url = "$UPLOAD_DIR/$file";
  $kilobytes = number_format(round(filesize($orig_url) / 1000));
  echo <<<EOF
<p> 
  <code> <a href="$url">$file</a> </code> (<a href="$orig_url">original</a> is $kilobytes KB) <br/>
  <img src="$url">
</p>
EOF;

  $i++;
  if ($i === 10) {
    break;
  }
}

# And then <img src="resize?name=$name&max-width=100"> for small thumbnails

html_footer();
?>

