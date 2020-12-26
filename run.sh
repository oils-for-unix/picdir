#!/usr/bin/env bash
#
# Usage:
#   ./run.sh <function name>

set -o nounset
set -o pipefail
set -o errexit

# Got this working:
#
# https://www.geekality.net/2011/05/01/php-how-to-proportionally-resize-an-uploaded-image/
# http://samples.geekality.net/image-resize/

# https://stackoverflow.com/questions/13338339/imagecreatefromjpeg-and-similar-functions-are-not-working-in-php
deps() {
  sudo apt install php php-gd
}

serve() {
  php -S localhost:8001
}

deploy() {
  local name=$1

  local host=$name@$name.org
  local dir=$name.org/imagebin/

  ssh $host mkdir -v -p $dir

  scp upload.php index.html $host:$dir
}

install-composer() {
  echo TODO

}

"$@"
