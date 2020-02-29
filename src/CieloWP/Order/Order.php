<?php

namespace CieloWP\Order;

class Order 
{
	public $id;
	public $name;
	public $value;
	public $cvv;
	public $brand;
	public $expiration_date;
	public $card_number;

	public function __construct($value, $id, $gateway_type, $brand)
	{
		//multiplar por 100 para converter para centavos, valor tem que ser enviado em centavos
        $valor = $value*100;

		$this->gateway_type = $gateway_type;
        $this->id = date("YmdHis").$id;
        $this->name = $_POST['cielo_credit_holder_name'];
        $this->value = $valor;
        $this->cvv = $_POST['cielo_credit_cvc'];
        $this->brand = $brand;
        $this->expiration_date = $_POST['cielo_credit_expiry'];
        $this->card_number = str_replace(".", "", $_POST['cielo_credit_number']);
	}
}