<?php
class DP_HelperForm{
	public static $forms = array();
	public $fieldError = '<span class="error error-[FIELDFOR]">[ERROR]</span>';
	public $fieldErrorClass = 'error';
	public $outputErrors = true;
	public $errors = array();
	public $rules = array();
	public $name;
	public $data;
	// Change this to validate dates
	public $datePattern = '/^\d{2}-\d{2}-\d{4}$/';
	
	public function __construct($name, $data = array()){
		$this->name = $name;
		$this->data = $data;
		$this->rules = $rules;
	}
	
	public function setData($data = array()){
		if(!$data){
			return;
		}
		
		$this->data = $data;
	}
	
	public static function wasPosted(){
		return (bool)$_POST;
	}
	
	public static function createForm($name, $data = array()){
		if(isset(self::$forms[$name])){
			return self::$forms[$name];
		}
		
		return self::$forms[$name] = new DP_HelperForm($name, $data);
	}
	
	public static function getForm($name){
		if(isset(self::$forms[$name])){
			return self::$forms[$name];
		}
		
		throw new Exception("Form $name does not exist");
	}
	
	public function validateForm($data = array(), $rules = array()){
		$val = self::validateData($data, $rules);
		
		$this->data = $data;
		
		if($val !== true){
			$this->errors = $val;
		}
	}
	
	public function getRawValue($name){
		if(!isset($this->data[$name])){
			return '';
		}
		
		return $this->data[$name];
	}
	
	public function getValues(){
		return $this->data;
	}
	
	public function hasValue($name){
		return isset($this->data[$name]);
	}
	
	public function getValue($name){
		$val = isset($this->data[$name]) ? $this->data[$name] : '';

		if(is_string($val)){
			return esc_attr(stripslashes($val));
		}
		
		return $val;
	}
	
	public static function getPost($post, $default = null){
		if(isset($_POST[$post])){
			return $_POST[$post];
		}
		
		return $default;
	}
	
	public static function makeAttrs($attrs){
		$attrstring = '';
		
		if(!is_array($attrs)){
			return '';
		}

		foreach($attrs as $key => $attr){
			$attrstring = $attrstring . ($attrstring ? ' ' : '') . $key . "='" . esc_attr($attr) . "'";
		}
		
		return $attrstring;
	}
	
	public function buildAttrs($attrs, $name = null){
		if(!$attrs |! is_array($attrs)){
			return '';
		}
		
		if($name){
			if($this->hasError($name)){
				if(!isset($attrs['class'])){
					$attrs['class'] = $this->fieldErrorClass;
				}else{
					$attrs['class'] .= " " . $this->fieldErrorClass;
				}
			}
		}
		
		return self::makeAttrs($attrs);
	}

	
	public function hasError($name){
		if(!isset($this->errors[$name])){
			return false;
		}
		
		return $this->errors[$name];
	}
	
	public function getFieldError($name){
		if(!$this->hasError($name)){
			return '';
		}
		
		$fieldError = str_replace('[ERROR]', $this->errors[$name]['general'], $this->fieldError);
		$fieldError = str_replace('[FIELDFOR]', $name, $fieldError);

		return $fieldError;
	}
	
	
	public function dropdown($name, $options, $attr = array()){
			$attr['name'] = $name;
			$attr['class'] = trim((isset($attr['class']) ? $attr['class'] : ''));
	
			$selected = $this->getValue($name);

			$html = "<select " . $this->buildAttrs($attr, $name) . ">";

			// for option groups : $key = label, $val = array of options
			// for options		 : $key = value, $val = label

			foreach ($options as $key => $val) {
				if (is_array($val)){
					$html .= '<optgroup label="' . esc_attr($key) . '>';

					foreach ($val as $value => $label) {
						$html .= '<option ' . ($selected == $value ? 'selected="selected"' : ''). ' value="' . esc_attr($value) . '">' .  esc_attr($label) . '</option>';
					}

					$html .= '</optgroup>';
				} else {
					$html .= '<option ' . ($selected == $key ? 'selected="selected"' : ''). ' value="' . esc_attr($key) . '">' .  esc_attr($val) . '</option>';
				}
			}

			$html .= '</select>';
			
			if($this->outputErrors){
				$html .= $this->getFieldError($name);
			}
			
			return $html;
		}
		
		public static function createInput($name, $type, $attrs, $selected){
			$value = isset($attrs['value']) ? $attrs['value'] : '';
			$isChecked = false;

			if(($type == 'radio' || $type == 'checkbox') && $value){	
					
				if(is_array($selected)){
				
					if(in_array($value, $selected)){
						$isChecked = true;
					}
				}else{
					if($value == $selected){
						$isChecked = true;
					}
				}
				
			}
			
			$checkedStr = $isChecked ? "checked='checked'" : '';
			
			return "<input type='$type' name='$name' " . self::makeAttrs($attrs) . " " . $checkedStr . " />";
		}
		
		public function input($name, $type = 'text', $attr = array()){
			$attr['name'] = $name;

			if(!isset($attr['value'])){
				$attr['value'] = $this->getValue($name);
			}
			
			$html = '<input type="' . $type . '" ' . $this->buildAttrs($attr, $name) . " />";
			
			if($this->outputErrors){
				$html .= $this->getFieldError($name);
			}
			
			return $html;
		}
		
		public function checkbox($name, $value, $attr = array()){
			if(isset($this->data[$name])){
				$attr['checked'] = 'checked';
			}
			
			$attr['value'] = $value;
			
			return $this->input($name, 'checkbox', $attr);
		}
		
		// keys;
		// - general: to be displayed by the field
		// - main: to be displayed at a form top
		public function setError($key, $errorKeys = array()){
			$this->errors[$key] = $errorKeys;
		}
		
		public function hasErrors(){
			return (bool)$this->errors;
		}
		
		// Typically, this will be savable data
		public function getDataForRules(){
			$data = array();
			
			foreach($this->rules as $key => $rule){
				$data[$key] = $this->data[$key];
			}
			
			return $data;
		}
		
		public function valuesAsHiddenInputs($omit = array()){
			$inputs = '';
			foreach($this->data as $key => $value){
				if(in_array($key, $omit)){
					continue;
				}
				
				if(is_array($this->getRawValue($key))){
					foreach($this->getRawValue($key) as $val){
						$inputs .= "<input type='hidden' name='" . $key . "[]' value='$val' />";
					}
				}else{
					$inputs .= $this->input($key, 'hidden');
				}
			}
			
			return $inputs;
		}
	
	public function validate($rules){
		$this->rules = $rules;
		
		foreach($rules as $key => $ruleTypes){
			foreach($ruleTypes as $rule){
				switch($rule){
					case 'R' : 
					// Required
					if(!isset($this->data[$key]) || trim($this->data[$key]) == ''){
						$this->setError($key, array(
							'main' => 'Please enter "' . $key . "'",
							'general' => 'This is required'
						));
					}
					
					break;
					case 'N' :
					// Numeric, not necasarily required
					if(isset($this->data[$key]) && $this->data[$key]){
						if(!is_numeric($this->data[$key])){
							// Translate the database fields into names here
							$this->setError($key, array(
								'main' =>  "Please make sure '" . $key . "'is a number",
								'general' => "This needs to be a number"
							));
						}
					}
					break;
					
					case "DATE" :
					// Change the regex for different date formats
					if(isset($this->data[$key]) && $this->data[$key]){
						if(preg_match($this->datePattern, $this->data[$key]) != true){
							$this->setError($key, array(
								'main' =>  $key . " needs to be a valid date",
								'general' => "This needs to be a valid date"
							));
						}
					}
					break;
					
					case "EMAIL" :
					if (filter_var($this->data[$key], FILTER_VALIDATE_EMAIL) == false) {
						$this->setError($key, array(
							'main' =>  $key . " needs to be a valid email address",
							'general' => "This needs to be a valid email address"
						));
					}
					
					break;
					
					case 'O' : 
					// Optional
					break;
				}
				
				// Length and ranges
				
				// String Length LESS
				if(preg_match('/LENGTH-LESS\[(\d+)\]/', $rule, $matches)){
					if((strlen($this->data[$key]) >= (int) $matches[1])){
						$this->setError($key, array(
							'main' =>  $key . ' must be fewer than '  . $matches[1] . ' characters',
							'general' => 'This must fewer than ' . $matches[1] . " characters"
						));
					}
				}
				
				// String length GREATER
				if(preg_match('/LENGTH-GREATER\[(\d+)\]/', $rule, $matches)){
					if((strlen($this->data[$key]) <= (int) $matches[1])){
						$this->setError($key, array(
							'main' =>  $key . ' must be greater than ' . $matches[1] . ' characters',
							'general' => 'This must be more than ' . $matches[1] . ' characters'
						));
					}
				}
				
				// String length equal
				if(preg_match('/LENGTH-EQUAL\[(\d+)\]/', $rule, $matches)){
					if((strlen($this->data[$key]) != (int) $matches[1])){
						$this->setError($key, array(
							'main' =>  $key . ' must be ' . $matches[1] . ' characters',
							'general' => 'This must ' . $matches[1] . ' characters'
						));
					}
				}
				
				// Value must be GREATER
				if(preg_match('/VALUE-GREATER\[([\d\.-]+)\]/', $rule, $matches)){
					if((float)$this->data[$key] <= (float) $matches[1]){
						$this->setError($key, array(
							'main' => $key . ' must be greater than ' . $matches[1],
							'general' => 'This must be greater than ' . $matches[1]
						));
					}
				}
				
				// Value must be GREATER
				if(preg_match('/VALUE-LESS\[([\d\.-]+)\]/', $rule, $matches)){
					if((float)$this->data[$key] >= (float) $matches[1]){
						$this->setError($key, array(
							'main' => $key . ' must be less than ' . $matches[1],
							'general' => 'This must be less than ' . $matches[1]
						));
					}
				}
				
				// Value must be GREATER
				if(preg_match('/VALUE-GREATER-EQ\[([\d\.-]+)\]/', $rule, $matches)){
					if((float)$this->data[$key] < (float) $matches[1]){
						$this->setError($key, array(
							'main' => $key . ' must be greater or equal to ' . $matches[1],
							'general' => 'This must be greater or equal to ' . $matches[1]
						));
					}
				}
				
				// Value must be GREATER
				if(preg_match('/VALUE-LESS-EQ\[([\d\.-]+)\]/', $rule, $matches)){
					if((float)$this->data[$key] > (float) $matches[1]){
						$this->setError($key, array(
							'main' => $key . ' must be less or equal to ' . $matches[1],
							'general' => 'This must be less or equal to ' . $matches[1]
						));
					}
				}
			}
		}
	
		if($this->errors){
			return false;
		}
		
		return true;
	}
}