<?php

require_once __DIR__ . '/libs/router/router.php';
require_once __DIR__ . '/app/Controllers/ItemController.php';
require_once __DIR__ . '/app/Controllers/CategoryController.php';

$router= new Router();

//Items
$router->addRoute('gatos', 'GET', 'ItemController', 'GetItems');
$router->addRoute('gatos/:id', 'GET', 'ItemController', 'GetItem');
$router->addRoute('gatos', 'POST', 'ItemController', 'AddItem');
$router->addRoute('gatos/:id', 'DELETE', 'ItemController', 'DeleteItem' );
$router->addRoute('gatos/:id', 'PATCH', 'ItemController', 'EditItem');

//Categorias
$router->addRoute('peluqueros', 'GET', 'CategoryController', 'GetCategorias');
$router->addRoute('peluqueros/:id', 'GET', 'CategoryController', 'GetCategory');
$router->addRoute('peluqueros', 'POST', 'CategoryController', 'AddCategory');
$router->addRoute('peluqueros/:id', 'DELETE', 'CategoryController', 'DeleteCategoria');
$router->addRoute('peluqueros/:id', 'PATCH', 'CategoryController', 'EditCategoria');


$router->route($_GET["resource"], $_SERVER['REQUEST_METHOD']);
