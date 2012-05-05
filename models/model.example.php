<?php

/*
WWW Framework
MVC Model class

Minimal example model for demonstration purposes.

Author and support: Kristo Vaher - kristo@waher.net
License: This file can be copied, changed and re-published under another license without any restrictions
*/

// WWW_Factory is parent class for all MVC classes of WWW
class WWW_model_example extends WWW_Factory {

	// All the variables of this model should be stored here
	public $id=0;
	public $name='';
	
	// This is like __construct() but for Factory-created objects
	public function __initialize(){
		// Do something here
		return true;
	}
	
	// This is intended to load data from database
	// * id - Identifier of the object loaded
	public function load($id){
		// Actual database query should be built here
		$this->id=$id;
		$this->name='Lorem Ipsum #'.rand(1,1000); // This is used for simply testing cache
		return true;
	}
	
	// This returns all of the data of currently open object
	public function get(){
		// Data is returned as an array
		return array(
			'id' => $this->id,
			'name'=>$this->name
		);
	}

	// This function is intended to save data to database
	public function save(){
		// Actual database query should be built here
		return true;
	}

}
	
?>