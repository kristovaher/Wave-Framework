<?php

/*
WWW Framework
General configuration

All configuration settings in this file are loaded into State where certain variables, such as 
error-reporting, can turn on or off certain PHP settings. This configuration script is also 
used by scripts under /tools/ folder. Note that most settings in this file are commented
out since system can function with default values. Uncomment and edit configuration only 
when wishing to change defaults.

* HTTP authentication settings
* Debugging and logging settings
* Database access settings
* Server and content settings
* Optimizations and cache settings
* Dynamic image loading settings
* API settings
* Request limiter settings
* Session settings

Author and support: Kristo Vaher - kristo@waher.net
*/

// HTTP AUTHENTICATION

	// If this is turned on then HTTP authentication is required before Index and API gateways work
	// These credentials are always used for developer tools under /tools/ directory though (with the exception of Adminer)
	$config['http-authentication']=false;
	$config['http-authentication-username']='developer';
	$config['http-authentication-password']='hellowww';
	
// DEBUGGING AND LOGGING

	// This turns on Index gateway performance logging
	// Value of '*' means that all data is logged, other values should be comma-separated values of the logged array (check using /tools/log-reader.php)
	// Set this to false to turn off logging entirely
	// This is set to false by default
	$config['logger']='*';

	// This can set the PHP error-reporting state
	// This value is 0 by default
	// $config['error-reporting']=E_ALL;

// DATABASE ACCESS

	// WWW Framework can connect to MySQL, PostgreSQL, SQLite, MS SQL and Oracle databases
	// If these values are not set API will not connect to a database
	// Database is not used by default and these values are not set
	// $config['database-type']='mysql';
	// $config['database-host']='localhost';
	// $config['database-name']='';
	// $config['database-username']='';
	// $config['database-password']='';

// SERVER AND CONTENT

	// Project title
	// This value, if defined, is shown by View controller at the end of page meta title
	// This value is set as 'WWW Framework' by default
	// $config['project-title']='WWW Framework';

	// Host domain name
	// This value is the host name of the server
	// If left undefined then server will define this setting by itself based on request
	// $config['http-host']='www.example.com';

	// Comma separated list of languages
	// These languages here are used for language detection as the first URL node, like http://www.example.com/{language-code}/page
	// Default value is 'en' and the first value in this array is considered as the 'first language' or 'main' language
	// These values must exist have translations files in resources folders, such as /resources/{language-code}.translations.php
	// $config['languages']='en';

	// Default language
	// This is considered the default language that URL controller will consider as the main language if language is not detected in URL
	// If not defined, this value will be assigned as the first value in 'languages' setting
	// $config['language']='en';

	// Setting timezone
	// This is useful for various date functions, so the displayed time would be correct
	// System uses Unix timestamp internally, so changing this value will not break the system
	// This is detected by default based on server value if not set here
	// $config['timezone']='Europe/London';

	// 404 view name
	// This is what Index gateway considers as the 404 'view' in case URL does not exist
	// This view must exist as a /views/class.{home-view}.php file
	// Default value is '404' and it loads /views/class.404.php file
	// $config['404-view']='404';

	// Home view name
	// This is what Index gateway considers as the default 'view' in case URL is set to root
	// This view must exist as a /views/class.{home-view}.php file
	// Default value is 'home' and it loads /views/class.home.php file
	// $config['home-view']='home';
	
	// Web root
	// This value is the relative URL node. If server is installed to http://www.example.com/mypage/ then this value is '/mypage/'
	// This is used in HTML views to reference to files on the server, such as location of JavaScript or image files
	// If this value is not defined then State detects this automatically
	// $config['web-root']='';
	
	// System root
	// This value is the absolute directory address on the web server for this installation
	// This is used internally in PHP for locations of include files and file locations
	// If this value is not defined then State detects this automatically
	// $config['system-root']='';
	
	// Enforcing slash at the end of URL
	// If this setting is turned on, then URL controller will redirect requests from URL's that don't end with a slash to ones that do
	// Example is redirecting http://www.example.com/mypage to http://www.example.com/mypage/
	// Slash in the end of the URL is considered the correct and standard way of writing page address
	// This value is 'true' by default
	// $config['enforce-url-end-slash']=false;
	
	// Enforcing first language URL node
	// If this value is set to true, then first language URL node must be defined in URL requests
	// URL controller will automatically redirect all URL's that don't include language nodes to ones that do
	// For example, http://www.example.com/contact/ would be then redirected to http://www.example.com/en/contact/
	// This only applies to first language, every other language would require the language node anyway
	// This value is 'true' by default
	// $config['enforce-first-language-url']=false;
	
	// Robots
	// This sets the default robots setting in the system
	// By default the value is 'noindex,nocache,nofollow,noarchive,noimageindex,nosnippet', but this can be overwritten by Sitemap 'robots' setting per URL
	// Robots can tell search engine crawlers and robots to either index or not specific URL's and images
	// $config['robots']='all';
	
	// Resource specific robots
	// This sets the default robots setting in the system for resource files
	// If these are not set, then handlers use the previous 'robots' setting when serving these files
	// $config['image-robots']='all';
	// $config['resource-robots']='all';
	// $config['file-robots']='all';
	
// OPTIMIZATIONS AND CACHE

	// Output compression
	// This sets what type of output compression is used, options include 'default' and 'gzip' or false
	// Note that whether the output will be actually compressed based on user agents request depends on whether user agent states compression type support in request header
	// This value is 'deflate' by default'
	// $config['output-compression']=false;
	
	// Index URL controller cache timeout
	// This is the cache duration for URL solving through URL controller
	// URL controller solves the requested URL to a specific view in the system based on /resources/{language-code}.sitemap.php file
	// If your sitemap does not change often then this value can be set higher
	// By default this value is set to 0 seconds and URL controller commands are not cached
	// $config['index-url-cache-timeout']=300;
	
	// Index View controller cache timeout
	// This is the cache duration for all returned views, which means that all loaded pages have a cache of this duration
	// It is possible to assign different cache duration settings by overrides this value per URL in /resources/{language-code}.sitemap.php file
	// By default this value is set to 0 seconds
	// $config['index-view-cache-timeout']=30;
	
	// Resource cache timeout
	// This sets how long static files are considered to be 'in cache'
	// Static files are most commonly CSS, JavaScript and image files
	// This affects returned max lifetime settings and internal cache duration (though that resets itself if original file is modified)
	// By default this value is set to 31536000, which is one year in seconds.
	// $config['resource-cache-timeout']=31536000;
	
	// Robots cache timeout
	// This setting states how long robots.txt contents are cached, if robots.txt is dynamically generated
	// Default value is four hours
	// $config['robots-cache-timeout']=14400;
	
	// Sitemap cache timeout
	// This setting states how long sitemap.xml contents are cached, if sitemap.xml is dynamically generated
	// Default value is four hours
	// $config['sitemap-cache-timeout']=14400;
	
// DYNAMIC IMAGES
	
	// 404 image placeholder
	// If this is set to true, then system returns /resources/placeholder.jpg file when the actual file cannot be found
	// This is set to true by default
	// $config['404-image-placeholder']=false;
	
	// Dynamic image loading
	// This flag sets if dynamic image loading is allowed
	// Dynamic image loading allows image resizing and basic editing over HTTP dynamically, such as generating thumbnails
	// This allows to return a 60x60 thumbnail of logo for example http://www.example.com/resources/images/60x60&logo.png
	// This is enabled by default
	// $config['dynamic-image-loading']=false;
	
	// Dynamic image max size
	// This sets the max width or height a dynamically generated image can be before returning 404
	// This is a security setting and is intended to restrict server from generating too large images
	// By default this value is set to 1000 pixels
	// $config['dynamic-max-size']=1000;
	
	// Dynamic size whitelist
	// This is an array of image resolutions that are allowed during image generation
	// RGB values should be stored in array like '120,60,14', '255,255,255' and so on
	// By default this value is not defined and all resolutions are allowed (as long as they are under dynamic-max-size setting)
	// $config['dynamic-size-whitelist']=array();
	
	// Dynamic color whitelist
	// This is an array of background colors that are allowed during image generation
	// RGB values should be stored in array like '120,60,14', '255,255,255' and so on
	// By default this value is not defined and all colors are allowed
	// $config['dynamic-color-whitelist']=array();
	
	// Dynamic quality whitelist
	// This is an array of quality percentages that are allowed during image generation
	// Values should be stored in array like '50', '30' and so on
	// By default this value is not defined and all quality settings are allowed
	// $config['dynamic-quality-whitelist']=array();
	
	// Dynamic image position whitelist
	// This is an array of image positions that are allowed during image generation
	// Values should be stored in array like 'top-left', '30-50' and so on
	// By default this value is not defined and all positions are allowed
	// $config['dynamic-position-whitelist']=array();
	
	// Dynamic image filters
	// This sets if image filters are allowed for dynamic image loading, in other words the filter() parameters
	// By default this value is set to true
	// $config['dynamic-image-filters']=false;
	
	// Dynamic filter whitelist
	// This is an array of image filters that are allowed during image generation
	// Values should be stored in array like 'grayscale@100', 'colorize@40,120,120,120' and so on (like they appear within filter() call)
	// Note that when no alpha is used in filter call, then it should still be written in this whitelist as 'grayscale@100' instead of just 'grayscale'
	// By default this value is not defined and all filters are allowed
	// $config['dynamic-filter-whitelist']=array();
	
// DYNAMIC RESOURCES

	// Dynamic resource loading
	// This true-false setting defines if resources can be loaded dynamically
	// Main behavior of this is that you can unify different /resource/ scripts and return them with the same HTTP request by separating their names with & symbol
	// This value is set to true by default
	// $config['dynamic-resource-loading']=false;

// API SETTINGS
	
	// This sets the default API profile that is used
	// All profile names that are not public require an API profile data set in /resources/api.keys.php
	// $config['api-public-profile']='public';
	
	// This setting assigns whether internal API logging/debugging is used
	// Turning this on allows MVC objects to call $this->internalLogEntry('log key','log entry')
	// This log can be read with log reader as /tools/log-reader.php?internal
	// By default this value is set to false
	// $config['internal-logging']=true;
	
// LIMITER

	// This turns on HTTP request limiter
	// If this is turned off then no limiter is checked at all and Limiter object is not created
	// By default this setting is false
	// $config['limiter']=true;
	
	// HTTP Authentication
	// If this is turned on then all HTTP requests have to be authenticated with HTTP username and password
	// Username and password are the same as the ones given above
	// By default this is false and is turned off
	// $config['http-authentication-limiter']=true;
	
	// Enforcing HTTPS connections
	// If this is turned on, then only https:// requests are allowed on the server
	// This value is turned off by default.
	// $config['https-limiter']=true;
	
	// IP blacklisting
	// This should be a comma-separated list of IP addresses that are blocked from accessing the site
	// By default this is not used.
	// $config['blacklist-limiter']='';
	
	// Limiting requests per minute
	// This sets how many requests an IP is allowed to make per minute on the server
	// If this number is exceeded then the IP will be blocked for an hour
	// Please note that it is recommended to use other anti-denial-of-service-attack methods, this alone is not good enough
	// By default this value is 0 and it is turned off
	// $config['request-limiter']=120;
	
	// Limiting server load
	// If this is set then server will block requests temporarily while the server load is too high
	// This setting sets the server load limit after which requests will be blocked
	// This is set to 0 and thus not used by default
	// $config['load-limiter']=80;

?>