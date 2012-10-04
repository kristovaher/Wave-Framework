<?php

/**
 * MyProjectNameHere <http://www.example.com>
 * Example PHP Class
 *
 * It is possible to PHP classes dynamically through Wave Framework Factory. To load this 
 * class, you need to call $this->getObject('example') method within MVC classes that are
 * extended from WWW_Factory class.
 *
 * @package    Factory
 * @author     DeveloperNameHere <email@example.com>
 * @copyright  Copyright (c) 2012, ProjectOwnerNameHere
 * @license    Unrestricted
 * @tutorial   /doc/pages/guide_objects.htm
 * @since      1.0.0
 * @version    1.0.0
 */
 
class example {
	private $myMessage='works';
	public function test(){
		echo $this->myMessage;
	}
}

?>