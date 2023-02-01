<?php 
namespace Core;

class Form {
	public static function textField($name = '', $value = '', $field_attrs = [], $wrap_attrs = []) {
		$field_attrs_html = '';
		if(!empty($field_attrs)) {
			foreach ($field_attrs as $key => $val) {
				$field_attrs_html .= $key.'="'.$val.'" ';
			}
		}
		echo '<div class="form-group">
		<input type="text" name="'.$name.'" value="'.$value.'" '.$field_attrs_html.' />
		</div>';
	}
	public static function dropDown() {}
	public static function textArea() {}
	public static function button() {}
	public static function submit() {}
}