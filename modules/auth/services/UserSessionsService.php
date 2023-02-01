<?php 
namespace Auth\Services;

/*
* ServiceName: UserSessionsService
* Author:	JZCoding
* Author URI:	http://jzcoding.com
*/

use Core\Service;

class UserSessionsService extends Service {
	public static function getFromCookie() {
		$thisModel = new self();
		if(Cookie::exists(REMEMBER_ME_COOKIE_NAME)) {
			$thisModel = $thisModel->_db->findFirst([
				'conditions' => "user_agent = ? AND session = ?",
				'bind'	=> [Session::uagent_no_version(), Cookie::get(REMEMBER_ME_COOKIE_NAME)]
			]);
		}
		return $thisModel;
	}
}