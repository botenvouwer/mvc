<?php

	class blogview extends view{
		
		private $leftComlumn = '';
		private $rightComlumn = '';
		public $actueel = true;
		public $archief = true;
		public $twitterfeed = true;
		public $twitterfeed2 = false;
		public $poll = true;
		public $title = 'Ons Urker Land';
		
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
		
		function addSidebar($title, $content, $left = true, $id = false){
			
			$id = ($id ? " id='$id'" : '');
			
			$sidebar = "
				<div$id class='sidebar'>
  					<header>
  						<h2>$title</h2>
  					</header>
  					<div class='sidebarbox'>
	  					$content
  					</div>
  				</div>
			";
			
			if($left){
				$this->leftComlumn .= $sidebar;
			}
			else{
				$this->rightComlumn .= $sidebar;
			}
			
		}
		
		private function addSidebarInternal($array){
			$this->addSidebar($array[0], $array[1], $array[2], $array[3]);
		}
		
		function setDefaultSidebars(){
			
			if($this->actueel){
				$this->addSidebarInternal($this->actueelSidebar());
			}
			
			if($this->archief){
				$this->addSidebarInternal($this->archiefSidebar());
			}
			
			if($this->twitterfeed){
				$this->addSidebarInternal($this->twitterfeed());
			}
			
			if($this->twitterfeed2){
				$this->addSidebarInternal($this->twitterfeed2());
			}
			
			if($this->poll){
				$this->addSidebarInternal($this->poll());
			}
			
		}
		
		function actueelSidebar(){
			return array(
				'Actueel',
				"<ul> <li> <a>Lorem ipsum dolor sit amet</a> </li> <li> <a>Lorem ipsum dolor sit amet</a> </li> <li> <a>Lorem ipsum dolor sit amet</a> </li> </ul>",
				true,
				'actueelSidebar'
			);
		}
		
		function archiefSidebar(){
			return array(
				'Archief',
				"<ul class='ul'> <li> <a>Augustus 2014</a> </li> <li> <a>juli 2014</a> </li> <li> <a>juni 2014</a> </li> <li> <a>mei 2014</a> </li> <li> <a>april 2014</a> </li> <li> <a>maart 2014</a> </li> <li> <a>meer</a> </li> </ul>",
				true,
				'archiefSidebar'
			);
		}
		
		function twitterfeed(){
			return array(
				'Twitter',
				"<div class='tweet'> <header><a>@twitteraar - jul 22</a></header> <article>Nullam id blandit eros. Nulla a tortor porta, dignissim ante sit amet, venenatis mauris.</article> </div> <div class='tweet'> <header><a>@twitteraar - jul 22</a></header> <article>Nullam id blandit eros. Nulla a tortor porta, dignissim ante sit amet, venenatis mauris.</article> </div> <div class='tweet'> <header><a>@twitteraar - jul 22</a></header> <article>Nullam id blandit eros. Nulla a tortor porta, dignissim ante sit amet, venenatis mauris.</article> </div> <div class='tweet'> <header><a>@twitteraar - jul 22</a></header> <article>Nullam id blandit eros. Nulla a tortor porta, dignissim ante sit amet, venenatis mauris.</article> </div>",
				false,
				'tweetbox'
			);
		}
		
		function twitterfeed2(){
			return array(
				'Twitter',
				"<div class='tweet'> <header><a>@twitteraar - jul 22</a></header> <article>Nullam id blandit eros. Nulla a tortor porta, dignissim ante sit amet, venenatis mauris.</article> </div> <div class='tweet'> <header><a>@twitteraar - jul 22</a></header> <article>Nullam id blandit eros. Nulla a tortor porta, dignissim ante sit amet, venenatis mauris.</article> </div> <div class='tweet'> <header><a>@twitteraar - jul 22</a></header> <article>Nullam id blandit eros. Nulla a tortor porta, dignissim ante sit amet, venenatis mauris.</article> </div> <div class='tweet'> <header><a>@twitteraar - jul 22</a></header> <article>Nullam id blandit eros. Nulla a tortor porta, dignissim ante sit amet, venenatis mauris.</article> </div>",
				true,
				'tweetbox'
			);
		}
		
		function poll(){
			return array(
				'Poll',
				"<input type='radio' name='poll' id='a'> <label for='a'>antwoord a</label> <br> <input type='radio' name='poll' id='b'> <label for='b'>antwoord b</label> <br> <input type='radio' name='poll' id='c'> <label for='c'>antwoord c</label> <br> <input type='radio' name='poll' id='d'> <label for='d'>antwoord d</label> <br>",
				false,
				'poll'
			);
		}
		
		function send(){
			
			$this->setDefaultSidebars();
			
			$this->html("
		  		<header class='main'>
		  			<a href='$GLOBALS[url]' id='logo'></a>
		  			<div class='mainColumn'></div>
		  			<ul id='mainMenu' class='menu'>
		  				<li>Archief</li>
		  				<li>Tip de redactie</li>
		  			</ul>
		  		</header>
		  		<div id='content'>
		  			<div id='leftColumn'>
		  				$this->leftComlumn
		  			</div>
		  			<div id='mainColumn' class='mainColumn blog'>
		  				$this->content
		  			</div>
		  			<div id='rightColumn'>
		  				$this->rightComlumn
		  			</div>
		  		</div>
		  		<footer class='main'>
		  			<a>Over ons</a> -
		  			<a>Contact</a> -
		  			<a>Archief</a> -
		  			<a>Tip de redactie</a>
		  		</footer>
			");
			
			parent::send();
			
		}
		
	}

?>