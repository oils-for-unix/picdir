<?php
include('config.php');

// NOTE: assert_options() is deprecated

$status = 0;

function callback($file, $line, $code, $desc = null) {
  // echo "CALLBACK\n";

  global $status;
  $status = 1;

  if (false) {
    echo "$file:$line: $code";
    if ($desc) {
      echo ": $desc";
    }
    echo "\n";
  }
}

assert_options(ASSERT_CALLBACK, 'callback');

// And I have to set this through the command line -d, not here!
// ini_set('zend.assertions', 1);

// Stop at first failure
// ini_set('assert.exception', 1);


function test_functions() {
  for ($i = 0; $i < 5; $i++) {
    echo "unique_id() = " . unique_id() . "\n";
  }

  // echo sanitize('my dir/bar.jpg') . "\n";
  assert(sanitize('my dir/bar.jpg') === 'my_dir_bar.jpg');

  // Don't use the second argument
  // assert(1 === 0, 'bad');

  assert(1 === 1);
}

test_functions();

if ($status === 0) {
  echo "OK\n";
}

exit($status);

?>
