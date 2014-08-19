<?php
	
	function print_w($array){
		return '<pre>'.print_r($array, true).'</pre>';
	}
	
	function error(){
		//todo make error screen
	}
	
	function pageNotFound(){
		if($GLOBALS['conf']['debug']){
			
			$debug = debug_backtrace();
			echo "Request could not be interpreted to proper action: <i>$GLOBALS[request]</i> <br> At line: <b>{$debug[0]['line']}</b> - in file: <i>{$debug[0]['file']}</i> <br> nameSpace: <b>$GLOBALS[nameSpace]</b> <br> action: <b>$GLOBALS[action]</b> <br> subaction: <b>$GLOBALS[subaction]</b> <br> ";
			exit;
		}
		else{
			echo "Not found 404 <br> $GLOBALS[request]";
			exit;
		}
	}
	
	function fileNotFound(){
		echo "Requested file not found 404 <br> $GLOBALS[request]";
		exit;
	}
	
	function printRequest(){
		$prms = print_w($GLOBALS['parameters']);
		return "
			<table>
				<tr>
					<td>request:</td>
					<td><i>$GLOBALS[request]</i></td>
				</tr>
				<tr>
					<td>nameSpace:</td>
					<td><b>$GLOBALS[nameSpace]</b></td>
				</tr>
				<tr>
					<td>action:</td>
					<td><b>$GLOBALS[action]</b></td>
				</tr>
				<tr>
					<td>subaction:</td>
					<td><b>$GLOBALS[subaction]</b></td>
				</tr>
				<tr>
					<td valign='top'>parameters:</td>
					<td>$prms</td>
				</tr>
			</table>
		";
	}
	
	//to import large blocks of php code like pdf or mail plugins
	function import($plugin_name){
		if(is_dir("$GLOBALS[root]/engine/foreign_libraries/$plugin_name")){
			if(file_exists("$GLOBALS[root]/engine/foreign_libraries/$plugin_name/use.php")){
				include_once("$GLOBALS[root]/engine/foreign_libraries/$plugin_name/use.php");
			}
			else{
				trigger_error("MVC error: No use.php found in foreign library folder. Declare a use.php. use.php preforms all necessary actions to load foreign library.", E_USER_ERROR);
			}
		}
		else{
			trigger_error('MVC error: "[root]/engine/foreign_libraries/" folder not found.', E_USER_ERROR);
		}
	}
	
	function isView($viewName){
		
		if(file_exists($GLOBALS['root'].'/views/'.$viewName.'/'.$viewName.'.view.php')){
			return true;
		}
		else{
			return false;
		}
		
	}
	
	function setView($viewName){
		
		global $actionInstance;
		$actionInstance->view = false;
		
		if($viewName){
			if(isView($viewName)){
				include_once($GLOBALS['root'].'/views/'.$viewName.'/'.$viewName.'.view.php');
				$view = $viewName.'view';
				if(class_exists($view)){
					$actionInstance->view = new $view();
					$actionInstance->view->setName($viewName);
				}
				else{
					trigger_error("MVC error: view '$viewName' has no view extention", E_USER_ERROR);
				}
			}
			else{
				trigger_error("MVC error: view '$viewName' not found", E_USER_ERROR);
			}
		}
		else{
			$actionInstance->view = false;
		}
		
	}
	
	function mimeType($filename){
		include_once($GLOBALS['root'].'/conf/mimetypes.php');
		$fileSuffix = '';
		preg_match("|\.([a-z0-9]{2,4})$|i", $filename, $fileSuffix);
		$fileSuffix = $fileSuffix[1];
		$fileSuffix = strtolower($fileSuffix);
		if(isset($mimeTypes[$fileSuffix])){
			return $mimeTypes[$fileSuffix];
		}
		else{
			return 'application/octet-stream';
		}
	}
	
	function parseList($list, $separator = ','){
		$list = explode($separator, $list);
		return array_map('trim', $list);
	}
	
	function getLastPartOfString($url, $separator = '/'){
		$url = explode($separator, $url);
		return end($url);
	}
	
	function parseJsonFile($location){
		$json = file_get_contents($location);
		return json_decode($json, true);
	}
	
	//checks if user has permission to use request
	function userHasRight($request){
		//todo: create rights system
		return true;
	}
	
?>