<?php

	/**
	* ~view.class.php
	* 
	* version 3
	* 
	* description,
	* 	This class makes the basic view and can be extendet to create a complete view
	*/
	
	class view{
		
		protected $html = '';
		public $js;
		public $css;
		public $title;
		public $style;
		protected $header;
		private $write_css = false;
		private $write_js = false;
		
		function __construct(){
			
			global $conf;
			$this->conf = $conf;
			
			$this->js = array();
			$this->css = array();
			
			$this->add_js('jquery-2.0.3.min');
			$this->add_js('jquery.cookie');
			$this->add_js('jquery-ui');
			$this->add_js('jquery.ui.touch-punch.min');
			$this->add_js('jquery.royalslider.min');
			$this->add_js('webapp');
			$this->add_js('sorttable');
		}
		
		function css($css){
			$this->write_css .= $css;
		}
		
		function js($js){
			$this->write_js .= $js;
		}
		
		public function add_css($css){
			$this->css[] = $css;
		}
		
		public function add_js($js){
			$this->js[] = $js;
		}
		
		function html($html = ''){
			$this->html .= $html;
		}
		
		function send(){
			
			$this->header .= "<meta name='action-url' content='".ADRES."/action.php' />";
			
			$template = $this->conf['template'];
			$style = $this->conf['style'];
			$urlcss = ADRES."/template/$template/styles/$style";
			$urljs = ADRES."/engine/javascript";
			$root = ROOT."/template/$template/styles/$style";
			
			$curdir = getcwd(); 
			
			chdir($root.'/all'); 
			foreach(glob('*.css') as $filename){
				$this->add_css("all/$filename");
			}
			
			chdir($root.'/view_'.$this->style);
			foreach(glob('*.css') as $filename){
				$this->add_css("view_{$this->style}/$filename");
			}
			
			chdir($curdir);
			
			$js = '';
			foreach($this->js as $value){
				$js .= '<script type="text/javascript" src="'.$urljs.'/'.$value.'.js"></script>';
			}
			
			$css = '';
			foreach($this->css as $value){
				$css .= '<link rel="stylesheet" href="'.$urlcss.'/'.$value.'" type="text/css">';
			}
			
			$write_css = '';
			$write_js = '';
			
			if($this->write_css){
				$write_css = '<style>'.$this->write_css.'</style>';
			}

			if($this->write_js){
				$write_js = '<script type="text/javascript" >'.$this->write_js.'</script>';
			}
			
			$html = '
				<!DOCTYPE html>
				<html>
					<head>
						<title>'.$this->title.'</title>
						<meta http-equiv="Content-Type" content="text/html;charset=UTF-8">
						'.$this->header.'
						'.$css.'
						<!--[if IE]><link rel="stylesheet" type="text/css" href="'.$urlcss.'/ie/ie.css" /><![endif]-->
						'.$js.'
						'.$write_css.'
						'.$write_js.'
					</head>
					<body>
						'.$this->html.'
					</body>
				</html>
			';
			
			$html = trim($html);
			
			echo $html;
		}
	}

?>