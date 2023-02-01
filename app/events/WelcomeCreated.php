<?php

namespace App\Events;

use Core\EventDispatcher;

class WelcomeCreated extends EventDispatcher {
    public $name;
    public function __construct($name = "")
    {
        $this->name = $name;
    }
}