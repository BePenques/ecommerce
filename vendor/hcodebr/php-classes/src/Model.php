<?php

namespace Hcode;

class Model {

	private $values = [];//ex: todos os dados do usuario

	public function __call($name, $args)//args - argumentos passados
	{
		$method = substr($name, 0, 3); //a partir da posição 0 me traga 3 posiçoes - verifica se é get ou set que quer
		$fieldname = substr($name, 3, strlen($name));//pega qual o campo escolhido

		switch($method)
		{
			case "get":
				return (isset($this->values[$fieldname]))?$this->values[$fieldname] : NULL;
			break;

			case "set":
				$this->values[$fieldname] = $args[0];
			break;

		}
	}


	public function setData($data = array())
	{
		foreach ($data as $key => $value) {

			$this->{"set".$key}($value);
			//tudo que for criado dinamico no php precisa estar entre chaves
		}
	}

	public function getValues()
	{
		return $this->values;

	}
}

?>