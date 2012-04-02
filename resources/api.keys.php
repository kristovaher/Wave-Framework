<?php

/*
WWW - PHP micro-framework
API keys file

This file stores an array of API profile names and their secret keys. When API is loaded with 
any other profile name than what is defined public, then the command has to be validated with 
the secret key from this file and the requesting client has to calculate hash of that command 
with that secret key prior to sending it to API. API hash is calculated by creating a SHA-1 
hash (with PHP's sha1() or its equivalent) from a string that is built by appending two strings 
together, an API command string and key-sorted serialized (with PHP's serialize() or its 
equivalent) input data array. Please note that when submitting to API, then things such as 
cookies and GET and POST variables are all considered parts of input data.

Author and support: Kristo Vaher - kristo@waher.net
*/

// All keys are stored in an array
$apiProfiles=array();

$apiProfiles['custom-profile']=array('disabled'=>0,'ip'=>'*','secret-key'=>'my-secret-key','encryption-key'=>'my-secret-encryption-key','token-timeout'=>300,'timestamp-timeout'=>10); // API profile has multiple settings set
	
?>