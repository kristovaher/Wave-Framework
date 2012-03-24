<?php

/*
WWW - PHP micro-framework
General configuration

All configuration settings in this file are loaded into WWW_State where certain variables, 
such as error-reporting, can turn off or on PHP errors. This configuration script is also 
used by scripts under /tools/ folder. Note that most settings in this file are commented
out due to being defined by default in the system. They are here only for reference.

Author and support: Kristo Vaher - kristo@waher.net
*/

// DEVELOPER AUTHENTICATION

	// If this is turned on then HTTP authentication is required before Index and API gateways work
	// These credentials are always used for developer tools under /tools/ directory though (with the exception of Adminer)
	$config['http-authentication']=false;
	$config['http-authentication-username']='developer';
	$config['http-authentication-password']='hellowww';
	
// DEBUGGING AND LOGGING

	// This turns on Index gateway performance logging
	// 'all' means that all data is logged, other values should be comma-separated values of logged array
	// Set this to empty string or false to turn off logging entirely
	$config['logger']='all';

	// This can set the PHP error-reporting state
	// This value is 0 by default
	// $config['error-reporting']=0;

// DATABASE ACCESS

	// If database name is not set then database is not connected to by API
	// Database is not used by default
	// $config['database-type']='mysql';
	// $config['database-host']='localhost';
	// $config['database-name']='';
	// $config['database-username']='';
	// $config['database-password']='';

// SERVER AND CONTENT

	// Host name
	// This value is the host name of the server
	// If left undefined then server will solve this setting by itself
	// $config['http-host']='www.example.com';

	// Comma separated list of languages
	// These languages here are used for language detection as the first URL node, like http://www.example.com/[language-code]/page
	// Default value is an array('en')
	// $config['languages']=array('en');
	
	// Default language
	// This is considered the default language that URL controller will consider as the main language if language is not detected in URL
	// If not defined, this value will be assigned as the first value in 'languages' array setting
	// $config['language']='en';
	
	// Setting timezone
	// This is detected by default based on server value if not set here
	// $config['timezone']='Europe/London';

	// 404 view name
	// This is what Index gateway considers as the 404 'view' in case URL does not exist
	// This view must exist as a /views/class.{home-view}.php file
	// Default value is '404'
	// $config['404-view']='404';
	
	// Home view name
	// This is what Index gateway considers as the default 'view' in case URL is set to root
	// This view must exist as a /views/class.{home-view}.php file
	// Default value is 'home'
	// $config['home-view']='home';
	
	// Web root
	// This value is the relative URL node. If server is installed to http://www.example.com/mypage/ then this value is '/mypage/'
	// If this value is not defined then State detects this automatically
	// $config['web-root']='';
	
	// System root
	// This value is the absolute directory address on the web server for this installation
	// If this value is not defined then State detects this automatically
	// $config['system-root']='';
	
	// Enforcing slash at the end of URL
	// If this setting is turned on, then URL controller will redirect requests from URL's that don't end with a slash to ones that do
	// Example is redirecting http://www.example.com/mypage to http://www.example.com/mypage/
	// This value is 'true' by default
	// $config['enforce-url-end-slash']=true;
	
	// Enforcing first language URL node
	// If this value is set to true, then first language URL node must be defined in URL requests
	// URL controller will automatically redirect all URL's that don't include language nodes to ones that do
	// For example, http://www.example.com/contact/ would be then redirected to http://www.example.com/en/contact/
	// This value is 'true' by default
	// $config['enforce-first-language-url']=true;
	
	// Robots
	// This sets the default robots setting in the system
	// By default the value is 'noindex,nocache,nofollow,noarchive,noimageindex,nosnippet', but this can be overwritten by Sitemap 'robots' setting per URL
	// $config['robots']='noindex,nocache,nofollow,noarchive,noimageindex,nosnippet';
	
// OPTIMIZATIONS

	// Output compression
	// This sets what type of output compression is used, options include 'default' and 'gzip' or false
	// Note that whether the output will be actually compressed based on clients request depends on whether client states that compression type in http-accept-encoding request header
	// This value is 'deflate' by default'
	// $config['output-compression']='deflate';
	
	// Index URL controller cache timeout
	// This is the cache duration for URL solving through URL controller
	// By default this value is set to 30 seconds
	// $config['index-url-cache-timeout']=30;
	
	// Index View controller cache timeout
	// This is the cache duration for all returned views
	// By default this value is set to 30 seconds
	// $config['index-view-cache-timeout']=30;
	
	// Resource cache timeout
	// This sets how long static files are considered to be 'in cache'
	// This affects returned max lifetime settings and internal cache duration (though that resets itself if original file is modified)
	// By default this value is set to 31536000, which is one year in seconds.
	// $config['resource-cache-timeout']=31536000;
	
// DYNAMIC IMAGES
	
	// Dynamic image loading
	// This flag sets if dynamic image loading is allowed
	// Dynamic image loading allows image resizing and basic editing over HTTP dynamically, such as generating thumbnails
	// This allows to return a 60x60 thumbnail of logo for example http://www.example.com/resources/images/60x60&logo.png
	// This is enabled by default
	// $config['dynamic-image-loading']=true;
	
	// Dynamic image max size
	// This sets the max width or height a dynamically generated image can be before returning 404
	// This is a security setting and is intended to restrict server from generating too large images
	// By default this value is set to 1000 pixels
	// $config['dynamic-max-size']=1000;
	
	// Dynamic size whitelist
	// This is an array of image resolutions that are allowed during image generation
	// RGB values should be stored in array like '120,60,14', '255,255,255' and so on
	// By default this value is set empty and all resolutions are allowed (as long as they are under dynamic-max-size setting)
	// $config['dynamic-size-whitelist']=array();
	
	// Dynamic color whitelist
	// This is an array of background colors that are allowed during image generation
	// RGB values should be stored in array like '120,60,14', '255,255,255' and so on
	// By default this value is set empty and all colors are allowed
	// $config['dynamic-color-whitelist']=array();
	
	// Dynamic quality whitelist
	// This is an array of quality percentages that are allowed during image generation
	// Values should be stored in array like '50', '30' and so on
	// By default this value is set empty and all quality settings are allowed
	// $config['dynamic-quality-whitelist']=array();
	
	// Dynamic image position whitelist
	// This is an array of image positions that are allowed during image generation
	// Values should be stored in array like 'top-left', '30-50' and so on
	// By default this value is set empty and all positions are allowed
	// $config['dynamic-position-whitelist']=array();
	
	// Dynamic image filters
	// This sets if image filters are allowed for dynamic image loading, in other words the filter() parameters
	// By default this value is set to true
	// $config['dynamic-image-filters']=true;
	
	// Dynamic filter whitelist
	// This is an array of image filters that are allowed during image generation
	// Values should be stored in array like 'grayscale@100', 'colorize@40,120,120,120' and so on (like they appear within filter() call)
	// Note that when no alpha is used in filter call, then it should still be written in this whitelist as 'grayscale@100' instead of just 'grayscale'
	// By default this value is set empty and all filters are allowed
	// $config['dynamic-filter-whitelist']=array();
	
	// 404 image placeholder
	// If this is set to true, then system returns /resources/placeholder.jpg file when the actual file cannot be found
	// This is turned on by default
	// $config['404-image-placeholder']=true;
	
// API
	
	// This sets the API profile that is used
	// All profile names that are not 'public' require an API key set in /resources/api.keys.php
	// $config['api-profile']='public';
	
	// This sets what type of input serializer is used for input validation
	// Default value is 'json' but 'serialize' can also be used
	// $config['api-serializer']='json';
	
	// This states how long an API profile token is valid
	// This value is 0 by default. If this is 0 then token based validation is not allowed
	// If the token has not been accessed for longer than the amount of seconds stored in this variable, then new token needs to be generated
	// $config['api-token-timeout']=0;
	
// LIMITER

	// This turns on HTTP request limiter
	// If this is turned off then no limiter is checked at all and Limiter object is not created
	// By default this setting is false
	// $config['limiter']=false;
	
	// HTTP Authentication
	// If this is turned on then all HTTP requests have to be authenticated with HTTP username and password
	// Username and password are the same as the ones in 'DEVELOPER AUTHENTICATION' section
	// By default this is false and is turned off
	// $config['http-authentication-limiter']=false;
	
	// Enforcing HTTPS connections
	// If this is turned on, then only https:// requests are allowed on the server
	// This value is turned off by default.
	// $config['https-limiter']=false;
	
	// IP blacklisting
	// This should be a comma-separated list of IP addresses that are blocked from accessing the site
	// By default this is not used.
	// $config['blacklist-limiter']='';
	
	// Limiting requests per minute
	// This sets how many requests an IP is allowed to make per minute on the server
	// If this number is exceeded then the IP will be blocked for an hour
	// Please note that it is recommended to use other anti-denial-of-service-attack methods, this alone is not good enough
	// By default this value is 0 and it is turned off
	// $config['request-limiter']=0;
	
	// Limiting server load
	// If this is set then server will block requests temporarily while the server load is too high
	// This setting sets the server load limit after which requests will be blocked
	// This is set to 0 and thus not used by default
	// $config['load-limiter']=0;
	
// SESSIONS

	// Disabling automatic session start
	// If this is set then State will not automatically initialize sessions
	// This is turned off by default
	// $config['disable-session-start']=false;
	
	// Session cookie name
	// If this is not set then PHP uses default session cookie name
	// $config['session-cookie']='PHPSESSID';

?>