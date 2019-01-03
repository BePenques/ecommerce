<?php

use \Hcode\Page;
use \Hcode\Model\Product;
use \Hcode\Model\Category;


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

	$page = new Page();

	$pagination =  $category->getProductsPage($page);

	$page->setTpl("category", [
		'category'=>$category->getValues(),
		'products'=>$pagination["data"]
	]);


});

?>