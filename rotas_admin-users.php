<?php

use \Hcode\PageAdmin;
use \Hcode\Model\User;


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

	$user = new User();

	$user->get((int)$iduser);

	$user->delete();//metodo que vai deletar;

	header("Location: /admin/users");
	exit;


});

$app->get("/admin/users/:iduser", function($iduser){//rota update

	User::verifyLogin();

	$user = new User();

	$user->get((int)$iduser);

	$page = new PageAdmin();

	$page->setTpl("users-update", array(
		"user"=>$user->getValues()
	));


});

$app->post("/admin/users/create", function(){//inserir dados

	User::verifyLogin();

	//var_dump($_POST);

	$user = new User();

	$_POST["inadmin"] = (isset($_POST["inadmin"]))?1:0;

	$user->setData($_POST);//criar os setters do array $_POST


	$user->save();//executa o insert dentro do banco

	//var_dump($user);

	header("Location: /admin/users");//redireciona para a pagina de listagem de usuarios
	exit;


});

$app->post("/admin/users/:iduser", function($iduser){//alterar dados

	User::verifyLogin();

	$user = new User();

	$_POST["inadmin"] = (isset($_POST["inadmin"]))?1:0;

	$user->get((int)$iduser);

	$user->setData($_POST);

	$user->update();//metodo para atualizar

	header("Location:/admin/users");
	exit;


});



?>