<?php 
namespace App\Controllers;

/*
* ControllerName: Welcome
* Author:	JZCoding
* Author URI:	http://jzcoding.com
*/

use App\Events\WelcomeCreated;
use Core\Controller;

class WelcomeController extends Controller {
	public function __construct($controller, $action) {
		parent::__construct($controller, $action);
	}

	/**
	* @method 
	* @return void
	*/
	public function indexAction() {
		
		$event = new WelcomeCreated("Hello");
		$event->dispatch();

		$this->view->render('welcome/index');
	}

}