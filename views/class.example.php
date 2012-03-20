<?php

/*
WWW - PHP micro-framework
MVC View class

Minimal example view for demonstration purposes.

Author and support: Kristo Vaher - kristo@waher.net
*/

// WWW_Factory is parent class for all MVC classes of WWW
class WWW_view_example extends WWW_Factory {

	// WWW_controller_url calls this function as output for page content
	public function render($input){
	
		// This is a simple example view with printed content
		echo 'This is an example view! Data sent to this view is as follows:';
		
		// This shows all the input variables sent to this view
		// If you just installed WWW Framework then go to URL /example/ to see the output
		echo '<pre>';
			print_r($input);
		echo '</pre>';		
		
		// It is always recommended to return a value from a function
		return true;
	}

}
	
?>