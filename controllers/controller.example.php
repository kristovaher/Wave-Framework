<?php

/*
WWW Framework
MVC Controller class

Minimal example controller for demonstration purposes

Author and support: Kristo Vaher - kristo@waher.net
License: This file can be copied, changed and re-published under another license without any restrictions
*/

// WWW_Factory is parent class for all MVC classes of WWW
class WWW_controller_example extends WWW_Factory {
	
	// Simple example call (from API it would be called as 'example-get' command)
	// Please note that only public methods can be called through API, protected and private methods remain hidden
	public function get(){
		// New objects can be created through Factory easily
		$example=$this->getModel('example');
		// This 'loads' model with ID of 1. Note that the function call here can be anything you need, this is just used as an example
		$example->load(1);
		// Returning the result of controller call
		return $example->get();
	}
	
}
	
?>