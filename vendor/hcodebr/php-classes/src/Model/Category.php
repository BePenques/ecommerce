<?php

namespace Hcode\Model;

use \Hcode\DB\Sql;

use \Hcode\Model;

use \Hcode\Mailer;



class Category extends Model{


		public static function listAll()
		{

			$sql = new Sql();

			return $sql->select("SELECT * FROM tb_categories  ORDER BY descategory");
		}
		
		public function save()//função para salvar os dados no banco
		{

			$sql = new Sql();
			/*
			pdesperson VARCHAR(64), 
			pdeslogin VARCHAR(64), 
			pdespassword VARCHAR(256), 
			pdesemail VARCHAR(128), 
			pnrphone BIGINT, 
			pinadmin TINYINT
			*/

			$results = $sql->select("CALL sp_categories_save(:idcategory, :descategory)", array(

				":idcategory"=>$this->getidcategory(),
				":descategory"=>$this->getdescategory()
				
			));

			$this->setData($results[0]);

			Category::updateFile();//atualizar arquivo html
		}



		public function get($idcategory)
		{
			$sql = new Sql();

			$results = $sql->select("SELECT * FROM tb_categories WHERE idcategory = :idcategory", [
				':idcategory'=>$idcategory
			]);

			$this->setData($results[0]); //criar os setters(colocar os dados dentro do obj)

		}

		public function delete()
		{
			$sql = new Sql();

			$sql->query("DELETE FROM tb_categories WHERE idcategory = :idcategory", [
				":idcategory"=>$this->getidcategory()//pegar do proprio objeto
			]);

			Category::updateFile();//atualizar arquivo html
		}

		public static function updateFile()
		{

			$categories = Category::listALL();

			$html = [];

			foreach ($categories as $row) {
				array_push($html, '<li><a href="/categories/'.$row['idcategory'].'">'.$row['descategory'].'</a></li>');

			}
			

			file_put_contents($_SERVER['DOCUMENT_ROOT'] . DIRECTORY_SEPARATOR . "views" . DIRECTORY_SEPARATOR . "categories-menu.html", implode('',$html));//salvar o arquivo html c o conteudo do array html transformado em texto
		}

		public function getProducts($related = true)
		{
			$sql = new Sql();

			if ($related === true)
			{
				return $sql->select("SELECT * 
								FROM tb_products 
								WHERE idproduct IN ( SELECT a.idproduct
													   FROM tb_products a
												 INNER JOIN tb_productscategories b
														 ON b.idproduct = a.idproduct
													  WHERE b.idcategory = :idcategory);",[
													  	'idcategory'=>$this->getidcategory()
							]);

			}else
			{
				return $sql->select("SELECT * 
								FROM tb_products 
								WHERE idproduct NOT IN ( SELECT a.idproduct
														   FROM tb_products a
													 INNER JOIN tb_productscategories b
															 ON b.idproduct = a.idproduct
														  WHERE b.idcategory = :idcategory);",[
														  	'idcategory'=>$this->getidcategory()
							]);

			}
		}

		public function getProductsPage($page = 1, $itemsPerPage = 3)//função para paginação - qual a pagina e quantos itens por pagina
		{

			$start = ($page - 1) * $itemsPerPage;

			$sql = new Sql();

			$results = $sql->select("

							SELECT SQL_CALC_FOUND_ROWS * FROM tb_products a 
							INNER JOIN tb_productscategories b ON a.idproduct = b.idproduct
							INNER JOIN tb_categories c ON c.idcategory = b.idcategory
							WHERE c.idcategory = :idcategory
						    LIMIT $start, $itemsPerPage;

						", [
							'idcategory'=>$this->getidcategory()

						]);

			$resultTotal = $sql->select("SELECT FOUND_ROWS() AS nrtotal");

			return [
					'data'=>Product::checkList($results), //produtos da pagina
					'total'=>(int)$resultTotal[0]["nrtotal"],//total de produtos
					'pages'=>ceil($resultTotal[0]["nrtotal"] / $itemsPerPage) //qto cada pagina tera de produtos
				   ];//ceil - converte arredondando para cima

		}
		
		
		public function addProduct(Product $product)
		{
			$sql = new Sql();

			$sql->query("INSERT INTO tb_productscategories (idcategory, idproduct) VALUES(:idcategory, :idproduct)", [
				':idcategory'=>$this->getidcategory(),
				':idproduct'=>$product->getidproduct()
			]);
		}

		public function removeProduct(Product $product)
		{
			$sql = new Sql();

			$sql->query("DELETE FROM tb_productscategories WHERE idcategory = :idcategory AND idproduct = :idproduct", [
				':idcategory'=>$this->getidcategory(),
				':idproduct'=>$product->getidproduct()
			]);
		}
}

?>