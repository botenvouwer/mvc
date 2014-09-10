<?php

	class cmsview extends view{
		
		public $title = 'CMS';
		
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
		
		function getMenu(){
			
		}
		
		function getMenuHTML(){
			
		}
		
		function send(){
			
			$test = printRequest();
			$test2 = print_w(userHasRight($GLOBALS['nameSpace'], $GLOBALS['action'], $GLOBALS['subaction']));
			
			
			$this->html("
		  		$this->content <br>
		  		$_SESSION[user] <br>
		  		$test <br>
		  		$test2
			");
			
			parent::send();
			
		}
		
	}

?>