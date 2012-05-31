<?php

/*
Wave Framework
MVC View class

This view is called by WWW_controller_view if WWW_controller_url could not find a matching 
view for current request. This can be used to customize page-specific page-not-found pages, 
however this view is not used for missing files and other static resources.

Author and support: Kristo Vaher - kristo@waher.net
License: This file can be copied, changed and re-published under another license without any restrictions
*/

// WWW_Factory is parent class for all MVC classes of Wave Framework
class WWW_view_404 extends WWW_Factory {

	// WWW_controller_url calls this function as output for page content
	public function render($input){
	
		// Note that this file is only delivered through Index gateway Data handler and not by File or Resource handlers
		// This view is loaded when URL Controller is unable to find a proper view
		
		// This simply prints out a 404 message when no proper view was found
		?>
			<h1>404</h1>
		<?php
		
	}

}
	
?>