<?php

/*
WWW - PHP micro-framework
General configuration

All configuration settings in this file are loaded into WWW_State where certain variables, 
such as error-reporting, can turn off or on PHP errors. This configuration script is also 
used by scripts under /tools/ folder.

Author and support: Kristo Vaher - kristo@waher.net
*/

// Database access credentials
// If database name is not set then database is not connected to by API
$config['database-type']='mysql';
$config['database-host']='localhost';
$config['database-name']='';
$config['database-username']='';
$config['database-password']='';

// Developer authentication, 
// If this is turned on then HTTP authentication is required before Index and API gateways work
// These credentials are always used for developer tools under /tools/ directory though (with the exception of Adminer)
$config['http-authentication']=false;
$config['http-authentication-username']='developer';
$config['http-authentication-password']='hellowww';

// Comma separated list of languages
// These languages here are used for language detection as the first URL node, like www.example.com/[language-code]/page
$config['languages']=array('en');

// This can set the PHP error-reporting state
$config['error-reporting']=E_ALL;

// This turns on Index gateway performance logging
// 'all' means that all data is logged, other values should be comma-separated values of logged array
// Set this to empty string or false to turn off logging entirely
$config['logger']='all';
	
?>