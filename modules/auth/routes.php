<?php
use Core\Router;

// set routes for your module


//Routes for AuthController
Router::route('auth', 'Auth', 'login', 'auth');
Router::route('auth/login', 'Auth', 'login', 'auth');
Router::route('auth/register', 'Auth', 'register', 'auth');
