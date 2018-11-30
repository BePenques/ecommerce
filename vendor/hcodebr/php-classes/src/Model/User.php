<?php

namespace Hcode\Model;

use \Hcode\DB\Sql;

use \Hcode\Model;

use \Hcode\Mailer;

class User extends Model{

	const SESSION = "User";
	const SECRET = "HcodePhp7_secret";

	public static function login($login, $password)
	{
		$sql =  new Sql();
		$results = $sql->select("SELECT * FROM tb_users WHERE deslogin = :LOGIN", array(
			":LOGIN"=>$login
		));

		if(count($results) === 0)
		{
			throw new \Exception("Usuário inexistente ou senha invalida.");
			
		}

		$data = $results[0];

		if (password_verify($password, $data["despassword"]) === true)//compara o hash da senha informada com a do banco
		{
			$user = new User();

			$user->setdata($data);

			//CRIAR UMA SESSÃO
			$_SESSION[User::SESSION] = 
			$user->getValues();//metodo para pegar os dados do usuario que a sessão pertence - getdados
			return $user;

		}
		else
		{
			throw new \Exception("Usuário inexistente ou senha invalida.");
		}

	}



		public static function verifyLogin($inadmin = true)//verifica se esta logado
		{
			if (   
				   !isset($_SESSION[User::SESSION])//se a sessão n estiver definida)
				   ||
				   !$_SESSION[User::SESSION] //se a sessão for falsa
				   || 
				   !(int)$_SESSION[User::SESSION]["iduser"] > 0 //verifica id do usuario
				   || 
				   (bool)$_SESSION[User::SESSION]["inadmin"] !== $inadmin //verifica se ele tem permissao adm para logar aqui
				)
			{
				header("Location: /admin/login");
				exit;
			}
			
		}

		public static function logout()
		{
			$_SESSION[User::SESSION] = null;
		}

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

			$results = $sql->select("CALL sp_users_save(:desperson, :deslogin, :despassword, :desemail, :nrphone, :inadmin)", array(

				":desperson"=>$this->getdesperson(),
				":deslogin"=>$this->getdeslogin(),
				":despassword"=>$this->getdespassword(),
				":desemail"=>$this->getdesemail(),
				":nrphone"=>$this->getnrphone(),
				":inadmin"=>$this->getinadmin()
			));

			$this->setData($results[0]);
		}

		public function get($iduser)
		{
			$sql = new Sql();

		$results =	$sql->select("SELECT * FROM tb_users a INNER JOIN tb_persons b USING(idperson) WHERE a.iduser = :iduser", array(
					":iduser"=>$iduser

			));

			$this->setData($results[0]);//cria os setters
		}

		public function update()
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

			$results = $sql->select("CALL sp_usersupdate_save(:iduser, :desperson, :deslogin, :despassword, :desemail, :nrphone, :inadmin)", array(
				":iduser"=>$this->getiduser(),
				":desperson"=>$this->getdesperson(),
				":deslogin"=>$this->getdeslogin(),
				":despassword"=>$this->getdespassword(),
				":desemail"=>$this->getdesemail(),
				":nrphone"=>$this->getnrphone(),
				":inadmin"=>$this->getinadmin()
			));

			$this->setData($results[0]);

		
		}

		public function delete()
		{
			$sql = new Sql();

			$sql->query("CALL sp_users_delete(:iduser)", array(":iduser"=>$this->getiduser()

			));
		}

		public static function getForgot($email, $inadmin = true)
		{
			//verificar se email esta cadastrado
			$sql = new Sql();

			$results = $sql->select("
				SELECT *
				FROM tb_persons a
				INNER JOIN tb_users b USING(idperson)
				WHERE a.desemail = :email;", array(
					":email"=>$email
				));

			//valida se o email existe

			if (count($results) === 0)
			{
				throw new \Exception("Não foi possivel recuperar a senha", 1);
			}else
			{
				$data = $results[0];//iduser

				$results2 = $sql->select("CALL sp_userspasswordsrecoveries_create(:iduser, :desip)", array(
					":iduser"=>$data["iduser"],
					":desip"=>$_SERVER["REMOTE_ADDR"]
				));

				if (count($results2) === 0)
				{
					var_dump($results2);
					throw new \Exception("Não foi possivel recuperar a senha");
					
				}else
				{
					$dataRecovery = $results2[0];

					$iv = random_bytes(openssl_cipher_iv_length('aes-256-cbc'));
             		$code = openssl_encrypt($dataRecovery['idrecovery'], 'aes-256-cbc', User::SECRET, 0, $iv);
             		$result = base64_encode($iv.$code);
					
					 if ($inadmin === true) {
                 		$link = "http://www.hcodecommerce.com.br/admin/forgot/reset?code=$result";
             		} else {
                 		$link = "http://www.hcodecommerce.com.br/forgot/reset?code=$result";
             } 

					$mailer = new Mailer($data["desemail"], $data["desperson"], " Redefinir senha da Hcode Store ", "forgot", array(
						"name"=>$data["desperson"],
						"link"=>$link
					));

					$mailer->send();
					return $data;
				}
			}
		}

		public static function valideForgotDecrypt($result)
		{
			//descriptografar para descobir o usuario e etc..

			$result = base64_decode($result);
     		$code = mb_substr($result, openssl_cipher_iv_length('aes-256-cbc'), null, '8bit');
     		$iv = mb_substr($result, 0, openssl_cipher_iv_length('aes-256-cbc'), '8bit');
     		$idrecovery = openssl_decrypt($code, 'aes-256-cbc', User::SECRET, 0, $iv);

			$sql = new Sql();

			$results = $sql->select
			(
				"SELECT * FROM tb_userspasswordsrecoveries a
				INNER JOIN tb_users b USING(iduser)
				INNER JOIN tb_persons c USING(idperson)
				WHERE a.idrecovery = :idrecovery
  					AND a.dtrecovery  IS NULL
  					AND DATE_ADD(a.dtregister, interval 1 hour) >= NOW();", array(
  						":idrecovery"=>$idrecovery
			));

			if (count($results) === 0)
			{
				//var_dump($idrecovery);
				throw new \Exception("Não foi possivel recuperar a senha");
				
			}else
			{
				return $results[0];
			}
		}

		public static function setForgotUsed($idrecovery)
		{
			$sql = new Sql();

			$sql->query("UPDATE tb_userspasswordsrecoveries SET dtrecovery = NOW() where idrecovery = :idrecovery", array(
				":idrecovery"=>$idrecovery
			));
		}

		public function setPassword($password)
		{
			$sql = new Sql();

			$sql->query("UPDATE tb_users SET despassword = :password WHERE iduser = :iduser", array(
				":password"=>$password,
				":iduser"=>$this->getiduser()
			));
		}
}

?>