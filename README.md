picdir
======

A small program that receives upload images and allows resizing at serving time.

## Files

- `upload.php`: Upload an image and store it with a unique filename.
  - need optional password
- `resize.php`: Request a resized version of an image.

## Ideas

- `fetch.php`: Fetch image with curl?
- `all.php`: List all images, instead of just recent ones.
- Configure Dreamhost send mails from picdir@oilshell.org to this script too?
  - Will it respond with a URL?
  - Or maybe you can view "recently mailed"?
  - Or use a free tier of an API?
- Support multiple uploads on a page, or many in an e-mail?


## Links

- JavaScript drag and drop.  This would be nice but looks complex, a lot of
  code:
  - <https://www.smashingmagazine.com/2018/01/drag-drop-file-uploader-vanilla-js/>

## Configuration

If the file `password` exists, it should contain a password hashed with PHP's
`password_hash()`.  The app will require a password to upload a picture.


