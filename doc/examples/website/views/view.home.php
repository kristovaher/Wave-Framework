<?php

class WWW_view_home extends WWW_Factory {

	// View Controller calls this function as output for page content
	public function render($input){
	
		// Loading translations
		$translations=$this->getTranslations();
		// Loading sitemap
		$sitemap=$this->getSitemap();
		// Loading view data
		$view=$this->getState('view');
		?>
			<div id="header">
				<a href="<?=$sitemap['page/contact']['url']?>"><?=$sitemap['page/contact']['meta-title']?></a>
				<a href="<?=$sitemap['page/about']['url']?>"><?=$sitemap['page/about']['meta-title']?></a>
				<a href="<?=$sitemap['add']['url']?>"><?=$sitemap['add']['meta-title']?></a>
				<a href="<?=$sitemap['list']['url']?>"><?=$sitemap['list']['meta-title']?></a>
				<a href="<?=$sitemap['home']['url']?>"><?=$sitemap['home']['meta-title']?></a>
			</div>
			<div id="body">
				<p><?=$translations['welcome']?></p>
			</div>
		<?php
		
	}

}
	
?>