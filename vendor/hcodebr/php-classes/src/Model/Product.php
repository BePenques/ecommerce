<?php

namespace Hcode\Model;

use \Hcode\DB\Sql;

use \Hcode\Model;

use \Hcode\Mailer;

class Product extends Model{


		public static function listAll()
		{

			$sql = new Sql();

			return $sql->select("SELECT * FROM tb_products  ORDER BY desproduct");
		}

		public static function checkList($list)
		{
			foreach ($list as &$row) {

				$p = new Product();
				$p->setData($row);//cria os setters, cria o obejto
				$row = $p->getValues();//verifica se existe a foto
				
			}

			return $list;//retorna o array list com os dados de cada produto já formatado
		}

	
		
		public function save()//função para salvar os dados no banco
		{

			$sql = new Sql();
			

			$results = $sql->select("CALL sp_products_save(:idproduct,
			                     :desproduct,
			                     :vlprice, 
			                     :vlwidth, 
			                     :vlheight, 
			                     :vllength,
			                     :vlweight,
			                     :desurl)", array(

				":idproduct"=>$this->getidproduct(),
				":desproduct"=>$this->getdesproduct(),
				":vlprice"=>$this->getvlprice(),
				":vlwidth"=>$this->getvlwidth(),
				":vlheight"=>$this->getvlheight(),
				":vllength"=>$this->getvllength(),
				":vlweight"=>$this->getvlweight(),
				":desurl"=>$this->getdesurl()
				
			));


			
			$this->setData($results[0]);



		
		}



		public function get($idproduct)
		{
			$sql = new Sql();

			$results = $sql->select("SELECT * FROM tb_products WHERE idproduct = :idproduct", [
				':idproduct'=>$idproduct
			]);

			$this->setData($results[0]); //criar os setters(colocar os dados dentro do obj)

		}

		public function delete()
		{
			$sql = new Sql();

			$sql->query("DELETE FROM tb_products WHERE idproduct = :idproduct", [
				":idproduct"=>$this->getidproduct()//pegar do proprio objeto
			]);

			
		}

		public function checkPhoto()
		{
			if(file_exists($_SERVER['DOCUMENT_ROOT'] . DIRECTORY_SEPARATOR .
				"resources" . DIRECTORY_SEPARATOR . 
				"site" . DIRECTORY_SEPARATOR .
				"img" . DIRECTORY_SEPARATOR .
				
				$this->getidproduct() . ".jpg"
		)) {
				$url = "/resources/site/img/" . $this->getidproduct() . ".jpg";
			}else
			{
				$url =  "/resources/site/img/product.jpg";
			}

			return $this->setdesphoto($url);
		}

		public function getValues()
		{
			$this->checkPhoto();

			$values = parent::getValues();//vai fazer oque o pai faz

			return $values;
		}


		public function setPhoto($file)
		{
			//detectar qual a extensao do arquivo
			$extension = explode('.', $file['name']);
			$extension = end($extension);

			switch ($extension) {
				case 'jpg':
				
				case 'jpeg':
					$image = imagecreatefromjpeg($file["tmp_name"]);

					break;

				case 'gif':
					$image = imagecreatefromgif($file["tmp_name"]);

					break;

				case 'png':
					$image = imagecreatefrompng($file["tmp_name"]);

					break;
			}

			$dist = $_SERVER['DOCUMENT_ROOT'] . DIRECTORY_SEPARATOR .
				"resources" . DIRECTORY_SEPARATOR . 
				"site" . DIRECTORY_SEPARATOR .
				"img" . DIRECTORY_SEPARATOR .
				
				$this->getidproduct() . ".jpg" ;

			imagejpeg($image, $dist);//a imagem e o caminho da onde quer salvar ela

			imagedestroy($image);

			$this->checkPhoto();
		}
		
		


		public function getFromUrl($desurl){//tra o produto a partir da URL informada

			$sql = new Sql();

			$rows = $sql->select("SELECT * FROM tb_products WHERE desurl = :desurl", [
				':desurl'=>$desurl
			]);

			$this->setData($rows[0]);//colocar a informação num objeto produto

		}

		public function getCategories()//metoro para trazer a categoria do produto
		{

			$sql = new Sql();

			return $sql->select("
				SELECT * FROM tb_categories a INNER JOIN tb_productscategories b ON a.idcategory = b.idcategory WHERE b.idproduct = :idproduct", [
					':idproduct'=>$this->getidproduct()
				]);
		}

		public static function getPage($page = 1, $itemsPerPage = 5)//função para paginação - qual a pagina e quantos itens por pagina
		{

			$start = ($page - 1) * $itemsPerPage;

			$sql = new Sql();

			$results = $sql->select("

							SELECT SQL_CALC_FOUND_ROWS * 
							 FROM tb_products  
							ORDER BY desproduct
						    LIMIT $start, $itemsPerPage;

						");

			$resultTotal = $sql->select("SELECT FOUND_ROWS() AS nrtotal");

			return [
					'data'=>$results, //produtos da pagina
					'total'=>(int)$resultTotal[0]["nrtotal"],//total de produtos
					'pages'=>ceil($resultTotal[0]["nrtotal"] / $itemsPerPage) //qto cada pagina tera de produtos
				   ];//ceil - converte arredondando para cima

		}

		public static function getPageSearch($search, $page = 1, $itemsPerPage = 5)//função para paginação - qual a pagina e quantos itens por pagina
		{

			$start = ($page - 1) * $itemsPerPage;

			$sql = new Sql();

			$results = $sql->select("

							SELECT SQL_CALC_FOUND_ROWS * 
						      FROM tb_products  
							WHERE desproduct LIKE :search
							ORDER BY desproduct 
						    LIMIT $start, $itemsPerPage;

						", [
							':search'=>'%'.$search.'%'
						]);

			$resultTotal = $sql->select("SELECT FOUND_ROWS() AS nrtotal");

			return [
					'data'=>$results, //produtos da pagina
					'total'=>(int)$resultTotal[0]["nrtotal"],//total de produtos
					'pages'=>ceil($resultTotal[0]["nrtotal"] / $itemsPerPage) //qto cada pagina tera de produtos
				   ];//ceil - converte arredondando para cima

		}
}		

?>