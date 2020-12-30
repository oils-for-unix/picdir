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

  # for php_codesniffer, weird.  Except that code sucks.
  # sudo apt install php-xml
}

export PICDIR_UPLOAD_DIR="_upload"
export PICDIR_RESIZED_DIR="_resized"

serve() {
  mkdir -p $PICDIR_UPLOAD_DIR $PICDIR_RESIZED_DIR
  php -S localhost:8991
}

hash-password() {
  local pass=$1
  local out=${2:-password}
  php hash_password.php "$pass" > $out
}

deploy() {
  local name=$1
  local password=${2:-}

  local host=$name@$name.org
  local dir=$name.org/picdir

  # Different password for each host
  if test -n "$password"; then
    hash-password "$password" _tmp/password
    scp _tmp/password $host:$dir
  fi

  # Matches default in config.php
  ssh $host mkdir -v -p $dir/{upload,resized}

  scp *.php *.css $host:$dir
}

unit-tests() {
  # This is stupid, Ubuntu has it off by default
  for file in *_test.php; do
    php -d 'zend.assertions=1' $file
  done
}

lint() {
  for file in *.php; do
    php -l "$file"
  done
}

# https://getcomposer.org/download/
install-composer() {
  wget https://getcomposer.org/installer
  php installer
}

# it automatically installs here
composer() {
  ./composer.phar "$@"
}

# https://github.com/FriendsOfPHP/PHP-CS-Fixer

# Hm this doesn't fix style.

install-fixer() {
  mkdir --parents tools/php-cs-fixer
  composer require --working-dir=tools/php-cs-fixer friendsofphp/php-cs-fixer
}

fixer() {
  tools/php-cs-fixer/vendor/bin/php-cs-fixer "$@"
}

# This gives you 4 space indents, not good
fix-all() {
  for name in *.php; do
    fixer fix $name
  done
}

# requires php-xml package
install-sniffer() {
  mkdir -p tools/php_codesniffer
  composer require --working-dir tools/php_codesniffer "squizlabs/php_codesniffer=*"
}

cbf() {
  tools/php_codesniffer/vendor/bin/phpcbf "$@"
}

# Also does stuff I don't want
cbf-all() {
  for name in *.php; do
    cbf $name
  done
}

#
# Production Tests
#

dreamhost-latency() {
  local name=$1

  set -x
  # PHP
  time curl http://$name.org/picdir/hello.php

  # PHP with image from file system
  time curl -o /tmp/foo.jpg \
    'http://chubot.org/picdir/resize.php?name=z6wh1m__IMG-5786.jpg&max-width=400'

  # Fast CGI
  time curl http://$name.org/wwz-test/test.wwz/b.txt

  # static file
  time curl http://$name.org/wwz-test/b.txt

  # Different domain
  time curl http://www.oilshell.org/ | wc -l
}

"$@"
