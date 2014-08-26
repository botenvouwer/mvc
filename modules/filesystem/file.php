<?php

	class getfile{
		
		function __construct(){
			$this->view = false;
		}
		
		function main($fileID){
			
			$file = new microBoatFile($fileID);
			
			if($file->found()){
				if($file->isPublic()){
					$file->stream();
				}
				else{
					fileNotFound();
				}
			}
			else{
				fileNotFound();
			}
			
		}
		
	}
	
	class file{
		
		function main(){
			
			//getfile from database
			//todo: create file class
			
			//->/getfile/1/file-name.ext
			
		}
		
	}

?>