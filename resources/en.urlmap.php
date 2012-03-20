<?php

/*
WWW - PHP micro-framework
URL Map file

URL Map's are used by WWW_controller_url to solve what view should be loaded based on clients 
request. WWW_controller_url detects both languages and URL and unsolved URL nodes from the 
request and matches them against this file, based on language code that it detects in the 
request. If it finds a match here then it returns that view. Note that it can also find a 
partial match, such as /example/test/test2/ will return the same view as /example/test/ and 
keep the /test2/ node as unsolved URL node that you can do anything you wish with. This can 
be useful if you store something else in that part of URL, such as product code.

Author and support: Kristo Vaher - kristo@waher.net
*/

$urlMap['home']='home';  // URL is the element key and value is the view assigned to be loaded
$urlMap['example']='example';  // URL is the element key and value is the view assigned to be loaded
$urlMap['example']='example/*';  // Aterisk allows 'unsolved URL's in this mode to be transferred
$urlMap['example/test']='example/alt'; // It is possible to have two different URL's use the same view with a 'slash' making it unique, 'alt' will be stored as 'subview'
	
?>