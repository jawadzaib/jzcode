<?php

namespace Core;

class EventDispatcher {
    public function dispatch()
    {
        $data = get_object_vars($this);
    }
}