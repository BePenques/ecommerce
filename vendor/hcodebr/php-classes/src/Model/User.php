<?php

namespace Hcode\Model;

use \Hcode\DB\Sql;

use \Hcode\Model;

class User extends Model{

	const SESSION = "User";

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
		
}

?>