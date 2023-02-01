<?php 
namespace Auth\Controllers;

/*
* ControllerName: Auth
* Author:	JZCoding
* Author URI:	http://jzcoding.com
*/

use Core\Controller;
use Core\RepositoryManager;
use Core\Validate;
use Core\Input;
use Core\Router;
use Auth\Services\AuthService;

class AuthController extends Controller {
	private $_repository;
	public function __construct($controller, $action, $module) {
		parent::__construct($controller, $action, $module);
		$layoutName = $this->module->getConfigItem('layout');
		$modelName = $this->module->getConfigItem('model');
		if($layoutName != '') {
			$this->view->setLayout($layoutName);
		}
		$this->_repository = RepositoryManager::getRepository($modelName);
		AuthService::$repository = $this->_repository;
		AuthService::$modelName = $modelName;
	}

	public function loginAction() {
		if(AuthService::isLoggedIn()) {
			$loginRedirect = $this->module->getConfigItem('login_redirect');
			Router::redirect($loginRedirect);
		}
		$validation = new Validate();
		if(isset($_POST) && !empty($_POST)) {
			$validation->check($_POST, [
				'username' => [
					'display'	=> 'Username',
					'required'	=> true
				],
				'password' => [
					'display'	=> 'Password',
					'required'	=> true
				]
			]);
			if($validation->passed()) {
				$passwordField = $this->module->getConfigItem('passwordProperty');
				$passwordField = ($passwordField) ? $passwordField : 'password';
				$usernameField = $this->module->getConfigItem('usernameProperty');
				$usernameField = ($usernameField) ? $usernameField : 'username';
				$emailField = $this->module->getConfigItem('emailProperty');
				$emailField = ($emailField) ? $emailField : 'email';
				$user = $this->_repository->findOneBy([$usernameField => Input::post('username')]);
				if(!$user) {
					$user = $this->_repository->findOneBy([$emailField => Input::post('username')]);
				}

				if($user && password_verify(Input::post('password'), $user->get($passwordField))) {
					$remember = (isset($_POST['remember_me']) && Input::get('remember_me')) ? true : false;
					AuthService::login($user, $remember);
					$loginRedirect = $this->module->getConfigItem('login_redirect');
					Router::redirect($loginRedirect);
				} else {
					$validation->addError("Username/Password combination is incorrect!");
				}
			}
		}
		$this->view->displayErrors = $validation->displayErrors();
		$this->view->render('auth/login');
	}

	public function registerAction() {
		$validation = new Validate();
		$properties = $this->module->getConfigItem('register_properties');
		$post_values = [];
		if(!empty($properties)) {
			foreach ($properties as $key => &$property) {
				$post_values[$key] = '';
				if(!isset($property['display'])) {
					$property['display'] = ucwords(str_replace('_', ' ', $key));
				}
				if(isset($property['unique']) && $property['unique']) {
					$property['unique'] = $this->_repository->table_name;
				}
			}
		}
		if(!empty($_POST)) {
			$post_values = posted_values($_POST);
			$validation->check($_POST, $properties);
			if($validation->passed()) {
				$passwordField = $this->module->getConfigItem('passwordProperty');
				$passwordField = ($passwordField) ? $passwordField : 'password';
				$login_on_register = $this->module->getConfigItem('login_on_register');
				AuthService::register($post_values, $passwordField, $login_on_register);
				if($login_on_register) {
					$loginRedirect = $this->module->getConfigItem('login_redirect');
					Router::redirect($loginRedirect);
				} else {
					Router::redirect($this->module->action('Auth', 'login'));
				}
			}
		}
		$this->view->post = $post_values;
		$this->view->properties = $properties;
		$this->view->displayErrors = $validation->displayErrors();
		$this->view->render('auth/register');
	}

}