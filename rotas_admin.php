<?php

use \Hcode\PageAdmin;
use \Hcode\Model\User;


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


$app->get("/admin/forgot", function(){

	$page = new PageAdmin([
		"header"=>false,
		"footer"=>false
	]);//desabilita o metodo construtor, pois não precisa de header nem footer, é uma pagina unica

	$page->setTpl("forgot");	

});

$app->post("/admin/forgot", function(){

	

	$user = User::getForgot($_POST["email"]);//metodo que recebe email por parametro

	header("Location: /admin/forgot/sent");
	exit;

});

$app->get("/admin/forgot/sent", function(){

	$page = new PageAdmin([
		"header"=>false,
		"footer"=>false
	]);//desabilita o metodo construtor, pois não precisa de header nem footer, é uma pagina unica

	$page->setTpl("forgot-sent");	

});

$app->get("/admin/forgot/reset", function(){

	$user = User::valideForgotDecrypt($_GET["code"]);//metodo para validar de que usuario pertence esse codigo

	$page = new PageAdmin([
		"header"=>false,
		"footer"=>false
	]);//desabilita o metodo construtor, pois não precisa de header nem footer, é uma pagina unica

	$page->setTpl("forgot-reset", array(
		"name"=>$user["desperson"],
		"code"=>$_GET["code"]
	));	

});

$app->post("/admin/forgot/reset", function(){

	$forgot = User::valideForgotDecrypt($_POST["code"]);

	User::setForgotUsed($forgot["idrecovery"]);//metodo para falar que esse metodo de recuperação já foi usado

	$user = new User(); //carrega um obj do tipo usuario

	$user->get((int)$forgot["iduser"]);//cria os setters

	$password = password_hash($_POST["password"], PASSWORD_DEFAULT, ["cost"=>12
	]);//criptografar antes de salvar no banco

	$user->setPassword($password);//função para salvar a a nova senha

		$page = new PageAdmin([
		"header"=>false,
		"footer"=>false
	]);//desabilita o metodo construtor, pois não precisa de header nem footer, é uma pagina unica

	$page->setTpl("forgot-reset-success");


});

?>