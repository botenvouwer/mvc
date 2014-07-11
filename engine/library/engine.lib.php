<?php
	
	function print_w($array){
		return '<pre>'.print_r($array, true).'</pre>';
	}
	
	function error(){
		//todo make error screen
	}
	
	//can import php plugins
	function import($plugin_name){
		if(is_dir("$GLOBALS[root]/engine/plug-ins/$plugin_name")){
			#chek if use exists
			include_once("$GLOBALS[root]/engine/plug-ins/$plugin_name/use.php");
		}
		else{
			error('Plug-in not found');
		}
	}
	
?>