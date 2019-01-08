<?php

namespace Hcode\Model;

use \Hcode\DB\Sql;

use \Hcode\Model;

use \Hcode\Mailer;

use \Hcode\Model\User;



class Cart extends Model{

	const SESSION = "Cart";

	//metodo para ver se precisa de um carrinho novo ou se já possui um
	public static function getFromSession()
	{
		$cart = new Cart();

		if(isset($_SESSION[Cart::SESSION]) && (int)$_SESSION[Cart::SESSION]['idcart'] > 0){//se a sessão estiver ativa - procura o carrinho

			$cart->get((int)$_SESSION[Cart::SESSION]['idcart']);

		}else{//se ainda não existe um carrinho

			$cart->getFromSessionID();//tenta carregar o carrinho

			if (!(int)$cart->getidcart() > 0)
			{
				$data = [
					'dessessionid'=>session_id()
				];

				if (User::checkLogin(false)){//se retornar true ele esta logado

				$user = User::getFromSession();//traz o usuario

				$data['iduser']  = $user->getiduser();
			}

			$cart->setData($data);

			$cart->save();

			$cart->setToSession();//settar para a sessão

			}

		}

		return $cart;
	}

	public function setToSession()
	{
		$_SESSION[Cart::SESSION] = $this->getValues(); //colocou o carrinho na sessão
	}

	public function getFromSessionID()
	{
		$sql = new Sql();

		$results = $sql->select("SELECT * FROM tb_carts WHERE dessessionid = :dessessionid", [
				':dessessionid'=>session_id()
		]);

		if (count($results) > 0 )
		{
			$this->setData($results[0]);
		}

		
	}


	public function get(int $idcart)
	{
		$sql = new Sql();

		$results = $sql->select("SELECT * FROM tb_carts WHERE idcart = :idcart", [
				'idcart'=>$idcart
		]);

		if (count($results) > 0 )
		{
			$this->setData($results[0]);
		}
	}

	public function save()//procedure que faz insert ou update
	{
		$sql = new Sql();

		$results = $sql->select("CALL sp_carts_save(:idcart, :dessessionid, :iduser, :deszipcode, :vlfreight,  :nrdays)", [
				':idcart'=>$this->getidcart(),
				':dessessionid'=>$this->getdessessionid(),
				':iduser'=>$this->getiduser(),
				':deszipcode'=>$this->getdeszipcode(),
				':vlfreight'=>$this->getvlfreight(),
				':nrdays'=>$this->getnrdays()
		]);

		$this->setData($results[0]);

	}
}

?>