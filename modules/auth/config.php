<?php
// set configuration for your module
$config = [
	// 'layout' => 'auth',
	'model' => 'User',
	'idProperty' => 'id',
	'usernameProperty' => 'username',
	'passwordProperty' => 'password',
	'emailProperty' => 'email',
	'register_properties' => [
		'first_name' => [
			'display' => 'First Name',
			'required' => true
		],
		'last_name' => [
			'display' => 'Last Name',
			'required' => true
		],
		'username' => [
			'display' => 'UserName',
			'required' => true,
			'unique' => true,
			'min' => 4
		],
		'email' => [
			'display' => 'Email',
			'required' => true,
			'unique' => true,
			'valid_email' => true
		],
		'password' => [
			'display' => 'Password',
			'required' => true,
			'min' => 6,
			'type' => 'password'
		],
		'confirm' => [
			'display' => 'Confirm Password',
			'required' => true,
			'matches' => 'password',
			'type' => 'password'
		]
	],
	'login_redirect' => 'profile',
	'login_on_register' => true
];