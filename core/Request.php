<?php 
namespace Core;

use Core\Response;
use Core\JSON;

class Request {
	private static $instance;
	private $_curl, $_options = [], $_response;

	public function __construct() {
		$this->_response = new Response();
	}

	public static function getInstance(){
        if (empty(self::$instance)) {
            self::$instance = new Request();
        }
        return self::$instance;
    }

	public function initialize($url = '') {
		$this->_curl = curl_init();
		if($url) {
			curl_setopt($this->_curl, CURLOPT_URL, $url);
		}
	}
	public function setOption($key, $value) {
		curl_setopt($this->_curl, $key, $value);
	}
	public function setOptionsArray($options) {
		foreach ($options as $key => $value) {
			$this->setOption($key, $value);
		}
	}
	public function send() {
		$response = curl_exec($this->_curl);
		$this->_response = ($response) ? JSON::parseResponse($response) : false;
		return $this->_response;
	}
	public function close() {
		curl_close($this->_curl);
	}

	public static function post($url, $data) {
		$instance = self::getInstance();
		$instance->initialize($url);
		$instance->setOption(CURLOPT_POST, true);
		$instance->setOption(CURLOPT_POSTFIELDS, $data);
		$instance->setOption(CURLOPT_HEADER, false);
		$response = $instance->send();
		$instance->close();
		return $response;
	}

	public static function get($url) {

	}
}