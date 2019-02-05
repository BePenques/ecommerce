<?php

use \Hcode\Page;
use \Hcode\Model\Product;
use \Hcode\Model\Category;
use \Hcode\Model\Cart;
use \Hcode\Model\Address;
use \Hcode\Model\User;


$app->get('/', function() {

	$products = Product::listAll();
    
	$page = new Page();

	$page->setTpl("index", [
			'products'=>Product::checkList($products)
	]);

});

$app->get("/categories/:idcategory", function($idcategory){

	$page = (isset($_GET['page'])) ? (int)$_GET['page'] : 1;//pegar o numero da pagina via URL, se não achar pega 1

	$category = new Category();

	$category->get((int)$idcategory);

	$pages = [];

	$pagination =  $category->getProductsPage($page);

	for ($i=1; $i <= $pagination['pages'] ; $i++) { 

		array_push($pages, [
			'link'=>'/categories/'.$category->getidcategory().'?page='.$i,
			'page'=>$i

		]);
	}

	$page = new Page();



	$page->setTpl("category", [
		'category'=>$category->getValues(),
		'products'=>$pagination["data"],
		'pages'=>$pages
	]);


});

$app->get("/products/:desurl", function($desurl){

	$product = new Product();

	$product->getFromUrl($desurl);

	$page = new Page();

	$page->setTpl("product-detail", [
		'product'=>$product->getValues(),
		'categories'=>$product->getCategories()

	]);

});

$app->get("/cart", function(){

	$cart = Cart::getFromSession();

    $page = new Page();

	$page->setTpl("cart", [
		'cart'=>$cart->getvalues(),
		'products'=>$cart->getProducts(),
		'error'=>Cart::getMsgError()
	]);

});

$app->get("/cart/:idproduct/add", function($idproduct){//rota para add no carrinho
	
	$product = new product();

	$product->get((int)$idproduct);

	$cart = Cart::getFromSession();

	$qtd = (isset($_GET['qtd'])) ? (int)$_GET['qtd'] : 1;

	for ($i=0; $i < $qtd; $i++) { 
		$cart->addProduct($product);
	}

	$cart->addProduct($product);

	header("Location: /cart");
	exit;

});

$app->get("/cart/:idproduct/minus", function($idproduct){//rota para remover apenas um produto do carrinho
	$product = new product();

	$product->get((int)$idproduct);

	$cart = Cart::getFromSession();

	$cart->removeProduct($product);

	header("Location: /cart");
	exit;

});

$app->get("/cart/:idproduct/remove", function($idproduct){//rota para remover todos os produto do carrinho
	$product = new product();

	$product->get((int)$idproduct);

	$cart = Cart::getFromSession();

	$cart->removeProduct($product, true);

	header("Location: /cart");
	exit;

});

$app->post("/cart/freight", function(){

	$cart = Cart::getFromSession();

	$cart->setFreight($_POST['zipcode']);

	header("Location:/cart");
	exit;

});

$app->get("/checkout", function(){

	User::verifyLogin(false);

	$cart = Cart::getFromSession();

	$address = new Address();

	$page = new Page();

	
	if (!$address->getdesaddress()) $address->setdesaddress('');
if (!$address->getdescomplement()) $address->setdescomplement('');
if (!$address->getdesdistrict()) $address->setdesdistrict('');
if (!$address->getdescity()) $address->setdescity('');
if (!$address->getdesstate()) $address->setdesstate('');
if (!$address->getdescountry()) $address->setdescountry('');
if (!$address->getdeszipcode()) $address->setdeszipcode('');

	$page->setTpl("checkout", [
		'cart'=>$cart->getvalues(),
		'address'=>$address->getValues()

	]);


});

$app->get("/login", function(){


	$page = new Page();

	$page->setTpl("login", [
		'error'=>User::getError(),
		'errorRegister'=>User::getErrorRegister(),
		'registerValues'=>(isset($_SESSION['registerValues'])) ? $_SESSION['registerValues'] : ['name'=>'', 'email'=>'', 'phone'=>'']
	]);

});

$app->post("/login", function(){

	try{

			User::login($_POST['login'], $_POST['password']);

    }catch(Exception $e){

    		User::setError($e->getMessage()); 

    }

	header("Location: /checkout");
	exit;
});

$app->get("/logout", function(){

	User::logout();

   	header("Location: /login");
   	exit;
});

$app->post("/register", function(){


	$_SESSION['registerValues'] = $_POST;

	if (!isset($_POST['name']) || $_POST['name'] == '')
	{
		User::setErrorRegister("Preencha o campo nome");
		header("Location: /login");
		exit;
	}

	if (!isset($_POST['email']) || $_POST['email'] == '')
	{
		User::setErrorRegister("Preencha o campo email");
		header("Location: /login");
		exit;
	}

		if (!isset($_POST['password']) || $_POST['password'] == '')
	{
		User::setErrorRegister("Preencha o campo senha");
		header("Location: /login");
		exit;
	}

	if(User::checkLoginExist($_POST['email']) === true)
	{
		User::setErrorRegister("Esse email já esta cadastrado");
		header("Location: /login");
		exit;

	}

	$user = new User();

	$user->setData([
		'inadmin'=>0,
		'deslogin'=>$_POST['email'],
		'desperson'=>$_POST['name'],
		'desemail'=>$_POST['email'],
		'despassword'=>$_POST['password'],
		'nrphone'=>$_POST['phone']
	]);

	$user->save();

	User::login($_POST['email'], $_POST['password']);//já faz a autenticação

	header("Location: /checkout");
	exit;

});

?>