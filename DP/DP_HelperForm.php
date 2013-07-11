<?php
class DP_HelperForm{
	public static $forms = array();
	public static $fieldError = '<span class="error">[ERROR]</span>';
	public $errors;
	public $name;
	public $data;
	
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
		if(!isset($data[$name])){
			return '';
		}
		
		return $data[$name];
	}
	
	public function getValue($name){
		$val = isset($this->data[$name]) ? $this->data[$name] : '';
		return esc_attr(stripslashes($val));
	}
	
	public static function getPost($post, $default = null){
		if(isset($_POST[$post])){
			return $_POST[$post];
		}
		
		return $default;
	}
	
	public static function buildAttrs($attrs){
		if(!$attrs |! is_array($attrs)){
			return '';
		}
		
		$attrstring = '';

		foreach($attrs as $key => $attr){
			$attrstring = $attrstring . ($attrstring ? ' ' : '') . $key . "='" . esc_attr($attr) . "'";
		}
		
		return $attrstring;
	}
	
	public function dropdown($name, $options, $attr = array()){
			$attr['name'] = $name;
			$attr['class'] = trim((isset($attr['class']) ? $attr['class'] : ''));
			$selected = $this->getValue($name);

			$html = "<select " . self::buildAttrs($attr) . ">";

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

			return $html;
		}
		
		public function input($name, $type = 'text', $attr = array()){
			$attr['name'] = $name;

			$attr['value'] = $this->getValue($name);
			$html = '<input type="' . $type . '" ' . self::buildAttrs($attr) . " />";
			
			return $html;
		}
	
	public static function validateData($data, $rules){
		$scrubbed = array();
		$errors = array();
		
		foreach($rules as $key => $ruleTypes){
			foreach($ruleTypes as $rule){
				switch($rule){
					case 'R' : 
					// Required
					if(!isset($data[$key]) || !$data[$key]){
						$errors[$key]['main'] = 'Please enter "' . $key . "'";
						$errors[$key]['general'] = 'This is required';
					}
					
					break;
					case 'N' :
					// Numeric, not necasarily required
					if(isset($data[$key]) && $data[$key]){
						if(!is_numeric($data[$key])){
							// Translate the database fields into names here
							$errors[$key]['main'] = "Please make sure '" . $key . "'is a number";
							$errors[$key]['general'] = "This needs to be a number";
						}
					}
					
					break;
					
					case 'O' : 
					// Optional
					break;
				}
			}
		}
		
		if(!$errors){
			return true;
		}
		
		return $errors;
	}
}