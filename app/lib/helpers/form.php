<?php 


use Core\Form;

function text_field($name = '', $value = '', $field_attrs = [], $wrap_attrs = []) {
	Form::textField($name, $value, $field_attrs, $wrap_attrs);
}