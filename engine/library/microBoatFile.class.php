<?php
	
	/* ~microBoatFile.class.php - MicroBoatMVC
		
		With the file class you can manage the uploaded userfiles. You also need this 
		class to upload files and or create folders inside the MicroBoatMVC file system.
		
		
	*/
	
	class microBoatFile{
		
		protected $root;
		protected $db;
		protected $selectedFile = 0;
		protected $found = false;
		protected $name;
		protected $directory;
		protected $description;
		protected $file;
		protected $extension;
		protected $contentType;
		protected $public;
		protected $creator;
		protected $updated_by;
		protected $created;
		protected $updated;
		
		function __construct($fileID){
			$this->root = $GLOBALS['root'];
			$this->db = $GLOBALS['db'];
			$this->select($fileID);
		}
		
		function select($fileID){
			if($this->exists($fileID)){
				$this->selectedFile = $fileID;
				$this->found = true;
				$this->getInfo();
				return true;
			}
			else{
				$this->found = false;
				return false;
			}
		}
		
		protected function exists($fileID){
			return $this->db->one("SELECT EXISTS(SELECT * FROM `doc_files` WHERE `id` = :id)", ':id', $fileID);
		}
		
		private function getInfo(){
			
			$fileInfo = $this->db->query("SELECT * FROM `doc_files` WHERE `id` = $this->selectedFile");
			
			$fileInfo = $fileInfo->fetchAll();
			$fileInfo = $fileInfo[0];
			
			$this->name = $fileInfo->name;
			$this->description = $fileInfo->description;
			$this->directory = $fileInfo->directory;
			$this->file = $fileInfo->file;
			$this->extension = $fileInfo->extension;
			$this->contentType = $fileInfo->content_type;
			$this->public = $fileInfo->public;
			$this->creator = $fileInfo->creator;
			$this->updated_by = $fileInfo->updated_by;
			$this->updated = $fileInfo->updated;
			$this->created = $fileInfo->created;
			
		}
		
		function found(){
			return $this->found;
		}
		
		function isPublic(){
			return $this->public;
		}
		
		function name(){
			
		}
		
		function contentType(){
			return $this->contentType;
		}
		
		function randomName(){
			
			
			
		}
		
		function saveUpload($filename = false){
			
		}
		
		function stream(){
			
			$file = "$this->root/files/$this->file.file";
			if(is_file($file)){
				
				header('Content-Type: '+$this->contentType);
				header("Content-disposition: attachment; filename=\"$this->name.$this->extension\""); 
				readfile($file);
				exit;
				
			}
			else{
				trigger_error("File error: file not found, did you remove it!", E_USER_ERROR);
			}
			
		}
		
		function create($name, $content, $description = '', $directory, $public = true){
			
			//create new file
			
			return $fileID;
			
		}
		
		function delete(){
			
		}
		
		function rename($name, $description = false){
			
		}
		
		function move($folderID){
			
		}
		
	}
	
?>