<?php

/*
WWW Framework
MVC View class

Minimal example view for demonstration purposes.

Author and support: Kristo Vaher - kristo@waher.net
License: This file can be copied, changed and re-published under another license without any restrictions
*/

// WWW_Factory is parent class for all MVC classes of WWW
class WWW_view_example extends WWW_Factory {

	// WWW_controller_url calls this function as output for page content
	public function render($input){
		
		// This shows how to use state messenger
		$messengerData=$this->getStateMessengerData('aaabbb',false);
		if(!$messengerData){
			$this->stateMessenger('aaabbb');
			$messengerName='Thomas Moore #'.rand(1,1000);
			$this->setStateMessengerData('name',$messengerName);
		} else {
			$messengerName=$messengerData['name'];
		}
		
		?>
			<div style="padding:30px;width:600px;margin-left:auto;margin-right:auto;">
				<h1 style="font:30px Tahoma; color:##465a9e;padding:30px;">Input data sent to view:</h1>
				<pre>
					<!-- $input is a variable sent to view that contains all the data that is useful when generating views -->
					<?=print_r($input,true)?>
				</pre>
				<h1 style="font:30px Tahoma; color:##465a9e;padding:30px;">View state:</h1>
				<pre>
					<!-- $input is a variable sent to view that contains all the data that is useful when generating views -->
					<?=print_r($this->getState('view'),true)?>
				</pre>
				<h1 style="font:30px Tahoma; color:##465a9e;padding:30px;">Sitemap:</h1>
				<pre>
					<!-- $input is a variable sent to view that contains all the data that is useful when generating views -->
					<?=print_r($this->getSitemap(),true)?>
				</pre>
				<h1 style="font:30px Tahoma; color:##465a9e;padding:30px;">Translations:</h1>
				<pre>
					<!-- $input is a variable sent to view that contains all the data that is useful when generating views -->
					<?=print_r($this->getTranslations(),true)?>
				</pre>
				<h1 style="font:30px Tahoma; color:##465a9e;padding:30px;">An example API response:</h1>
				<pre>
					<!-- This shows an example API call response -->
					<?=print_r($this->api('example-get'),true)?>
				</pre>
				<h1 style="font:30px Tahoma; color:##465a9e;padding:30px;">State messenger data:</h1>
				<pre>
					<!-- This shows data stored in state messenger -->
					<?=print_r($messengerData,true)?>
				</pre>
				<h1 style="font:30px Tahoma; color:##465a9e;padding:30px;">State:</h1>
				<pre>
					<!-- This shows an example API call response -->
					<?=print_r($this->getState(),true)?>
				</pre>
			</div>
		<?php
		
		// It is always recommended to return a value from a function
		return true;
		
	}

}
	
?>