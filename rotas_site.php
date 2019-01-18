<?php

use \Hcode\Page;
use \Hcode\Model\Product;
use \Hcode\Model\Category;
use \Hcode\Model\Cart;


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
		'products'=>$cart->getProducts()
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

	$cart->addProduct($product);

	header("Location: /cart");
	exit;

});

$app->get("/cart/:idproduct/remove", function($idproduct){//rota para remover apenas um produto do carrinho
	$product = new product();

	$product->get((int)$idproduct);

	$cart = Cart::getFromSession();

	$cart->addProduct($product, true);

	header("Location: /cart");
	exit;

});

$app->post("/cart/freight", function(){

	$cart = Cart::getFromSession();

	$cart->setFreight($_POST['zipcode']);

	header("Location:/cart");
	exit;

});

?>