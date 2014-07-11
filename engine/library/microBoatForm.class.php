<?php

	/*
		~microBoatForm.class 0.1.3 - Made by William © Botenvouwer
		
		Description,
		
			The microBoatForm class enables a PHP programmer to easily create a advanced 
			HTML form in a really short time. It can also validate the forms and has the 
			ability to save the form template in json.
	*/

	class microBoatForm{
		
		private $id = 'microBoatForm';
		public $name = 'microBoatForm.class';
		public $description = 'A form made by the microBoatForm.class';
		public $submitType = 'post';
		public $action = '';
		public $param = '';
		public $buttonName = 'Send';
		public $multiple = false;
		protected $order = 0;
		protected $orderSubs = 0;
		
		public $formParts = null;
		public $formSubs = null;
		
		protected $errors = array();
		public $debugMode = true;
		
		function __construct(){
			$this->action = $_SERVER['PHP_SELF'];
			$this->formParts = new microBoatFormParts();
			$this->formSubs = new microBoatFormSubs();
		}
		
		function setID($id){
			if(!$id){
				$this->error('Unable to set id. Please check the datatype.');
			}
			else{
				$this->id = $id;
				foreach($this->formParts as $formPart){
					$formPart->formid = $id;
				}
			}
		}

		function getID(){
			return $this->id;
		}
		
		private function validateSubmitType($submitType){
			$submitType = strtolower($submitType);
			$prefix = 'mbfs_';
			$class = $prefix.$submitType;
			
			if(class_exists($class)){
				if(is_subclass_of($class, 'microBoatFormSubmit')){
					return $class;
				}
				else{
					$this->error("<i>$class</i> does not extend microBoatFormSubmit.", true);
				}
			}
			else{
				$this->error("Submit type: <i>$submitType</i> is not found.", true);
			}
		}
		
		private function validateElementType($elementType){
			$elementType = strtolower($elementType);
			$prefix = 'mbfe_';
			$class = $prefix.$elementType;
			
			if(class_exists($class)){
				if(is_subclass_of($class, 'microBoatFormElement')){
					return $class;
				}
				else{
					$this->error("<i>$class</i> does not extend microBoatFormElement.");
					return false;
				}
			}
			else{
				$this->error("Submit type: <i>$elementType</i> is not found.");
			}
		}
		
		function loadFromFile($url){
			$file = file_get_contents($url);
			$file = json_decode($file, true);
			$this->loadFromArray($file);
		}
		
		function loadFromArray($stack){
			
			if(isset($stack['header']['id'])){
				$this->setID($stack['header']['id']);
			}
			
			$this->name = (isset($stack['header']['name']) ? $stack['header']['name'] : 'microBoatForm.class');
			$this->description = (isset($stack['header']['description']) ? $stack['header']['description'] : 'A form made by the microBoatForm.class');
			
			if($stack['header']['submittype']){
				if($this->validateSubmitType($stack['header']['submittype'])){
					$this->submitType = $stack['header']['submittype'];
				}
			}
			
			$this->action = (isset($stack['header']['action']) ? $stack['header']['action'] : $_SERVER['PHP_SELF']);
			$this->param = (isset($stack['header']['param']) ? $stack['header']['param'] : '');
			$this->buttonName = (isset($stack['header']['buttonname']) ? $stack['header']['buttonname'] : 'Send');
			
			if(isset($stack['header']['multiple'])){
				$this->multiple = (is_bool($stack['header']['multiple']) ? $stack['header']['multiple'] : false);
			}
			
			if($this->multiple){
				foreach($stack['form'] as $array){
					$this->addSub($array['header']);
					foreach($array['form'] as $subarray){
						$subarray['classname'] = $array['header']['classname'];
						$this->addPart($subarray);
					}
				}
			}
			else{
				foreach($stack['form'] as $array){
					$this->addPart($array);
				}
			}
		}
		
		function addSub($className = 'sub', $name = '', $description = '', $order = 0){
			$num = func_num_args();
			
			if(!$className){
				$this->error('Specify a className for the sub like: addSub($className [, $name [,  $description]] or addSub($array)).');
			}
			
			if($num == 1){
				if(is_array($className)){
					$stack = $className;
					$className = (isset($stack['classname']) ? $stack['classname'] : '');
					$name = (isset($stack['name']) ? $stack['name'] : '');
					$description = (isset($stack['description']) ? $stack['description'] : '');
					$order = (isset($stack['order']) ? $stack['order'] : 0);
					if(!$className){
						$this->error('Specify a className for the sub like: addSub($classname [, $name [,  $description]] or addSub($array)).');
					}
				}
			}
			
			if(!is_string($className)){
				$this->error('className for sub must be DataType String');
			}
			elseif(!preg_match('/^[a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*$/', $className)){
				$this->error("<i>$className</i> is not a valid class name.");
			}
			else{
				if(isset($this->formSubs->$className)){
					$this->error("Sub <i>$className</i> already exist");
				}
				else{
					
					$reorder = false;
					if(isset($order)){
						if(is_numeric($order)){
							if(($order - 1) == $this->orderSubs){
								$order = $this->orderSubs;
								$this->orderSubs += 1;
							}
							else{
								$order = $this->orderSubs;
								$this->orderSubs += 1;
								$reorder = true;
							}
						}
						else{
							$order = $this->orderSubs;
							$this->orderSubs += 1;
						}
					}
					else{
						$order = $this->orderSubs;
						$this->orderSubs += 1;
					}
					
					$this->formSubs->$className = new microBoatFormSub($name, $description, $order);
					
					if($reorder){
						$this->reOrder($className, $order, 'formSubs');
					}
				}
			}
		}
		
		function addPart($stack){
			
			if(is_array($stack)){
				
				if(!isset($stack['id'])){
					$this->error('Can not create part becouse: id is not set.');
				}
				else if(!$stack['id']){
					$this->error('Can not create part becouse: id is not set.');
				}
				else if(!is_string($stack['id'])){
					$this->error('Can not create part becouse: id is not a string.');
				}
				else{
					$check = substr($stack['id'], -2);
					$className = $stack['id'];
					$id = $stack['id'];
					if(!preg_match('/^[a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*$/', $className)){
						$this->error("Can not create part becouse: <i>$className</i> (id) is not a valid class name");
						return;
					}
				}
				
				if(!isset($stack['type'])){
					$type = 'text';
				}
				else if(!$stack['type']){
					$type = 'text';
				}
				else if(!is_string($stack['type'])){
					$type = 'text';
				}
				else{
					if($elementType = $this->validateElementType($stack['type'])){
						$type = $stack['type'];
					}
					else{
						$this->error('Can not create part becouse: type is not valid.');
						return;
					}
				}
				
				$name = (isset($stack['name']) ? $stack['name'] : '');
				$description = (isset($stack['description']) ? $stack['description'] : '');
				$required = (isset($stack['required']) ? $stack['required'] : false);
				$value = (isset($stack['value']) ? $stack['value'] : '');
				
				if($this->isSend()){
					$value = '';
				}
				
				$disabled = (isset($stack['disabled']) ? $stack['disabled'] : false);
				$placeholder = (isset($stack['placeholder']) ? $stack['placeholder'] : '');
				$min = (isset($stack['min']) ? $stack['min'] : '');
				$max = (isset($stack['max']) ? $stack['max'] : '');
				$param = (isset($stack['param']) ? $stack['param'] : '');
				$options = (isset($stack['options']) ? $stack['options'] : '');
				$classNameSub = (isset($stack['classname']) ? $stack['classname'] : '');
				
				$reorder = false;
				if(isset($stack['order'])){
					if(is_numeric($stack['order'])){
						$order = $stack['order'];
						
						if(($stack['order'] - 1) == $this->order){
							$order = $this->order;
							$this->order += 1;
						}
						else{
							$order = $this->order;
							$this->order += 1;
							$reorder = true;
						}
					}
					else{
						$order = $this->order;
						$this->order += 1;
					}
				}
				else{
					$order = $this->order;
					$this->order += 1;
				}
				
				if(isset($this->formParts->$className)){
					$this->error("Can not create part becouse: formpart <i>$className</i> already exist.");
					return;
				}
				else{
					$this->formParts->$className = new $elementType($this->id, $id, $type, $name, $description, $required, $value, $disabled, $placeholder, $min, $max, $param, $classNameSub, $options, $order);
				}
				
				if($reorder){
					$this->reOrder($className, $stack['order'], 'formParts');
				}
				
			}
			else{
				$this->error('Can not create part becouse: Fist agument must be datatype array.');
			}
		}
		
		function reOrder($className = '', $order = 0, $which = 'formParts'){
			
			if(!$className){
				$this->error('Need className to re order.');
				return;
			}
			
			if(!is_numeric($order)){
				$order = 0;
			}	
			
			$num = count(get_object_vars($this->$which)) - 1;
			if($num < $order){
				$order = $num;
			}
			
			foreach($this->$which as $key => $formPart){
				if($key == $className){
					$formPart->order = $order;
				}
				else if($formPart->order >= $order){
					$formPart->order += 1;
				}
			}
			
		}
		
		function getHTML(){
			
			if(count(get_object_vars($this->formParts)) == 0){
				$this->error('No form parts set can not create from without form parts!');
			}
			
			$className = $this->validateSubmitType($this->submitType);
			$formSubmit = new $className($this);
			$button = $formSubmit->getButton();
			
			if($this->multiple){
				
				if(count(get_object_vars($this->formSubs)) == 0){
					$this->error('No form subs set can not create from without form subs in multiple mode!');
				}
				
				$formSubParts = array();
				foreach($this->formParts as $formPart){
					$parent = $formPart->childOfSub;
					if(!isset($formSubParts[$parent])){
						$formSubParts[$parent] = array();
					}
					$formSubParts[$parent][] = $formPart;
				}
				
				$formSubs = array();
				foreach($this->formSubs as $key => $formSub){
					$formParts = array();
					foreach($formSubParts[$key] as $formPart){
						$formParts[$formPart->order] = $formPart->getHTML();
					}
					ksort($formParts);
					$formParts = implode($formParts, '');
					$legend = ($formSub->name ? "<legend class='sub_title title'>$formSub->name</legend>" : '');
					$description = ($formSub->description ? "<p class='sub_description description'>$formSub->description</p>" : '');
					$formSubs[$formSub->order] = "
						<fieldset class='main sub'>
							$legend
							$description
							<table class='main sub'>
								$formParts
							</table>
						</fieldset>
					";
				}
				ksort($formSubs);
				$formSubs = implode($formSubs, '');
				$legend = ($this->name ? "<legend class='title'>$this->name</legend>" : '');
				$description = ($this->description ? "<p class='description'>$this->description</p>" : '');
				$form = "
					<div id='{$this->id}_div' class='microBoatForm'>
						$legend
						$description
						$formSubs
						$button
					</div>
					<input type='hidden' name='{$this->id}[isSend]' value='$this->param'>
				";
				
			}
			else{
				$formParts = array();
				foreach($this->formParts as $formPart){
					$formParts[$formPart->order] = $formPart->getHTML();
				}
				ksort($formParts);
				$formParts = implode($formParts, '');
				$legend = ($this->name ? "<legend class='title'>$this->name</legend>" : '');
				$description = ($this->description ? "<p class='description'>$this->description</p>" : '');
				$form = "
					<div id='{$this->id}_div' class='microBoatForm'>
						<fieldset class='main'>
							$legend
							$description
							<table class='main'>
								$formParts
								<tr>
									<td></td>
									<td>$button</td>
									<td></td>
								</tr>
							</table>
						</fieldset>
					</div>
					<input type='hidden' name='{$this->id}[isSend]' value='$this->param'>
				";
			}
			
			$form = $formSubmit->getHTML($form);
			return $form;
		}
		
		function validate(){
			
			$submit = $this->validateSubmitType($this->submitType);
			
			$submit = new $submit($this);
			
			$gonogo = true;
			foreach($this->formParts as $part){
				if(!$part->validateMe()){
					$gonogo = false;
				}
			}
			
			return $submit->validate($gonogo);
			
		}
		
		function getJson(){
			
			$header = array(
				'id' => $this->id,
				'name' => $this->name,
				'description' => $this->description,
				'submittype' => $this->submitType,
				'action' => $this->action,
				'param' => $this->param,
				'multiple' => $this->multiple,
				'buttonname' => $this->buttonName,
			);
			
			if($this->multiple){
				
			}
			else{
				$formParts = array();
				foreach($this->formParts as $key => $formPart){
					$formPartPre = array(
						'id' => $formPart->id,
						'type' => $formPart->type,
						'name' => $formPart->name,
						'description' => $formPart->description,
						'order' => $formPart->order
					);
					
					if($formPart->isRequired()){
						$formPartPre['required'] = true;
					}
					else{
						$formPartPre['required'] = false;
					}
					if($formPart->disabled){
						$formPartPre['disabled'] = $formPart->disabled;
					}
					if($formPart->placeholder){
						$formPartPre['placeholder'] = $formPart->placeholder;
					}
					if($formPart->min){
						$formPartPre['min'] = $formPart->min;
					}
					if($formPart->max){
						$formPartPre['max'] = $formPart->max;
					}
					if($formPart->param){
						$formPartPre['param'] = $formPart->param;
					}
					if($formPart->getValue()){
						$formPartPre['value'] = $formPart->getValue();
					}
					if($formPart->options){
						$formPartPre['options'] = $formPart->options;
					}
					
					$formParts[] = $formPartPre;
				}
				$form = array('header'=>$header, 'form'=>$formParts);
			}
			return json_encode($form, JSON_PRETTY_PRINT);
		}
		
		function saveFile($url){
			$fh = fopen($url, 'w');
			fwrite($fh, $this->getJson());
			fclose($fh);
		}
		
		function isSend(){
			if(isset($_REQUEST[$this->id]['isSend'])){
				return true;
			}
			else{
				return false;
			}
		}
		
		function getData(){
			if($this->isSend()){
				return $_REQUEST[$this->id];
			}
			else{
				$this->error('Can not get data becouse form is not send');
			}
		}
		
		private function error($message, $exit = false){
			
			$trace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS);
			
			$line = $trace[1]['line'];
			$file = $trace[1]['file'];
			
			$mode = ($exit ? 'Fatal Error' : 'Notice');
			$error = "<b>$mode</b>:  $message in <b>$file</b> on line <b>$line</b><br>";
			$this->errors[] = $error;
			if($this->debugMode){
				echo (count($this->errors == 1) ? '<br>' : ''). $error;
			}
			
			if($exit){
				exit;
			}
		}
		
		function getErrors(){
			return $this->errors;
		}
		
	}
	
	#--------------------- parts class --------------------------------------------------------------------------------------------------------------------
	
	class microBoatFormParts{
		
	}
	
	#--------------------- Subs class --------------------------------------------------------------------------------------------------------------------
	
	class microBoatFormSubs{
		
	}
	
	#--------------------- Sub part class --------------------------------------------------------------------------------------------------------------------
	
	class microBoatFormSub{
		
		public $name;
		public $description;
		public $order = 0;
		
		function __construct($name, $description, $order){
			$this->name = (isset($name) ? $name : '');
			$this->description = (isset($description) ? $description : '');
			$this->order = (isset($order) ? $order : 0);
		}
		
	}
	
	#--------------------- Default Form Submits --------------------------------------------------------------------------------------------------------------
	
	//Base
	class microBoatFormSubmit{
		
		protected $parent = '';
		
		function __construct($parent){
			$this->parent = $parent;
		}
		
	}
	
	//Submits
	class mbfs_post extends microBoatFormSubmit{
		
		function validate($gonogo){
			return $gonogo;
		}
		
		function getButton(){
			return "<input type='submit' value='{$this->parent->buttonName}' />";
		}
		
		function getHTML($form){
			$id = $this->parent->getID();
			return "<form action='{$this->parent->action}' method='post' id='$id'>$form</form>";
		}	
	}
	
	#Using this submit type requires the microBoat webapp.js
	class mbfs_webapp extends microBoatFormSubmit{
		
		function validate($gonogo){
			$formParts = $this->parent->formParts;
			foreach($formParts as $part){
				echo "<load query='#error_{$this->parent->id}_$part->id'>$part->error</load>";
				$part->error = '';
			}
		}
		
		function getButton(){
			return "<input type='button' value='$this->buttonName' action='$this->action' param='$this->param' form='this' />";
		}
		
		function getHTML($form){
			return "<form id='$this->id'>$form</form>";
		}
	}
	
	#Using this submit type requires the old microBoat webapp.js
	class mbfs_webapp_old extends microBoatFormSubmit{
		
		function validate($gonogo){
			if(!$gonogo){
				$formParts = $this->parent->formParts;
				$id = $this->parent->getID();
				foreach($formParts as $part){
					echo "<load id='#error_{$id}_$part->id'>$part->error</load>";
					$part->error = '';
				}
			}
			return $gonogo;
		}
		
		function getButton(){
			$id = $this->parent->getID();
			return "<input type='button' class='btn' value='{$this->parent->buttonName}' action='{$this->parent->action}' level='{$this->parent->param}' form='#{$id}' />";
		}
		
		function getHTML($form){
			$id = $this->parent->getID();
			return "<form id='{$id}'>$form</form>";
		}
	}
	
	#--------------------- Default Form Elements --------------------------------------------------------------------------------------------------------------
	
	//Base
	class microBoatFormElement{
		public $id = '';
		public $type = '';
		public $formid = '';
		public $name = '';
		public $description = '';
		public $disabled = '';
		public $placeholder = '';
		public $min = '';
		public $max = '';
		public $param = '';
		protected $value = '';
		protected $required = false;
		protected $reqclass = '';
		protected $star = '';
		public $order = 0;
		public $error = '';
		public $options;
		
		function __construct($formid ,$id, $type, $name, $description, $required, $value, $disabled, $placeholder, $min, $max, $param, $classNameSub, $options, $order){
			$this->id = $id;
			$this->type = $type;
			$this->name = $name;
			$this->required = $required;
			$this->description = $description;
			$this->formid = $formid;
			$this->disabled = $disabled;
			$this->placeholder = $placeholder;
			$this->min = $min;
			$this->max = $max;
			$this->param = $param;
			$this->childOfSub = $classNameSub;
			$this->options = $options;
			$this->order = $order;
			
			if($value){
				$this->setValue($value);
			}
			
			if($this->getValue()){
				$this->value = 'value="'.$this->getValue().'"';
			}
			$this->setRequired($required);
		}
		
		function validateMe(){
			if($this->required){
				if(!$this->getValue()){
					$this->error = 'Vul '.$this->name.' in!';
					return false;
				}
			}
			return true;
		}
		
		function isRequired(){
			return $this->required;
		}
		
		function setRequired($bool){
			if($bool){
				$this->required = true;
				$this->reqclass = ' required';
				$this->star = '<span class="required_star">*</span>';
			}
			else{
				$this->required = false;
				$this->reqclass = '';
				$this->star = '';
			}
		}
		
		function getValue(){
			if(isset($_REQUEST["{$this->formid}"][$this->id])){
				return $_REQUEST["{$this->formid}"][$this->id];
			}
			else{
				return false;
			}
		}
		
		function setValue($value){
			$_REQUEST["{$this->formid}"][$this->id] = $value;
			$this->value = 'value="'.$value.'"';
		}
	}
	
	//Multipecoise elements
	class mbfe_selectbox extends microBoatFormElement{
		
		public $options;
		protected $opt_html = '';
		
		function getHTML(){
			
			$this->opt_html .= "<option value='' ></option>";
			foreach($this->options as $key => $option){
				$selected = ($this->getValue_key() ==  $key ? ' selected' : '' );
				$this->opt_html .= "<option value='$key'$selected >$option</option>";
			}
			
			return "
				<tr>
					<td><label for='{$this->formid}_$this->id' title='$this->description' >{$this->star}{$this->name}:</label></td>
					<td><select id='{$this->formid}_$this->id' title='$this->description' class='{$this->formid}$this->reqclass' $this->reqclass name='{$this->formid}[$this->id]' >$this->opt_html</select></td>
					<td id='error_{$this->formid}_$this->id' class='error' >$this->error</td>
				</tr>
			";
			
			$this->error = '';
			
		}
		
		function getValue(){
			if(isset($_REQUEST["{$this->formid}"][$this->id])){
				if(isset($this->options[$_REQUEST["{$this->formid}"][$this->id]][1])){
					return $this->options[$_REQUEST["{$this->formid}"][$this->id]][1];
				}
				else{
					return false;
				}
			}
			else{
				return false;
			}
		}	
			
		function getValue_key(){
			if(isset($_REQUEST["{$this->formid}"][$this->id])){
				return $_REQUEST["{$this->formid}"][$this->id];
			}
			else{
				return false;
			}
		}
		
		function validateMe(){
			
			if($this->required){
				if(!$this->getValue()){
					$this->error = 'Selecteer één van de opties';
					return false;	
				}
			}
			return true;
			
		}
		
	}
	
	class mbfe_multiple extends microBoatFormElement{
		
		public $options;
		protected $opt_html = '';
		
		function getHTML(){
			
			$array = $this->getValue();
			foreach($this->options as $key => $option){
				$selected = (isset($array[$key]) ? ' selected' : '' );
				$this->opt_html .= "<option value='$key'$selected >$option</option>";
			}
			
			return "
				<tr>
					<td><label for='{$this->formid}_$this->id' title='$this->description' >{$this->star}{$this->name}:</label></td>
					<td><select id='{$this->formid}_$this->id' title='$this->description' class='{$this->formid}$this->reqclass' $this->reqclass name='{$this->formid}[$this->id][]' multiple >$this->opt_html</select></td>
					<td id='error_{$this->formid}_$this->id' class='error' >$this->error</td>
				</tr>
			";
			
			$this->error = '';
			
		}
		
		function validateMe(){
			
			if($this->required){
				if(!$this->getValue()){
					$this->error = 'Kies op zijn minst één optie';
					return false;	
				}
			}
			return true;
			
		}
		
		function getValue(){
			if(isset($_REQUEST["{$this->formid}"][$this->id])){
				$array = array();
				foreach($_REQUEST["{$this->formid}"][$this->id] as $key){
					$array[$key] = $this->options[$key][1];
				}
				return $array;
			}
			else{
				return false;
			}
		}
		
	}
	
	class mbfe_checkbox extends microBoatFormElement{
		
		public $options;
		protected $opt_html = '';
		
		function getHTML(){
			
			$array = $this->getValue();
			foreach($this->options as $key => $option){
				$selected = (isset($array[$key]) ==  $key ? ' checked' : '' );
				$this->opt_html .= "<li><input type='checkbox'$selected id='{$this->formid}_chek_$key' name='{$this->formid}[$this->id][]' title='$this->description' class='{$this->formid}$this->reqclass' value='$key'> <label for='{$this->formid}_chek_$key' >$option</label></li>";
			}
			
			return "
				<tr>
					<td><label title='$this->description' >{$this->star}{$this->name}:</label></td>
					<td></td>
					<td id='error_{$this->formid}_$this->id' class='error' >$this->error</td>
				</tr>				
				<tr>
					<td colspan='3'><ul style='list-style:none;'>$this->opt_html</ul></td>
				</tr>
			";
			
			$this->error = '';
			
		}
		
		function validateMe(){
			
			if($this->required){
				if(!$this->getValue()){
					$this->error = 'Selecteer één van de opties';
					return false;	
				}
			}
			return true;
			
		}
		
		function getValue(){
			if(isset($_REQUEST["{$this->formid}"][$this->id])){
				$array = array();
				foreach($_REQUEST["{$this->formid}"][$this->id] as $key){
					$array[$key] = $this->options[$key][1];
				}
				return $array;
			}
			else{
				return false;
			}
		}
		
	}
	
	class mbfe_radiobox extends microBoatFormElement{
		
		public $options;
		protected $opt_html = '';
		
		function getHTML(){
			
			foreach($this->options as $key => $option){
				$selected = '';
				if(strlen($this->getValue()) != 0){
					$selected = ($this->getValue() == $key ? ' checked' : '' );
				}
				$this->opt_html .= "<li><input type='radio'$selected id='{$this->formid}_{$this->id}_opt_$key' name='{$this->formid}[$this->id]' title='$this->description' class='{$this->formid}$this->reqclass' $this->reqclass  value='$key'> <label for='{$this->formid}_{$this->id}_opt_$key' >$option</label></li>";
			}
			
			return "
				<tr>
					<td><label title='$this->description' >{$this->star}{$this->name}:</label></td>
					<td></td>
					<td id='error_{$this->formid}_$this->id' class='error' >$this->error</td>
				</tr>				
				<tr>
					<td colspan='3'><ul style='list-style:none;'>$this->opt_html</ul></td>
				</tr>
			";
			
			$this->error = '';
			
		}
		
		function getValue(){
			if(isset($_REQUEST[$this->formid][$this->id])){
				return $_REQUEST[$this->formid][$this->id];
			}
			else{
				return false;
			}
		}
		
		function validateMe(){
			
			if($this->required){
				if(!isset($_REQUEST["{$this->formid}"][$this->id])){
					$this->error = 'Selecteer één van de opties';
					return false;
				}
				elseif(strlen($this->getValue()) == 0){
					$this->error = 'Selecteer één van de opties';
					return false;
				}
			}
			return true;
			
		}
		
	}
	
	//Elements
	class mbfe_text extends microBoatFormElement{
		
		function getHTML(){
			
			return "
				<tr>
					<td><label for='{$this->formid}_$this->id' title='$this->description' >{$this->star}{$this->name}:</label></td>
					<td><input id='{$this->formid}_$this->id' title='$this->description' placeholder='$this->placeholder' type='text' class='{$this->formid}$this->reqclass' $this->reqclass name='{$this->formid}[$this->id]'$this->value /></td>
					<td id='error_{$this->formid}_$this->id' class='error' >$this->error</td>
				</tr>
			";
			
			$element->error = '';
			
		}
		
	}
	
	class mbfe_hidden extends microBoatFormElement{
		
		function getHTML(){
			return "<input id='{$this->formid}_$this->id' type='hidden' class='{$this->formid}' name='{$this->formid}[$this->id]'$this->value />";
		}
		
		function validateMe(){
			return true;
		}
		
	}
	
	class mbfe_number extends microBoatFormElement{
		
		function getHTML(){
			
			return "
				<tr>
					<td><label for='{$this->formid}_$this->id' title='$this->description' >{$this->star}{$this->name}:</label></td>
					<td><input id='{$this->formid}_$this->id' title='$this->description' type='number' class='{$this->formid}$this->reqclass' $this->reqclass name='{$this->formid}[$this->id]'$this->value /></td>
					<td id='error_{$this->formid}_$this->id' class='error' >$this->error</td>
				</tr>
			";
			
			$element->error = '';
			
		}
		
		function validateMe(){
			if(!parent::validateMe()){
				return false;	
			}
			
			if($this->isRequired() || strlen($this->getValue()) != 0){
				if(!is_numeric($this->getValue())){
					$this->error = 'Dit moet een getal zijn';
					return false;	
				}
			}
			return true;
		}
		
	}
	
	#dutch zipcode
	class mbfe_postcode extends microBoatFormElement{
		
		function getHTML(){
			
			return "
				<tr>
					<td><label for='{$this->formid}_$this->id' title='$this->description' >{$this->star}{$this->name}:</label></td>
					<td><input id='{$this->formid}_$this->id' title='$this->description' type='text' class='{$this->formid}$this->reqclass' $this->reqclass maxlength='6' placeholder='8322RD' name='{$this->formid}[$this->id]'$this->value /></td>
					<td id='error_{$this->formid}_$this->id' class='error' >$this->error</td>
				</tr>
			";
			
			$element->error = '';
			
		}
		
		function validateMe(){
			if(!parent::validateMe()){
				return false;	
			}
			
			if(!preg_match("#^[0-9]{4}\s?[a-z]{2}$#i", $this->getValue())){
				$this->error = 'Dit is geen postcode';
				return false;	
			}
			
			return true;
		}
		
	}	
	
	class mbfe_adres extends microBoatFormElement{
		
		function getHTML(){
			
			return "
				<tr>
					<td><label for='{$this->formid}_$this->id' title='$this->description' >{$this->star}{$this->name}:</label></td>
					<td><input id='{$this->formid}_$this->id' title='$this->description' type='text' class='{$this->formid}$this->reqclass' $this->reqclass placeholder='8322RD' name='{$this->formid}[$this->id]'$this->value /></td>
					<td id='error_{$this->formid}_$this->id' class='error' >$this->error</td>
				</tr>
			";
			
			$element->error = '';
			
		}
		
		function validateMe(){
			if(!parent::validateMe()){
				return false;	
			}
			
			if(!preg_match("/^([a-z ]{3,}) ([0-9]{1,})([a-z]*)$/i", $this->getValue())){
				$this->error = 'Dit is geen adres';
				return false;	
			}
			
			return true;
		}
		
	}
	
	class mbfe_email extends microBoatFormElement{
		
		function getHTML(){
			
			return "
				<tr>
					<td><label for='{$this->formid}_$this->id' title='$this->description' >{$this->star}{$this->name}:</label></td>
					<td><input id='{$this->formid}_$this->id' title='$this->description' type='email' class='{$this->formid}$this->reqclass' $this->reqclass maxlength='100' placeholder='kees@live.nl' name='{$this->formid}[$this->id]'$this->value /></td>
					<td id='error_{$this->formid}_$this->id' class='error' >$this->error</td>
				</tr>
			";
			
			$element->error = '';
			
		}
		
		function validateMe(){
			if(!parent::validateMe()){
				return false;	
			}
			
			if(!filter_var($this->getValue(), FILTER_VALIDATE_EMAIL)){
				$this->error = 'Onjuist email adres';
				return false;	
			}
			
			return true;
		}
		
	}
	
	class mbfe_ip extends microBoatFormElement{
		
		function getHTML(){
			
			return "
				<tr>
					<td><label for='{$this->formid}_$this->id' title='$this->description' >{$this->star}{$this->name}:</label></td>
					<td><input id='{$this->formid}_$this->id' title='$this->description' type='text' class='{$this->formid}$this->reqclass' $this->reqclass maxlength='100' placeholder='33.243.110.114' name='{$this->formid}[$this->id]'$this->value /></td>
					<td id='error_{$this->formid}_$this->id' class='error' >$this->error</td>
				</tr>
			";
			
			$element->error = '';
			
		}
		
		function validateMe(){
			if(!parent::validateMe()){
				return false;	
			}
			
			if(!filter_var($this->getValue(), FILTER_VALIDATE_IP)){
				$this->error = 'Onjuist ip adres';
				return false;	
			}
			
			return true;
		}
		
	}
	
	class mbfe_password extends microBoatFormElement{
		
		function getHTML(){
			
			return "
				<tr>
					<td><label for='{$this->formid}_$this->id' title='$this->description' >{$this->star}{$this->name}:</label></td>
					<td><input id='{$this->formid}_$this->id' title='$this->description' type='password' class='{$this->formid}$this->reqclass' $this->reqclass maxlength='30' placeholder='*****' name='{$this->formid}[$this->id]'$this->value /></td>
					<td id='error_{$this->formid}_$this->id' class='error' >$this->error</td>
				</tr>
			";
			
			$element->error = '';
			
		}
		
		function validateMe(){
			if(!parent::validateMe()){
				return false;	
			}
			
			return true;
		}
		
	}
	
	class mbfe_repeatpassword extends microBoatFormElement{
		
		function getHTML(){
			
			return "
				<tr>
					<td><label for='{$this->formid}_{$this->id}_1' title='$this->description' >{$this->star}{$this->name}:</label></td>
					<td><input id='{$this->formid}_{$this->id}_1' title='$this->description' type='password' class='{$this->formid}$this->reqclass' $this->reqclass maxlength='30' placeholder='*****' name='{$this->formid}[{$this->id}_1]'$this->value /></td>
					<td id='error_{$this->formid}_$this->id' class='error' >$this->error</td>
				</tr>
				<tr>
					<td><label for='{$this->formid}_{$this->id}_2' title='$this->description' >{$this->star}Herhaal {$this->name}:</label></td>
					<td><input id='{$this->formid}_{$this->id}_2' title='$this->description' type='password' class='{$this->formid}$this->reqclass' $this->reqclass maxlength='30' placeholder='*****' name='{$this->formid}[{$this->id}_2]'$this->value /></td>
					<td></td>
				</tr>
			";
			
			$element->error = '';
			
		}
		
		function getValue(){
			if(isset($_REQUEST["{$this->formid}"][$this->id.'_1'])){
				return $_REQUEST["{$this->formid}"][$this->id.'_1'];
			}
			else{
				return false;
			}
		}
		
		function validateMe(){
			
			if(($this->getValue()) || ($this->required)){
				
				if($this->required && !$this->getValue()){
					$this->error = 'Vul '.$this->name.' in!';
					return false;
				}
				elseif(!$_REQUEST["{$this->formid}"][$this->id.'_2']){
					$this->error = 'Herhaal '.$this->name;
					return false;	
				}
				elseif($this->getValue() != $_REQUEST["{$this->formid}"][$this->id.'_2']){
					$this->error = $this->name . ' komt niet overeen';
					return false;
				}
			}
			
			return true;
		}
		
	}
	
	class mbfe_textbox extends microBoatFormElement{
		
		function getHTML(){
			
			return "
				<tr>
					<td><label for='{$this->formid}_$this->id' title='$this->description' >{$this->star}{$this->name}:</label></td>
					<td colspan='2' id='error_{$this->formid}_$this->id' class='error' >$this->error</td>
				</tr>
				<tr>
					<td></td>
					<td colspan='2' ><textarea id='{$this->formid}_$this->id' title='$this->description' class='{$this->formid}$this->reqclass' $this->reqclass maxlength='500' placeholder='$this->placeholder' name='{$this->formid}[$this->id]' >$this->value</textarea></td>
				</tr>
			";
			
			$element->error = '';
			
		}
		
		function validateMe(){
			if(!parent::validateMe()){
				return false;	
			}
			
			return true;
		}
		
	}
	
?>