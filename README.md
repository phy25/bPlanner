bPlanner (Open Source Version)
==============================

JWC Schedule fetcher for BIT's __hackers__ in PHP. Includes iCal generation code.

If you don't know PHP, that's fine. There are lots of fun codes that you can make use of, to handle class schedule page of JWC :).

How to use
----------

1.  Install normal PHP environment (Needs PHP 5.2+ with CURL, iconv).
2.  Download all files and put them in the right folder.
3.  Open `index.php` as the server url in your browser (like http://localhost/bPlanner/index.php).
4.  Currently if you need iCal, please put all content into `data.php` with `<?php ` at the beginning. Then open `index.php?action=ical`. Better support is on the way.