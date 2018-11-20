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
	exit;
});

$app->get('/admin/logout', function(){

	User::logout();

	header("Location: /admin/login");
	exit;
});

$app->get("/admin/users", function(){//lista todos os usuarios

	User::verifyLogin();

	$users = User::listAll();

	$page = new PageAdmin();

	$page->setTpl("users", array(
		"users"=>$users
	));


});

$app->get("/admin/users/create", function(){//rota create

	User::verifyLogin();

	$page = new PageAdmin();

	$page->setTpl("users-create");


});

$app->get("/admin/users/:iduser/delete", function($iduser){//deletar dados

	User::verifyLogin();


});

$app->get("/admin/users/:iduser", function($iduser){//rota update

	User::verifyLogin();

	$page = new PageAdmin();

	$page->setTpl("users-update");


});

$app->post("/admin/users/create", function(){//inserir dados

	User::verifyLogin();


});

$app->post("/admin/users/:iduser", function($iduser){//alterar dados

	User::verifyLogin();


});



$app->run();

 ?>