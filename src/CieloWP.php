<?php

/*
Class CieloWP
Author Camilo da Silveira @ Unius
Site silveiracamilo.com.br
*/

namespace CieloWP;

use CieloWP\Gateway\Gateway;
use CieloWP\Gateway\GatewayCreditCard;
use CieloWP\Gateway\GatewayDebitCard;

class CieloWP
{
	const APP_ENV = "sandbox";
	const MERCHANT_ID = "****";
	const MERCHANT_KEY = "******";
	const MERCHANT_ID_SANDBOX = "5fcc9ccd-b4b7-4e1c-8d9c-0fe79933018d";
	const MERCHANT_KEY_SANDBOX = "3PztoePya9FcJ0B1HJqJUFjgODDw6DZkH3gr4B+gPwo=";

	static public function process_payment($order)
	{
		if($order->gateway_type==Gateway::TYPE_CREDIT_CARD){
			return self::process_payment_credit_card($order);
		} else if($order->gateway_type==Gateway::TYPE_DEBIT_CARD){
			return self::process_payment_debit_card($order);
		} 
	}

	static protected function process_payment_credit_card($order)
	{		
		return (new GatewayCreditCard(CieloWP::APP_ENV, get_site_url(), CieloWP::get_merchant_id(), CieloWP::get_merchant_key()))->process_payment($order);
	}

	static protected function process_payment_debit_card($order)
	{
		return (new GatewayDebitCard(CieloWP::APP_ENV, get_site_url(), CieloWP::get_merchant_id(), CieloWP::get_merchant_key()))->process_payment($order);
	}

	static public function check_payment_done($paymentId)
	{
		return (new Gateway(CieloWP::APP_ENV, get_site_url(), CieloWP::get_merchant_id(), CieloWP::get_merchant_key()))->check_payment_done($paymentId);
	}

	static protected function get_merchant_id()
	{
		if(CieloWP::APP_ENV=="sandbox")
			return CieloWP::MERCHANT_ID_SANDBOX;
		else
			return CieloWP::MERCHANT_ID;
	}

	static protected function get_merchant_key()
	{
		if(CieloWP::APP_ENV=="sandbox")
			return CieloWP::MERCHANT_KEY_SANDBOX;
		else
			return CieloWP::MERCHANT_KEY;
	}
}