<?php

function formatPrice(float $vlprice)//função para formatar preço
{
	return number_format($vlprice, 2, ",", ".");
}

?>