<?php

error_reporting(E_ALL);

// $apiProfile='custom-profile';
// $apiKey='my-secret-key';
// $encryptionKey='my-secret-key';

// Requiring wrapper and creating the object
require('class.www-wrapper.php');
$www=new WWW_Wrapper('http://www.waher.net/wdev/www.api');

		echo '<h1>Public request</h1>';
		$www->setCommand('example-get');
		$www->setFile('uploadfile','connector.php');
		$result=$www->sendRequest();
		echo '<pre>';
		print_r($www->returnLog());
		print_r($result);
		echo '</pre>';
		$www->clearLog();
		
	echo '<hr/>';

		echo '<h1>Creating session key</h1>';
		$www->setAuthentication('custom-profile','my-secret-key');
		$www->setCommand('www-create-session');
		$result=$www->sendRequest();
		echo '<pre>';
		print_r($www->returnLog());
		print_r($result);
		echo '</pre>';
		$token=$result['www-token'];
		$www->clearLog();
		$www->clearAuthentication();
			
	echo '<hr/>';

		echo '<h1>Creating session key</h1>';
		$www->setAuthentication('custom-profile','my-secret-key');
		$www->setToken($token);
		$www->setCommand('example-get');
		$result=$www->sendRequest();
		echo '<pre>';
		print_r($www->returnLog());
		print_r($result);
		echo '</pre>';
		$www->clearLog();
		$www->clearAuthentication();
			
	echo '<hr/>';



?>