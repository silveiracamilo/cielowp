<?php

/*
Class CieloLog
Author Camilo da Silveira
Site silveiracamilo.com.br
*/

namespace CieloWP\Order;

class Order 
{
	public $app_env;
	public $merchant_id;
	public $merchant_key;

	public $id;
	public $name;
	public $value;
	public $cvv;
	public $brand;
	public $expiration_date;
	public $card_number;	

	public $return_url;	

	public function __construct($app_env, $merchant_id, $merchant_key, $value=null, $id=null, $gateway_type=null, $return_url=null)
	{
		$this->app_env = $app_env;
		$this->merchant_id = $merchant_id;
		$this->merchant_key = $merchant_key;		

		//multiplar por 100 para converter para centavos, valor tem que ser enviado em centavos
        $valor = $value*100;

		$this->gateway_type = $gateway_type;
        $this->id = date("YmdHis").$id;
		$this->value = $valor;
		
		if($_POST['payment_method']=='cielo_credit'){
			$this->name = $_POST['credit_card_holder_name'];
			$this->cvv = $_POST['credit_card_cvc'];
			$this->expiration_date = $_POST['credit_card_expiry'];
			$this->card_number = str_replace(".", "", $_POST['credit_card_number']);
			$this->brand = $_POST['credit_card_brand'];
		} else {
			$this->name = $_POST['debit_card_holder_name'];
			$this->cvv = $_POST['debit_card_cvc'];
			$this->expiration_date = $_POST['debit_card_expiry'];
			$this->card_number = str_replace(".", "", $_POST['debit_card_number']);
			$this->brand = $_POST['debit_card_brand'];
		}		
		
		$this->return_url = $return_url;
	}
}