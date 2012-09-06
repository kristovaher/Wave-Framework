<?php

/**
 * MyProjectNameHere <http://www.example.com>
 * Home View
 * 
 * This view is loaded when WWW_controller_view finds root or home page as the view file. Name 
 * of the 'home' view is defined as a default in WWW_State class. This home view example also 
 * shows how to use translations.
 * 
 * @package    Factory
 * @author     DeveloperNameHere <email@example.com>
 * @copyright  Copyright (c) 2012, ProjectOwnerNameHere
 * @license    Unrestricted
 * @tutorial   /doc/pages/guide_mvc.htm
 * @since      1.0.0
 * @version    1.0.0
 */

class WWW_view_home extends WWW_Factory {

	/**
	 * View Controller calls this function as output for page content.
	 * 
	 * This method returns null by default because the API will load the 
	 * result from output buffer, if the API call echoes/prints any output. 
	 * It is recommended for View methods not to return any variable data.
	 *
	 * @param array $input input array from View Controller
	 * @return null
	 */
	public function render($input){
		
		// Translations are stored in input variables and can be used within the view
		$translations=$this->getTranslations();
		
		?>
			<div style="text-align:center;padding:30px;">
				<!-- Simple translation is echoed to show how the translations can be used -->
				<h1 style="font:30px Tahoma; color:#3e445a;padding:30px;text-align:center;"><?=$translations['hello-world']?></h1>
				<!-- This shows how to dynamically load a resource -->
				<img width="160" height="160" src="resources/images/160x160&logo.png"/>
			</div>
		<?php
		
		// API Will load result data from output buffer
		return null;
		
	}

}
	
?>