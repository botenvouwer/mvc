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
	function userHasRight($nameSpace, $action, $subaction){
		
		global $db;
		$user = $_SESSION['user'];
		
		if($rightList = getRightForRequest($nameSpace, $action, $subaction)){
			
			if($user > 0){
				
				$rightList = implode(',', $rightList);
				$query = "
					SELECT COUNT( * ) 
					FROM  `mvc_rights` r
					LEFT JOIN  `mvc_grouprights` gr ON r.`id` = gr.`right` 
					LEFT JOIN  `mvc_groups` g ON gr.`group` = g.`id` 
					LEFT JOIN  `mvc_groupusers` gu ON g.`id` = gu.`group` 
					LEFT JOIN  `mvc_users` u ON gu.`user` = u.`id` 
					WHERE u.`id` = $user
					AND r.`id` 
					IN ( $rightList )
				";
				
				$count = $db->one($query);
				
				if($count > 0){
					return true;
				}
				else{
					return false;
				}
			}
			else{
				return false;
			}
		}
		else{
			return true;
		}
	}
	
	//todo: verhuizen naar rights.class
	function getListOfUserRights($userID){
		
		
		
	}
	
	function getRightForRequest($nameSpace, $action, $subaction){
		//check if there is need to have right for a request
		
		global $db;
		
		//kijken of subactie unieke rechten nodig heeft
		$subactionRight = $db->one("SELECT `id` FROM `mvc_rights` WHERE `right` = :param", ':param', "$nameSpace.$action.$subaction");
		$actionRight = $db->one("SELECT `id` FROM `mvc_rights` WHERE `right` = :param", ':param', "$nameSpace.$action");
		$nameSpaceRight = $db->one("SELECT `id` FROM `mvc_rights` WHERE `right` = :param", ':param', "$nameSpace");
		
		if($subactionRight){
			$rightsRequieredList = getSuperRichts($nameSpace, $action);
			$rightsRequieredList[] = $subactionRight;
		}
		else if($actionRight){
			$rightsRequieredList = getSuperRichts($nameSpace);
			$rightsRequieredList[] = $actionRight;
		}
		else if($nameSpaceRight){
			$rightsRequieredList = getSuperRichts();
			$rightsRequieredList[] = $nameSpaceRight;
		}
		else{
			return false;
		}
		
		return $rightsRequieredList;
		
	}
	
	function getSuperRichts($nameSpace = false, $action = false){
		
		global $db;
		
		$array = array();
		$array[] = '*';
		
		if($nameSpace){
			$array[] = "$nameSpace.*";
		}
		
		if($action){
			$array[] = "$nameSpace.$action.*";
		}
		
		$keys = array();
		$parameters = array();
		foreach($array as $key => $value){
			$keys[] = ":pram_$key";
			
			$parameters[] = array(
				":pram_$key",
				$value,
				'str'
			);
		}
		
		$keys = implode(',', $keys);
		$rights = $db->query("SELECT `id` FROM `mvc_rights` WHERE `right` IN ($keys)", $parameters);
		
		$rightsReturn = array();
		while($row = $rights->fetch()){
			$rightsReturn[] = $row->id;
		}
		
		return $rightsReturn;
		
	}
	
?>