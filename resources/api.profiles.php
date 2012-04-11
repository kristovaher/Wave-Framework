<?php

/*
WWW Framework
API keys file

This file stores an array of API profile names and their secret keys. When API is loaded with 
any other profile name than what is defined public, then the command has to be validated with 
the secret key from this file and the requesting user agent has to calculate hash of that command 
with that secret key prior to sending it to API. This file also includes other options related 
to API profile settings, such as timeouts and IP restrictions.

* Key of the $apiProfiles array is the profile name
* disabled - A true or false setting whether this profile is disabled
* ip - Either aterisk or comma separated list of IP addresses, this limits what IP's can use this profile
* secret-key - Secret key of the API profile, should be a 32 character string for best security. If not defined, then hash validation is not used.
* token-timeout - Time (in seconds) how long a generated token is valid if left unused. If not set, token timeout is infinite.
* timestamp-timeout - This sets how many seconds from the moment request was made this request is valid (this is used to help protect against replay attacks). If not set, then timestamp validation is not used.

Author and support: Kristo Vaher - kristo@waher.net
*/

// All keys are stored in an array
$apiProfiles=array();

$apiProfiles['custom-profile']=array('disabled'=>false,'ip'=>'*','timestamp-timeout'=>120,'token-timeout'=>600,'secret-key'=>'my-secret-key'); // API profile has multiple settings set
	
?>