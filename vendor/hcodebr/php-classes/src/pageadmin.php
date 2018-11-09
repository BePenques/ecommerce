<?php

namespace Hcode;

class PageAdmin extends Page{


	public function __construct($opts = array(), $tpl_dir = "/views/admin/")
	{
		parent::__construct($opts, $tpl_dir);//aproveita o codigo construtor da outra pagina - Herança

	}
}

?>