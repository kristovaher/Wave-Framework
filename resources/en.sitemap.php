<?php

/*
WWW Framework
Sitemap file

Sitemaps are used by WWW_controller_url to solve what view should be loaded based on user agents 
request. WWW_controller_url detects both languages and URL and unsolved URL nodes from the request 
and matches them against this file, based on language code that it detects in the request. If it 
finds a match here then it returns that view. Note that it can also find a partial match, such 
as /example/test/test2/ will return the same view as /example/test/ and keep the /test2/ node as 
unsolved URL node, if the setting for URL has it enabled. This can be useful if you store 
something else in that part of URL, such as product code.

* view - View file to be loaded from /views/class.{view}.php
* meta-title - View controller appends this to Meta title of the page
* meta-description - Meta description string loaded for this view
* meta-keywords - List of meta keywords loaded for this view
* unsolved-url-nodes - True or false value regarding whether this module is matched partly without throwing 404 errors
* subview - This allows to categorize the same view file under different categories
* robots - This allows to overwrite default robots setting from configuration
* temporary-redirect - This tells URL controller to redirect requests made to this URL
* permanent-redirect - This tells URL controller to permanently redirect requests made to this URL
* hidden - True or false value about whether this URL will be hidden from sitemap.xml generation
* cache-timeout - Time in seconds about how long cache exists for requests made to this URL, this overwrites default configuration setting
* usergroups - If this value is not false, then URL controller checks for user credentials. Note that the checking method is not turned on by default in URL controller
* view-controller - If this is not defined then /controllers/class.view.php will be used to return the view. In here you can assign a different view for class.{name}.php.

Author and support: Kristo Vaher - kristo@waher.net
*/

$siteMap['home']=array('view'=>'home','meta-title'=>'Home!','cache-timeout'=>30);  // URL is the element key and view is the view assigned to be loaded
$siteMap['example']=array('view'=>'example','unsolved-url-nodes'=>true);  // If unsolved URL nodes are enabled, then 404 is not thrown when URL has further nodes
$siteMap['example/subview']=array('view'=>'example','subview'=>'alt'); // It is possible to have two different URL's use the same view with a 'slash' making it unique, 'alt' will be stored as 'subview'
$siteMap['example/robots']=array('view'=>'example','robots'=>''); // This allows to overwrite the default robots setting
$siteMap['example/redirect']=array('view'=>'example','temporary-redirect'=>'http://www.google.com'); // It is also possible to redirect URL's
$siteMap['example/hide']=array('view'=>'example','hidden'=>true,'cache-timeout'=>0); // This means that the URL is hidden from sitemap as well as that cache is not used for this view
$siteMap['example/meta']=array('view'=>'example','meta-title'=>'My custom title!','meta-description'=>'Meta description here','meta-keywords'=>'www,framework'); // Meta information can also be listed for page
	
?>