<?php 
namespace Core;

use Core\Response;

class JSON {
	public static function encode($data = []) {
		return json_encode($data);
	}
	public static function decode($encoded_string) {
		return json_decode($encoded_string);
	}
	public static function response($status = true, $message = '', $data = []) {
		return self::encode([
			'status' => $status,
			'message' => $message,
			'data' => $data
		]);
	}
	public static function parseResponse($encoded_string) {
		$data = self::decode($encoded_string);
		return new Response($data->status, $data->message, $data->data);
	}
}