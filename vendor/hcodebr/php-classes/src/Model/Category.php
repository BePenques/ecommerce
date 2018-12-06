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
		}

		
		
}

?>