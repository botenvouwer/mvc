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
		protected $js = array();
		protected $css = array();
		protected $header = array();
		public $title;
		
		function __construct(){
			
		}
		
		public function addCSS($css){
			$this->css[] = $css;
		}
		
		public function addJS($js){
			$this->js[] = $js;
		}
		
		public function addHeader($header){
			$this->header[] = $header;
		}
		
		function html($html = ''){
			$this->html .= $html;
		}
		
		function send(){
			
			foreach(glob('*.css') as $filename){
				$this->add_css("view_{$this->style}/$filename");
			}
			
			
			
			$write_css = '';
			$write_js = '';
			
			$html = '
				<!DOCTYPE html>
				<html>
					<head>
						<title>'.$this->title.'</title>
						<meta http-equiv="Content-Type" content="text/html;charset=UTF-8">
						'.$header.'
						'.$css.'
						'.$javascript.'
					</head>
					<body>
						'.$this->html.'
					</body>
				</html>
			';
			
			//todo: html opmaken
			$html = trim($html);
			
			echo $html;
		}
	}

?>