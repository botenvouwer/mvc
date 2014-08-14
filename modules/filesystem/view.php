<?php

	class style{
		
		private $selectedView = '';
		private $requestedFilePath = '';
		
		private function resolveViewAndFileName($requestedFilePath){
			$length = count($requestedFilePath);
			
			if($length >= 2){
				$this->selectedView = $requestedFilePath[0];
				
				//todo: controleren of view bestaad
				
				unset($requestedFilePath[0]);
				$this->requestedFilePath = implode('/',$requestedFilePath);
			}
			else{
				fileNotFound();
			}
			
		}
		
		function css($requestedCSSFilePath = array()){
			
			$this->resolveViewAndFileName();
			
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
		
		function image(){
			$this->resolveViewAndFileName();
			
			$file = $this->root.'/'.$this->selectedView.'/javascript/'.$this->requestedFilePath;
			if(is_file($file)){
				
				//zoek image mime type op
				
				header('Content-Type: application/javascript');
				readfile($file);
				exit;
			}
			else{
				fileNotFound();
			}
		}
		
		function javascript(){
			$this->resolveViewAndFileName();
			
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