<?php

class WWW_view_list extends WWW_Factory {

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
				<?php
					$movies=$this->api('movies-all');
					if(!isset($movies['error'])){
						echo '<table id="movies">';
						echo '<tr>';
							echo '<th>'.$translations['title'].'</th>';
							echo '<th>'.$translations['year'].'</th>';
						echo '</tr>';
						foreach($movies as $m){
							echo '<tr>';
								echo '<td><a href="'.str_replace(':0:',$m['id'],$sitemap['movie']['url']).'">'.$m['title'].'</a></td>';
								echo '<td>'.$m['year'].'</td>';
							echo '</tr>';
						}
						echo '</table>';
					} else {
						echo '<p>'.$translations['cannot-find-movies'].'</p>';
					}
					// If this is set, then movie was added
					if(isset($input['ok-notification'])){
						echo '<p><b>'.$translations['movie-added'].'</b></p>';
					}
				?>		
			</div>
		<?php
		
	}

}
	
?>