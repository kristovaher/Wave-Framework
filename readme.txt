
WAVE FRAMEWORK
--------------

Open Source API-centric PHP Micro-framework

ABOUT THIS README
-----------------

This ReadMe file, that you are reading right now, is specifically about the Wave Framework. Unless it has been modified or mentioned anywhere, this ReadMe has no details about the website or web service that has been built on Wave Framework. To get details about the website or web service, you should either refer to another ReadMe file, if provided, file headers or ask the authors of the website or the web service.

ABOUT
-----

Wave is a PHP micro-framework that is built loosely following model-view-control architecture and factory method design pattern. It is made for web services, websites and info-systems and is built to support a native API architecture, caching, user control and smart resource management. Wave is a compact framework that does not include bloated libraries and features and is developed keeping lightweight speed and optimizations in mind. While not necessary for using Wave Framework, it comes by default with a URL and View controllers intended for building websites by solving URL requests and loading views.

Mercurial and Git repositories is available for developers who are interested in following the development.

Official website and documentation:
http://www.waveframework.com

Social networks for latest news: 
Google+ - http://plus.google.com/106969835456865671988 
Facebook - http://www.facebook.com/WWWFramework 
Twitter - http://www.twitter.com/WWWFramework

FEATURES
--------

 * Modern API-centric framework for PHP versions 5.3 and above
 * Secure API requests with hash validation, token and key-based authentication
 * Dynamically loaded Hierarchical MVC objects through Factory pattern
 * API returns XML, CSV, JSON, HTML, native PHP and other data formats
 * Compressed data output with Deflate and Gzip
 * Input and output data is fully UTF-8
 * PDO-specific database controller for general-use database connections
 * Index gateway and Handlers for all types of HTTP requests
 * Caching system with tagging support for all types of dynamic and static requests
 * View and URL Controllers that support multiple languages and clean URL's
 * Users and permissions control
 * jQuery JavaScript framework supported
 * On-demand dynamic image resizer and editor
 * On-demand resource compression, unifying and minifying
 * Automatic sitemap.xml and robots.txt generation
 * Automatically generated API documentation
 * API wrapper classes that make browser and server to server communication easy
 * 256bit Rijndael encrypted data transmission
 * API Observers for creating event-specific listeners
 * Installation-specific MVC class and resource overrides
 * Request limiter that can block HTTP requests under certain conditions
 * Request logger that can be used for detailed performance grading of HTTP requests
 * Debugging, backup, update and filesystem maintenance tools
 * Compatibility script that tests support for server setup
 * Supports Apache and Nginx servers in Linux and Windows environments
 * Supports APC extension
 * 100+ pages of detailed documentation and tutorials
 * Licensed under GNU Lesser General Public License Version 3
 
INSTALLATION
------------

 1. Unpack the Wave Framework downloaded archive.
 2. Configuration file in root directory /config.ini of the archive should be configured according to your needs. Read more about configuration from Wiki.
 3. Upload the files to your server and make filesystem folder /filesystem/ and all of its subfolders writable by PHP, for example with command chmod 0777 or giving rights using FileZilla (Right click on folder -> File Permissions -> Numeric value -> 777) or with any other FTP software. This is not required on Windows server.
 4. Wave Framework requires servers ability to redirect all requests to /index.php file, thus Apache RewriteEngine or Nginx HttpRewriteModule has to be used. Look at points 6a and 6b, depending on your server.
 5a. This only applies to Apache server: In some hosting environments the line in /.htaccess and /tools/.htaccess 'Options +FollowSymLinks ' may throw an error, if it does then this line should be commented out and compatibility script tried again.
 5b. This only applies to Nginx server: For rewrites to work properly you need to place settings found in /nginx.conf file to your Nginx server configuration.
 6. Test if server is set up properly by making a request to /tools/compatibility.php script.
 7. Access the root file with a browser and if 'Hello WWW!' is shown without any errors, then everything should be up and running. There is no setup script that needs to be run and you can start developing your application right away.
 8. Important! Make sure to change http-authentication-username and http-authentication-password lines in /config.ini file (line #29 and #30). These are used to authenticate access to developer tools in /tools/ directory and it may pose a security risk if left unchanged.
 
HELP AND DOCUMENTATION
----------------------

Official documentation about how to set up a system and use the API, as well as tutorials are available under /doc/ directory. To read documentation, open /doc/start.htm file with a web browser.

If you wish to participate in Wave Framework development and submit patches, fixes and new features, then it is recommended to do so through GitHub.

I am also willing to answer questions regarding Wave Framework when contacted through info@waveframework.com e-mail address.

REPOSITORIES
------------

GitHub - https://github.com/kristovaher/Wave-Framework
SourceForge - https://sourceforge.net/projects/www-php/
Google Code - http://code.google.com/p/www-framework/
BitBucket - https://bitbucket.org/kristovaher/wave-framework

AUTHOR
------

Kristo Vaher
kristo@waher.net
http://www.waher.net
http://plus.google.com/102632377636999004385/

LICENSE
-------

This framework is released as open source and its components (with the exception of external components included in this package and detailed in the next section) are released under GNU Lesser General Public License Version 3. This license means that you can use this Framework for any purpose and attach it to any website or web service without requiring to apply the same license to the system it is attached to, as long as the copyright and license notes remain with the framework. Full license document is included in the archive as license.txt file.

Note that some files, such as files in /models/, /views/, /controllers/ and /resources/ subfolder, are not restricted by GNU LGPL v3 license. These files can be copied, changed and re-published under another license without any restrictions, unless stated differently in the file header.

If you are reading this and this framework has been set up on a web server somewhere and used as a framework for a web system, then note that only Wave Framework itself is covered by the GNU LGPL v3 license. Other components of the website or web service itself may be covered by another license and may not be open source like Wave Framework itself is. Every readable file of this framework carries specific copyright and license notes in their headers. If you are not sure, then please refer to the headers of files to see if they carry another license.

ADDITIONAL COMPONENTS
---------------------

Wave also incorporates the following open source components that are not required for the functionality of the framework, but are included for bootstrapping reasons.

jQuery
* http://jquery.com/
* /resources/jquery.js
* License: Released under either the MIT License or the GNU General Public License Version 2

YUI Reset CSS
* http://yuilibrary.com/yui/docs/cssreset/
* /resources/reset.css
* License: Released by Yahoo! Inc. under BSD License

Adminer
* http://www.adminer.org/
* /tools/adminer.php
* License: Released by Jakub Vrana under Apache License Version 2 and GNU General Public License Version 2
