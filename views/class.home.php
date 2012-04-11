<?php

/*
WWW Framework
MVC View class

This view is loaded when WWW_controller_view finds root or home page as the view file. Name 
of the 'home' view is defined as a default in WWW_State class. This home view example also 
shows how to use translations.

Author and support: Kristo Vaher - kristo@waher.net
*/

// WWW_Factory is parent class for all MVC classes of WWW
class WWW_view_home extends WWW_Factory {

	// WWW_controller_url calls this function as output for page content
	public function render($input){
		
		// Translations are stored in input variables and can be used within the view
		$translations=$input['www-view-data']['translations'];
		
		?>
			<div style="text-align:center;padding:30px;">
				<!-- Simple translation is echoed to show how the translations can be used -->
				<h1 style="font:30px Tahoma; color:##465a9e;padding:30px;text-align:center;"><?=$translations['hello-world']?></h1>
				<!-- This shows how to dynamically load a resource -->
				<img width="160" height="160" src="<?=$input['www-view-data']['web-root']?>resources/images/160x160&logo.png"/>
			</div>
		<?php
		
		// It is always recommended to return a value from a function
		return true;
	}

}
	
?>