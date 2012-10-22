<?php

/**
 * MyProjectNameHere <http://www.example.com>
 * Controller Class
 *
 * It is recommended to extend Controller classes from WWW_Factory in order to 
 * provide various useful functions and API access for the Controller.
 *
 * @package    Factory
 * @author     DeveloperNameHere <email@example.com>
 * @copyright  Copyright (c) 2012, ProjectOwnerNameHere
 * @license    Unrestricted
 * @tutorial   /doc/pages/guide_mvc.htm
 * @since      1.0.0
 * @version    1.0.0
 */

class WWW_controller_example extends WWW_Factory {
	
	/**
	 * Simple example call to get data
	 *
	 * Please note that only public methods can be called through API, protected 
	 * and private methods remain hidden. This method would be accessible over API 
	 * with 'www-command=example-get' call.
	 *
	 * @param array $input input data sent to controller
	 * @input [key] This key is one of the accepted input values
	 * @return array
	 * @output [key] This is an output value that might exist in the output array
	 * @response [500] Data returned
	 */
	public function get($configuration){
	
		// This is only set for demonstration purposes, remove it when using this class as template
		$configuration['id']=1;
	
		// ID needs to be set
		if(isset($configuration['id'])){
			// Loading the model and data to the model
			$data=$this->getModel('example');
			if($data->load($configuration['id'])){
				// Returning an array representation of the model data
				return $this->resultTrue('Entry found',$data->get());
			} else {
				// Action failed because entry was not found
				return $this->resultFalse('Entry not found');
			}
		} else {
			// Action failed because incorrect request was made to the controller
			return $this->resultError('ID not defined');
		}
		
	}
	
	/**
	 * Simple example call to get multiple database rows
	 *
	 * @param array $input input data sent to controller
	 * @input [key] This key is one of the accepted input values
	 * @return array
	 * @output [key] This is an output value that might exist in the output array
	 * @response [500] Data returned
	 */
	public function all($configuration){
	
		// Loading the model and sending input data to the request
		$data=$this->getModel('example');
		$data=$data->all($configuration);
		
		// This returns empty array if none were found
		if($data){
			return $this->resultTrue('Request complete',$data);
		} else {
			return $this->resultFalse('Search failed');
		}
		
	}
	
	/**
	 * Simple example call to add rows to database
	 *
	 * @param array $input input data sent to controller
	 * @input [key] This key is one of the accepted input values
	 * @return array
	 * @output [key] This is an output value that might exist in the output array
	 * @response [500] Data returned
	 */
	public function add($input){
			
		// This flag checks if error has been encountered during form validation
		$errorsEncountered=array();
		$errorFields=array();
		
		// Validating input
		if(isset($input['name']) && trim($input['name'])!=''){
			$input['name']=trim($input['name']);
		} else {
			$errorFields['name']=true;
			$errorsEncountered[]='name-incorrect';
		}
		
		// Data is only added if no errors were encountered
		if(empty($errorsEncountered)){
			// Getting model and setting the parameters
			$data=$this->getModel('example');
			$data->name=$input['name'];
			// Attempting to save
			if($data->save()){
				return $this->resultTrue('Entry added');
			} else {
				return $this->resultError('Failed to add entry');
			}
		} else {
			return $this->resultFalse('Input data incorrect');
		}
		
	}

	/**
	 * Simple example call to edit rows to database
	 *
	 * @param array $input input data sent to controller
	 * @input [key] This key is one of the accepted input values
	 * @return array
	 * @output [key] This is an output value that might exist in the output array
	 * @response [500] Data returned
	 */
	public function edit($input){
	
		// ID has to be set when editing
		if(isset($input['id'])){
			
			// This flag checks if error has been encountered during form validation
			$errorsEncountered=array();
			$errorFields=array();
			
			// Validating input
			if(isset($input['name']) && trim($input['name'])!=''){
				$input['name']=trim($input['name']);
			} else {
				$errorFields['name']=true;
				$errorsEncountered[]='name-incorrect';
			}
			
			// Data is only edited if no errors were encountered
			if(empty($errorsEncountered)){
				// Getting model
				$data=$this->getModel('example');
				// Data loading has to work based on the provided ID
				if($data->load($input['id'])){
					// Setting the changed input parameters
					$data->name=$input['name'];
					// Attempting to save
					if($data->save()){
						return $this->resultTrue('Entry edited');
					} else {
						return $this->resultError('Failed to edit entry');
					}
				} else {
					return $this->resultFalse('Entry ID not found');
				}
			} else {
				return $this->resultFalse('Input data incorrect');
			}
				
		} else {
			return $this->resultFalse('Entry ID is missing');
		}
		
	}

	/**
	 * Simple example call to delete a row from database
	 *
	 * @param array $input input data sent to controller
	 * @input [key] This key is one of the accepted input values
	 * @return array
	 * @output [key] This is an output value that might exist in the output array
	 * @response [500] Data returned
	 */
	public function delete($input){
	
		// ID has to be set when deleting
		if(isset($input['id'])){
			// Getting model
			$data=$this->getModel('example');
			// Attempting to delete
			if($data->delete($input['id'])){
				return $this->resultTrue('Entry deleted');
			} else {
				return $this->resultError('Failed to delete entry');
			}
		} else {
			return $this->resultFalse('Entry ID is missing');
		}
		
	}
	
}
	
?>