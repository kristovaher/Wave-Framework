<?php

/*
WWW - PHP micro-framework
Sitemap file

Sitemaps are used by WWW_controller_url to solve what view should be loaded based on clients 
request. WWW_controller_url detects both languages and URL and unsolved URL nodes from the 
request and matches them against this file, based on language code that it detects in the 
request. If it finds a match here then it returns that view. Note that it can also find a 
partial match, such as /example/test/test2/ will return the same view as /example/test/ and 
keep the /test2/ node as unsolved URL node, if the setting for URL has it enabled. This can 
be useful if you store something else in that part of URL, such as product code.

Author and support: Kristo Vaher - kristo@waher.net
*/

$siteMap['home']=array('view'=>'home','meta-title'=>'Home!');  // URL is the element key and view is the view assigned to be loaded
$siteMap['example']=array('view'=>'example','unsolved-url-nodes'=>true);  // If unsolved URL nodes are enabled, then 404 is not thrown when URL has further nodes
$siteMap['example/subview']=array('view'=>'example','subview'=>'alt'); // It is possible to have two different URL's use the same view with a 'slash' making it unique, 'alt' will be stored as 'subview'
$siteMap['example/robots']=array('view'=>'example','robots'=>''); // This allows to overwrite the default robots setting
$siteMap['example/redirect']=array('view'=>'example','temporary-redirect'=>'http://www.google.com'); // It is also possible to redirect URL's
$siteMap['example/hide']=array('view'=>'example','hidden'=>true); // This means that the URL is hidden from sitemap
$siteMap['example/meta']=array('view'=>'example','meta-title'=>'My custom title!','meta-description'=>'Meta description here','meta-keywords'=>'www,framework'); // Meta information can also be listed for page
	
?>