<?php 

session_start();

require_once("vendor/autoload.php");

use \Slim\Slim;

$app = new Slim();

$app->config('debug', true);


require_once("rotas_site.php");
require_once("rotas_admin.php");
require_once("rotas_admin-users.php");
require_once("rotas_admin-categories.php");
require_once("rotas_admin-products.php");






$app->run();

 ?>







