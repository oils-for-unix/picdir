picdir
======

A simple web service that allows you to upload an image and then link to a
dynamically-resized version of it.

It's a useful tool for self-hosted blogs and documentation.

## Configuration

- If the file `password` exists, it should contain a password hashed with PHP's
  `password_hash()`.  The app will require a password to upload a picture.
- Because of the way `router.php` and `.htaccess` work, it should be hosted at
  `https://www.example.com/picdir/`.

## Files

- `router.php`: Used to get rid of `.php` extensions.  Passed to the PHP dev
  server, AND runs in production via `.htaccess`.
- `index.php`: The home page.
- `upload.php`: Upload an image and store it with a unique filename.
- `resize.php`: Request a resized version of an image.  Computes it, and then
  redirects to a static file.

## Data

- `uploads/`: Where user data is stored.
- `resized/`: Where resized images are cached.

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
- [CImage and img.php](https://cimage.se/): *server-side resize, crop and
  processing of images using PHP GD*.
- Comment on picdir: <https://lobste.rs/s/nmvcdk/why_bloat_is_still_software_s_biggest#c_c1t7ho>

