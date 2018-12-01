<?php

namespace Hcode\Model;

use \Hcode\DB\Sql;

use \Hcode\Model;

use \Hcode\Mailer;

class Category extends Model{


		public static function listAll()
		{

			$sql = new Sql();

			return $sql->select("SELECT * FROM tb_users a INNER JOIN tb_persons b USING(idperson) ORDER BY b.desperson");
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

		
		
}

?>