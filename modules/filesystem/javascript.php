<?php

	class javascript{
		
		function main($requestedJavascriptFilePath = ''){
			
			$file = $this->root.'/javascript/'.$requestedJavascriptFilePath;
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