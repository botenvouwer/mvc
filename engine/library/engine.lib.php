<?php
	
	function print_w($array){
		return '<pre>'.print_r($array, true).'</pre>';
	}
	
	function error(){
		//todo make error screen
	}
	
	function pageNotFound(){
		if($GLOBALS['conf']['debug']){
			echo "Request could not be interpreted to proper action: <i>$GLOBALS[request]</i> <br> nameSpace: $GLOBALS[nameSpace] <br> action: $GLOBALS[action] <br> subaction: $GLOBALS[subaction] <br> ";
			exit;
		}
		else{
			echo 'not found 404 <br>'.$GLOBALS['request'];
			exit;
		}
	}
	
	function printRequest(){
		$prms = print_w($GLOBALS['parameters']);
		return "
			<table>
				<tr>
					<td colspan='2'>$GLOBALS[request]</td>
				</tr>
				<tr>
					<td>namespace:</td>
					<td>$GLOBALS[nameSpace]</td>
				</tr>
				<tr>
					<td>action:</td>
					<td>$GLOBALS[action]</td>
				</tr>
				<tr>
					<td>sub action:</td>
					<td>$GLOBALS[subaction]</td>
				</tr>
				<tr>
					<td>param:</td>
					<td>$GLOBALS[param]</td>
				</tr>
				<tr>
					<td>parameters:</td>
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
			trigger_error('MVC error: "[root]/engine/foreign_libraries/" folder not found.');
		}
	}
	
	function setView($viewName){
		
		global $actionInstance;
		
		//todo: haal view op en maak instantie aan
		
		$actionInstance->view = false;
		
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
		return true;
	}
	
?>