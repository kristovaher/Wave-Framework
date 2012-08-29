<?php

/**
 * Wave Framework <http://www.waveframework.com>
 * Example View
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

class WWW_view_example extends WWW_Factory {

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
		
		?>
			<div style="padding:30px;width:600px;margin-left:auto;margin-right:auto;">
				<h1 style="font:30px Tahoma; color:##465a9e;padding:30px;">An example API response:</h1>
				<pre>
					<!-- This shows an example API call response -->
					<?=print_r($this->api('example-get'),true)?>
				</pre>
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
				<h1 style="font:30px Tahoma; color:##465a9e;padding:30px;">State:</h1>
				<pre>
					<!-- This shows an example API call response -->
					<?=print_r($this->getState(),true)?>
				</pre>
			</div>
		<?php
		
		// API Will load result data from output buffer
		return null;
		
	}

}
	
?>