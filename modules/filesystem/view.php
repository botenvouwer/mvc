<?php

	class style{
		
		private $selectedView = '';
		private $requestedFilePath = '';
		
		private function resolveViewAndFileName($requestedFilePath){
			$length = count($requestedFilePath);
			
			if($length >= 2){
				$this->selectedView = $requestedFilePath[0];
				
				if(!isView($selectedView)){
					fileNotFound();
				}
				
				unset($requestedFilePath[0]);
				$this->requestedFilePath = implode('/',$requestedFilePath);
			}
			else{
				fileNotFound();
			}
			
		}
		
		function css($requestedCSSFilePath = array()){
			
			$this->resolveViewAndFileName($requestedCSSFilePath);
			
			$file = $this->root.'/'.$this->selectedView.'/css/'.$this->requestedFilePath;
			if(is_file($file)){
				header('Content-Type: text/css');
				readfile($file);
				exit;
			}
			else{
				fileNotFound();
			}
			
		}
		
		function image($requestedIMAGEFilePath = array()){
			$this->resolveViewAndFileName($requestedIMAGEFilePath);
			
			$file = $this->root.'/'.$this->selectedView.'/image/'.$this->requestedFilePath;
			if(is_file($file)){
				
				header('Content-Type: '.mimeType($file));
				readfile($file);
				exit;
			}
			else{
				fileNotFound();
			}
		}
		
		function javascript($requestedJSFilePath = array()){
			$this->resolveViewAndFileName($requestedJSFilePath);
			
			$file = $this->root.'/'.$this->selectedView.'/javascript/'.$this->requestedFilePath;
			if(is_file($file)){
				header('Content-Type: application/javascript');
				readfile($file);
				exit;
			}
			else{
				fileNotFound();
			}
		}
		
	}

?>