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
				":vlprice   "=>$this->getvlprice(),
				":vlwidth   "=>$this->getvlwidth(),
				":vlheight  "=>$this->getvlheight(),
				":vllength  "=>$this->getvllength(),
				":vlweight  "=>$this->getvlweight(),
				":desurl    "=>$this->getdesurl()
				
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

		
		
}

?>