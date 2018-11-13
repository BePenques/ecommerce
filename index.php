<?php 

session_start();

require_once("vendor/autoload.php");

use \Slim\Slim;

use \Hcode\Page;

use \Hcode\PageAdmin;

use \Hcode\Model\User;

$app = new Slim();

$app->config('debug', true);

$app->get('/', function() {
    
	$page = new Page();

	$page->setTpl("index");

});

$app->get('/admin', function() {

	//verifica se a pessoa esta logada ou não
	User::verifyLogin();//verifyLogin - metodo estatico
    
	$page = new PageAdmin();

	$page->setTpl("index");

});

$app->get('/admin/login', function() {

	$page = new PageAdmin([
		"header"=>false,
		"footer"=>false
	]);//desabilita o metodo construtor, pois não precisa de header nem footer, é uma pagina unica

	$page->setTpl("login");
});

$app->post('/admin/login', function() {

	User::login($_POST["login"], $_POST["password"]);//metodo p receber o post do formulario login, e post da senha

	header("Location: /admin");
});

$app->run();

 ?>