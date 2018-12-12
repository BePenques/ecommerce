<?php

use \Hcode\PageAdmin;
use \Hcode\Model\User;
use \Hcode\Model\Category;

//rota para acessar template de categorias
$app->get("/admin/categories", function(){

	//verifica se a pessoa esta logada ou não
	User::verifyLogin();

	$categories = Category::listAll();//uma classe Category com o metodo ListAll

	$page = new PageAdmin();

	$page->setTpl("categories", [
		'categories'=>$categories//o template recebe um array
	]);


});

$app->get("/admin/categories/create", function(){//rota para criar categoria

	//verifica se a pessoa esta logada ou não
	User::verifyLogin();

	$page = new PageAdmin();

	$page->setTpl("categories-create");


});

$app->post("/admin/categories/create", function(){//rota para criar categoria

	//verifica se a pessoa esta logada ou não
	User::verifyLogin();

	$category = new Category();

	$category->setData($_POST);

	$category->save();

	header('Location: /admin/categories');
	exit;


});

$app->get("/admin/categories/:idcategory/delete", function($idcategory){

	//verifica se a pessoa esta logada ou não
	User::verifyLogin();

	$category = new Category();

	$category->get((int)$idcategory); //metodo para carregar o objto pra ter certeza que ele ainda existe

	$category->delete();

	header('Location: /admin/categories');
	exit;

});

$app->get("/admin/categories/:idcategory", function($idcategory){

	//verifica se a pessoa esta logada ou não
	User::verifyLogin();

	$category = new Category();

	$category->get((int)$idcategory);

	$page = new PageAdmin();

	$page->setTpl("categories-update", [

		'category'=>$category->getValues()
	]);



});

$app->post("/admin/categories/:idcategory", function($idcategory){

	//verifica se a pessoa esta logada ou não
	User::verifyLogin();

	$category = new Category();

	$category->get((int)$idcategory);

	$category->setData($_POST);

	$category->save();

	header('Location: /admin/categories');
	exit;

});


//rota para categoria

$app->get("/categories/:idcategory", function($idcategory){

	$category = new Category();

	$category->get((int)$idcategory);

	$page = new Page();

	$page->setTpl("category", [
		'category'=>$category->getValues(),
		'products'=>[]
	]);


});

?>