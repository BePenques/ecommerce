<?php

use \Hcode\PageAdmin;
use \Hcode\Model\User;

$app->get("/admin/users/:iduser/password", function($iduser){

	User::verifyLogin();

	$user = new User();

	$user->get((int)$iduser);

	$page = new PageAdmin();

	$page->setTpl("users-password", [
		"user"=>$user->getValues(),
		"msgError"=>User::getError(),
		"msgSuccess"=>User::getSuccess()
	]);
});

$app->post("/admin/users/:iduser/password", function($iduser){

	User::verifyLogin();

	if(!isset($_POST['despassword']) || $_POST['despassword']==='')
	{
		User::setError("Preencha a nova senha");
		header("Location: /admin/users/$iduser/password");
		exit;
	}

		if(!isset($_POST['despassword-confirm']) || $_POST['despassword-confirm']==='')
	{
		User::setError("Preencha a confirmação da nova senha");
		header("Location: /admin/users/$iduser/password");
		exit;
	}

	if($_POST['despassword'] !== $_POST['despassword-confirm'])
	{
		User::setError("Confirme corretamente as senhas");
		header("Location: /admin/users/$iduser/password");
		exit;
	}

	$user = new User();

	$user->get((int)$iduser);

	$user->setPassword(User::getPasswordHash($_POST['despassword']));

	User::setSuccess("Senha alterada com sucesso");

	header("Location: /admin/users/$iduser/password");
	exit;

});

$app->get("/admin/users", function(){//lista todos os usuarios

	User::verifyLogin();

	$search = (isset($_GET['search'])) ? $_GET['search'] : "";

	$page = (isset($_GET['page'])) ? (int)$_GET['page'] : 1;

	if($search != ''){

		$pagination = User::getPageSearch($search, $page);

	}else{
		$pagination = User::getPage($page);
	}

	$pages = [];

	for($x = 0; $x < $pagination['pages']; $x++)
	{
		array_push($pages, [
		 'href'=>'/admin/users?'.http_build_query([
            'page'=>$x+1,
            'search'=>$search
		 ]), 
		 'text'=>$x+1
		]);
	}

	$page = new PageAdmin();

	$page->setTpl("users", array(
		"users"=>$pagination['data'],
		"search"=>$search,
		"pages"=>$pages
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