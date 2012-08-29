<?php

/**
 * Wave Framework <http://www.waveframework.com>
 * Example Controller
 *
 * It is recommended to extend View classes from WWW_Factory in order to 
 * provide various useful functions and API access for the view.
 *
 * @package    Factory
 * @author     Kristo Vaher <kristo@waher.net>
 * @copyright  Copyright (c) 2012, Kristo Vaher
 * @license    Unrestricted
 * @tutorial   /doc/pages/guide_mvc.htm
 * @since      1.0.0
 * @version    3.1.3
 */

class WWW_controller_example extends WWW_Factory {
	
	/**
	 * Simple example call to get data
	 *
	 * Please note that only public methods can be called through API, protected 
	 * and private methods remain hidden. This method would be accessible over API 
	 * with 'www-command=example-get' call.
	 *
	 * @param array [$input] input data sent to controller
	 * @input [key] This key is one of the accepted input values
	 * @return array
	 * @output [key] This is an output value that might exist in the output array
	 */
	public function get($input){
		// New objects can be created through Factory easily
		$example=$this->getModel('example');
		// This 'loads' model with ID of 1. Note that the function call here can be anything you need, this is just used as an example
		$example->load(1);
		// Getting the data from the model
		$data=$example->get();
		// Returning the result of controller call
		return $this->resultTrue('Data returned!',$data);
	}
	
}
	
?>