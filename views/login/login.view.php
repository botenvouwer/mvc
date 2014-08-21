<?php

	class loginview extends view{
		
		public $content = '';
		
		function __construct(){
			
			$this->addCSS('desktop.css');
			$this->addCSS('mobile.css');
			$this->addCSS('tablet.css');
			$this->addJS('library/jquery.js');
			$this->addJS('library/jquery.ui.js');
			$this->addJS('library/jquery.royalslider.min.js');
			$this->addJS('library/jquery.ui.touch-punch.min.js');
			$this->addJS('library/jquery.cookie.js');
			$this->addJS('library/MicoBoatWebapp.js');
			
		}
		
		function send(){
			
			$this->html("
		  		<div id='main'>
					<div id='login'>
						$this->content
					</div>
				</div>
			");
			
			parent::send();
			
		}
		
	}

?>