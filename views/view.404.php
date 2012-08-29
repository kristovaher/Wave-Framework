<?php

/**
 * Wave Framework <http://www.waveframework.com>
 * 404 View
 * 
 * This view is called by WWW_controller_view if WWW_controller_url could not find a matching 
 * view for current request. This can be used to customize page-specific page-not-found pages, 
 * however this view is not used for missing files and other static resources.
 * 
 * @package    Factory
 * @author     Kristo Vaher <kristo@waher.net>
 * @copyright  Copyright (c) 2012, Kristo Vaher
 * @license    Unrestricted
 * @tutorial   /doc/pages/guide_mvc.htm
 * @since      1.0.0
 * @version    3.1.3
 */

class WWW_view_404 extends WWW_Factory {

	/**
	 * View Controller calls this function as output for page content.
	 * 
	 * This method returns null by default because the API will load the 
	 * result from output buffer, if the API call echoes/prints any output. 
	 * It is recommended for View methods not to return any variable data.
	 *
	 * @param array [$input] input array from View Controller
	 * @return null
	 */
	public function render($input){
	
		// Note that this file is only delivered through Index Gateway Data Handler and not by File or Resource Handlers
		// This view is loaded when URL Controller is unable to find a proper view
		
		// This simply prints out a 404 message when no proper view was found
		?>
			<h1>404</h1>
		<?php
		
		// API Will load result data from output buffer
		return null;
		
	}

}
	
?>