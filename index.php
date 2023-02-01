<?php


//Headers http - CORS
header('Access-Control-Allow-Origin: *');
header("Access-Control-Allow-Headers: X-API-KEY, Origin, X-Requested-With, Content-Type, Accept, Access-Control-Request-Method");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");
header("Allow: GET, POST, OPTIONS, PUT, DELETE");
$method = $_SERVER['REQUEST_METHOD'];
if($method == "OPTIONS") {
    die();
}


// Load Bootstrap
require_once ('bootstrap.php');





session_start();

$url = isset($_SERVER['PATH_INFO']) ? explode('/', ltrim($_SERVER['PATH_INFO'], '/')) : [];

if(!Core\Session::exists(CURRENT_USER_SESSION_NAME) && Core\Cookie::exists(REMEMBER_ME_COOKIE_NAME)) {
	App\Models\Users::loginUserFromCookie();
}
Core\RepositoryManager::initialize($entityManager);

Core\LoadModules::generate();

// Route the url
Core\Router::load($url);

