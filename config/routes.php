<?php 
use Core\Router;
// Set Routes for your application here


//Routes for WelcomeController
Router::route('Welcome', 'Welcome', 'index', '');
Router::route('Welcome/index', 'Welcome', 'index', '');


//Routes for ProfileController
Router::route('profile', 'Profile', 'index', '');
Router::route('Profile/index', 'Profile', 'index', '');


//Routes for ProductsController
Router::route('Products', 'Products', 'index', '');
Router::route('Products/index', 'Products', 'index', '');
Router::route('Products/new', 'Products', 'new', '');
Router::route('Products/edit/:id', 'Products', 'edit', '');
Router::route('Products/delete/:id', 'Products', 'delete', '');