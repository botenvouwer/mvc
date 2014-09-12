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
		private $name = '';
		public $title;
		
		function setName($name){
			if(!$this->name){
				$this->name = $name;
				return true;
			}
			return false;
		}
		
		function getName(){
			return $this->name;
		}
		
		public function addCSS($css, $media = false){
			$this->css[] = array($css, $media);
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
			
			global $conf;
			global $root;
			global $url;
			
			//loop through added css files and add them to html file if they exist
			$css = '';
			foreach($this->css as $filename){
				
				$filepath = "$root/views/$this->name/css/$filename[0]";
				$urlpath = "$url/style/css/$this->name/$filename[0]";
				if(is_file($filepath)){
					$media = ($filename[1] ? " media='$filename[1]'" : '');
					$css .= "<link rel='stylesheet' href='$urlpath' type='text/css'>\n";
				}
				else{
					if($conf['debug']){
						trigger_error('View error: css file not found at: '.$filepath, E_USER_ERROR);
					}
				}
				
			}
			
			//loop door javascript en voeg toe als ze gevonden zijn
			$js = '';
			foreach($this->js as $filename){
				$filepathView = "$root/views/$this->name/javascript/$filename";
				$urlpathView = "$url/style/javascript/$this->name/$filename";
				$filepathJS = "$root/javascript/$filename";
				$urlpathJS = "$url/javascript/$filename";
				
				if(is_file($filepathView)){
					$js .= "<script src='$urlpathView'></script>\n";
				}
				else if(is_file($filepathJS)){
					$js .= "<script src='$urlpathJS'></script>\n";
				}
				else{
					if($conf['debug']){
						trigger_error('View error: javascript file not found at: '.$filepathJS.' or '.$filepathView, E_USER_ERROR);
					}
				}
			}
			
			//loop door headers en voeg ze toe aan het document
			$headers = '';
			foreach($this->header as $header){
				$headers .= "$header\n";
			}
			
			$this->html = trim($this->html);
			$html = '
				<!DOCTYPE html>
				<html>
					<head>
						<title>'.$this->title.'</title>
						<meta http-equiv="Content-Type" content="text/html;charset=UTF-8">
						'.$headers.'
						'.$css.'
						'.$js.'
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