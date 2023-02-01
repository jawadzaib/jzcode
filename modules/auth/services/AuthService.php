<?php 
namespace Auth\Services;

/*
* ServiceName: AuthService
* Author:	JZCoding
* Author URI:	http://jzcoding.com
*/

use Core\Service;
use Core\Session;
use Core\Cookie;
use Auth\Services\UserSessionsService;
use Core\RepositoryManager;

class AuthService extends Service {
	public static $repository;
	public static $modelName;
	public static $currentLoggedInUser;
	public function findByUsername($username) {
		return $this->getFirstRecord(['conditions' => 'username = ?', 'bind' => [$username]]);
	}
	public static function getRepositoryInstance() {
		if(empty(self::$repository)) {
			self::$repository = RepositoryManager::getRepository(self::$modelName);
		}
		return self::$repository;
	}
	public static function login($user, $rememberMe = false) {
		Session::set(CURRENT_USER_SESSION_NAME, $user->getId());
		if($rememberMe) {
			$hash = md5(uniqid() + rand(0, 100));
			$user_agent = Session::uagent_no_version();
			Cookie::set(REMEMBER_ME_COOKIE_NAME, $hash, REMEMBER_ME_COOKIE_EXPIRY);
			$fields = ['session' => $hash, 'user_agent' => $user_agent, 'user_id' => $user->id];
			self::$_db->query("DELETE FROM user_sessions WHERE user_id = ? AND user_agent = ?", [$user->id, $user_agent]);
			self::$_db->insert('user_sessions', $fields);
		}
	}

	public static function loginUserFromCookie() {
		$user_session = UserSessions::getFromCookie();
		if(isset($user_session->user_id) && $user_session->user_id != '') {
			$repo = self::getRepositoryInstance();
			$user = $repo->findOneBy(['id' => $user_session->user_id]);
			self::login($user);
			return $user;
		}
	}
	public static function isLoggedIn() {
		return (!isset(self::$currentLoggedInUser) && Session::exists(CURRENT_USER_SESSION_NAME));
	}
	public static function currentLoggedInUser() {
		if(!isset(self::$currentLoggedInUser) && Session::exists(CURRENT_USER_SESSION_NAME)) {
			$repo = self::getRepositoryInstance();
			$user_id = (int)Session::get(CURRENT_USER_SESSION_NAME);
			self::$currentLoggedInUser = $repo->findOneBy(['id' => $user_id]);
		}
		return self::$currentLoggedInUser;
	}

	public static function logout() {
		$user_agent = Session::uagent_no_version();
		$user_session = UserSessions::getFromCookie();
		$user_session->delete();	
		Session::delete(CURRENT_USER_SESSION_NAME);
		if(Cookie::exists(REMEMBER_ME_COOKIE_NAME)) {
			Cookie::delete(REMEMBER_ME_COOKIE_NAME);
		}
		self::$currentLoggedInUser = null;
		return true;
	}

	public static function register($data, $passwordProperty, $login = false) {
		$model = '\App\Models\\'.self::$modelName;
		$user = new $model();
		if(!empty($data)) {
			foreach ($data as $key => $value) {
				if(property_exists($user, $key)) {
					if($key == $passwordProperty) {
						$value = password_hash($value, PASSWORD_DEFAULT);
					}
					$user->set($key, $value);
				}
			}
		}
		RepositoryManager::save($user);
		if($login) {
			self::login($user);
		}
	}
}