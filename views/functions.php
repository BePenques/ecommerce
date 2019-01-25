<?php

use \Hcode\Model;

use \Hcode\Model\User;

function formatPrice(float $vlprice)//função para formatar preço
{
	return number_format($vlprice, 2, ",", ".");
}

function checkLogin($inadmin = true)
{
	return User::checkLogin($inadmin);
}

function getUserName()
{
	$user = User::getFromSession();

	return $user->getdesperson();
}

?>