<?php

/*
Class Gateway
Author Camilo da Silveira @ Unius
Site silveiracamilo.com.br
*/

namespace CieloWP\Gateway;

use Cielo\API30\Merchant;
use Cielo\API30\Ecommerce\Environment;
use Cielo\API30\Ecommerce\Sale;
use Cielo\API30\Ecommerce\CieloEcommerce;

class Gateway
{
	const TYPE_CREDIT_CARD = "credito";
	const TYPE_DEBIT_CARD = "debito";
	const TYPE_BANK_SLIP = "boleto";

	protected $app_env;
	protected $appUrl;
	protected $merchantId;
	protected $merchantKey;

	protected $environment;
	protected $merchant;

	protected $sale;
	protected $customer;
	protected $payment;

	protected $error_msgs = ['1'=> 'Transação não autorizada. Transação referida.',
							 '01'=> 'Transação não autorizada. Transação referida.',
							 '05'=> 'Não Autorizada',
					         '57'=> 'Cartão Expirado',
					         '78'=> 'Cartão Bloqueado',
					         '99'=> 'Tempo Esgotado',
					         '77'=> 'Cartão Cancelado',
					         '70'=> 'Problemas com o Cartão de Crédito'];

	public function __construct($app_env, $appUrl, $merchantId, $merchantKey)
	{
		$this->app_env = $app_env;
		$this->appUrl = $appUrl;
		$this->merchantId = $merchantId;
		$this->merchantKey = $merchantKey;

		// Configure o ambiente
        if($this->app_env=='sandbox')
        	$this->environment = Environment::sandbox();
        else 
        	$this->environment = Environment::production();

        //echo "<br>getApiUrl():".$this->environment->getApiUrl()."<br>";

        // Configure seu merchant
        $this->merchant = new Merchant($this->merchantId, $this->merchantKey);
	}

	public function process_payment( $order ) 
	{
		// Crie um a instância de Sale informando o ID do pedido na loja
        $this->sale = new Sale($order->id);

        // Crie uma instância de Customer informando o nome do cliente
        $this->customer = $this->sale->customer($order->name);

        // Crie uma instância de Payment informando o valor do pagamento
        $this->payment = $this->sale->payment($order->value);
	}

	public function check_payment_done($paymentId)
    {
		$this->sale = (new CieloEcommerce($this->merchant, $this->environment))->getSale($paymentId);

		$status = $this->sale->getPayment()->getStatus();
		$returnMessage = $this->sale->getPayment()->getReturnMessage();

		return ["status"=>$status,
				"returnMessage"=>$returnMessage];

		if($status && ($status==1 || $status==2)) {
            return true;
        } 

		return false;
    }
}