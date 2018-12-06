<?php 

session_start();

require_once("vendor/autoload.php");

use \Slim\Slim;

use \Hcode\Page;

use \Hcode\PageAdmin;

use \Hcode\Model\User;

use \Hcode\Model\Category;

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

$app->run();

 ?>







