<?php

/*
Class CieloWP
Author Camilo da Silveira
Site silveiracamilo.com.br
*/

namespace CieloWP;

use CieloWP\Gateway\Gateway;
use CieloWP\Gateway\GatewayCreditCard;
use CieloWP\Gateway\GatewayDebitCard;

class CieloWP
{
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
		return (new GatewayCreditCard($order))->process_payment();
	}

	static protected function process_payment_debit_card($order)
	{
		return (new GatewayDebitCard($order))->process_payment();
	}

	static public function check_payment_done($order, $paymentId)
	{
		return (new Gateway($order))->check_payment_done($paymentId);
	}
}