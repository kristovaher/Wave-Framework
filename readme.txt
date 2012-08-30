
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
 
 1. Unpack the downloaded archive of Wave Framework or go to the repository folder if you downloaded through Git or Mercurial.
 
 2. Configuration file '/config.ini' in root directory of the archive should be configured according to your needs. Configuration file has multiple comments about each setting, but they can be left undefined and unchanged at first, since the Framework is able to define defaults for most settings by itself.
 
 3. Upload the entire archive to your server and make filesystem folder '/filesystem/' and all of its subfolders writable by PHP with command 'chmod 0755' or giving rights using an FTP program like FileZilla (Right click on folder -> File Permissions -> Numeric value -> 0755). This is not required on Windows server (or Windows localhost) since Windows has folders writable by default.
  * On some servers the FTP account is configured so that 0755 will not allow PHP to actually write to these folders if you set the rights over FTP, so you should either ask the administrator to change the account permissions or set the folder permissions to 0777 and see if that works when 0755 did not (this is less secure however, so you should only do it temporarily).
  * Some servers modify the files uploaded through FTP in some way, often breaking line breaks and thus functionality of the scripts. If you encounter problems and there is no clear error message, then make sure that the uploaded files are correctly stored on the server. Upload should work correctly if you upload files in Binary mode (In FileZilla it is 'Transfer -> Transfer Type -> Binary').
 
 4. Wave Framework requires servers ability to redirect all requests to '/index.php' file, thus Apache RewriteEngine or Nginx HttpRewriteModule has to be used. Look at points 5A or 5B, depending on your server.
 
 5A. Apache
  * On most server setups the Apache-related settings in this list should already be enabled and everything should work, but in case you run into problems and you cannot edit Apache configuration yourself, then ask for assistance from your hosting provider.
  * Make sure that you uploaded '/.htaccess' and '/tools/.htaccess' files to the server, as sometimes they may not be uploaded due to operating system thinking they are hidden.
  * Apache also needs to support '.htaccess' directives from those files, so make sure that 'AllowOverride' setting is 'All' in Apache directory configuration. This is usually enabled by default on most servers.
  * Since RewriteEngine is required for URL rewrites, your server needs to have the module loaded, which means that the line 'LoadModule rewrite_module modules/mod_rewrite.so' needs to be uncommented in Apache. This is usually enabled by default on most servers.
  * On some hosting environments the line 'Options +FollowSymLinks' in '.htaccess' may throw an error, so if an error is thrown then I suggest commenting or removing that line and trying the compatibility script again.
 
 5B. Nginx
  * For rewrites to work properly you need to place the configuration settings found in '/nginx.conf' file to your Nginx server configuration. This is more complicated than setting up redirects locally on Apache servers, since a lot of Nginx servers have very different configurations and just a single configuration file (Nginx has no '.htaccess' like functionality) and you need to implement your configuration inside that main configuration. If you know a little about Nginx server configuration, then it should not be a problem.
 
 6. Test if server is set up properly by making a request to '/tools/compatibility.php' script and fix any errors that Compatibility script might throw. Warnings can be ignored, but Wave Framework works at its best if it encounters no warnings in Compatibility script.
 
 7. Access the root file with a web browser. If 'Hello Wave' is shown together with a pretty logo, without any errors, then everything should be up and running! There is no setup script that needs to be run separately and you can start developing your application right away.
  * If the page shows an error message, then make sure that the '/filesystem/' folders are writable and that the configuration steps above have been followed.
  * Make sure that the files were uploaded correctly and that FTP did not convert line breaks to single line in uploaded files. If it did, then fix this by uploading files in Binary mode.
  * You can also take a look at '/tools/debugger.php' script in case you encounter errors even if Compatibility script says that everything is alright with the server. If Debugger script does not show any warnings and your page still shows errors, then the error happens in core, such as version incompatibility, file permissions and more. Double check that you have followed the previous steps.
 
 8. Important! Make sure to change http-authentication-username and http-authentication-password lines in /config.ini file (line #29 and #30). These are used to authenticate access to developer tools in /tools/ directory and it may pose a security risk if left unchanged as all downloaded archives have the same username and password at first.
 
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
