<?php


function dnd($data) {
	echo '<pre>';
	var_dump($data);
	echo '</pre>';
	die();
}

function sanitize($value) {
	return htmlentities($value, ENT_QUOTES, 'UTF-8');
}


function currentLoggedInUser() {
	return Users::currentLoggedInUser();
}

function posted_values ($data) {
	$clean_data = array();
	if($data) {
		foreach ($data as $key => $value) {
			$clean_data[$key] = sanitize($value);
		}
	}
	return $clean_data;
}
