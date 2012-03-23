
WWW FRAMEWORK
-------------

API-focused web and infosystems PHP micro-framework

ABOUT
-----

WWW is a PHP micro-framework that is built loosely on model-view-control architecture and factory method design pattern. It is made for web services, websites and info-systems and is built around a native API architecture, caching and smart image and resource management. WWW is a compact framework that does not include optional libraries, has a very small footprint and is developed keeping lightweight speed and optimizations in mind.

WWW comes by default with a view controller and a gateway intended for website functionality with clean URLs that also incorporates a front-end JavaScript controller.

Documentation about how to set up a system and use the API, as well as tutorials are available at https://sourceforge.net/p/www-php/wiki/Home/

Mercurial and Git repository is available for developers who are interested in following the development.

Social networks for latest news: 
Google+ - http://plus.google.com/106969835456865671988 
Facebook - http://www.facebook.com/WWWFramework 
Twitter - http://www.twitter.com/WWWFramework

FEATURES
--------

 * Modern API-focused framework
 * Secure API requests with input and token and key-based authentication
 * Dynamically loaded Hierarchical MVC objects through Factory pattern
 * API returns XML, CSV, JSON, HTML, native PHP and other data formats
 * Compressed data output with Deflate and Gzip
 * Input and output data is fully UTF-8
 * PDO-specific database controller for general-use database connections
 * Apache-driven smart Index gateway that handles all HTTP requests
 * Full cache control for all types of dynamic and static requests
 * View and URL controllers that support multiple languages and clean URL's
 * jQuery based front-end UI controller with smart AJAX functionality
 * On-demand dynamic image resizer and basic image editor
 * On-demand resource compressing, unifying and minifying
 * Automatic sitemap.xml and robots.txt generation
 * Installation-specific MVC class and resource overrides
 * Request limiter that can block HTTP requests under various conditions
 * Request logger that can be used for detailed performance grading of HTTP requests
 * Backup, update and filesystem maintenance tools
 * Compatibility script that tests support for server setup
 * Supports Linux and Windows servers, LAMP and WAMP setups
 * Licensed under GNU Lesser General Public License Version 3
 
INSTALLATION
------------

 1. Unpack the WWW Framework downloaded archive.
 2. Configuration file in root directory /config.php of the archive should be configured according to your needs. Read more about configuration from Wiki.
 3. Upload the files to your server and make filesystem folder /filesystem/ and all of its subfolders writeable by PHP, for example with command chmod 0777 or giving rights using FileZilla (Right click on folder -> File Permissions -> Numeric value -> 777) or with any other FTP software. This is not required on Windows server.
 4. There is a tool script for checking if your system is compatible for WWW Framework or not. After you have uploaded scripts to server, run the script /tools/compatibility.php. If this script shows any errors, then WWW Framework might not work properly.
 5. In some hosting environments the line in /.htaccess and /tools/.htaccess 'Options +FollowSymLinks ' may throw an error, if it does then this line should be commented out and compatibility script tried again.
 6. Access the root file with a browser and if 'Hello WWW!' is shown without any errors, then everything should be up and running. There is no setup script that needs to be run and you can start developing your application right away.
 
HELP AND DOCUMENTATION
----------------------

Official documentation and tutorials are available at https://sourceforge.net/p/www-php/wiki/Home/

Support and tickets should be posted at https://sourceforge.net/p/www-php/tickets/

I am also willing to answer questions regarding WWW Framework when contacted through e-mail.

REPOSITORIES
------------

SourceForge - https://sourceforge.net/projects/www-php/
Google Code - http://code.google.com/p/www-framework/
BitBucket - https://bitbucket.org/kristovaher/www-framework
GitHub - https://github.com/kristovaher/WWW-Framework

AUTHOR
------

Kristo Vaher
kristo@waher.net
http://www.waher.net/+

LICENSE
-------

This framework is released as open source and its components (with the exception of external components included in this package and detailed in the next section) are released under GNU Lesser General Public License Version 3. Full license document is included in the archive as license.txt file.

AUTHORS OF ADDITIONAL COMPONENTS
--------------------------------

jQuery
http://jquery.com/
Released under either the MIT License or the GNU General Public License Version 2

YUI Reset CSS
http://yuilibrary.com/yui/docs/cssreset/
Released by Yahoo! Inc. under BSD License

Adminer
Released by Jakub Vrana under Apache License Version 2 and GNU General Public License Version 2
