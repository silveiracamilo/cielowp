<?php

/*
Class Gateway
Author Camilo da Silveira
Site silveiracamilo.com.br
*/

namespace CieloWP\Gateway;

use CieloWP\Order\Order;

use Cielo\API30\Merchant;
use Cielo\API30\Ecommerce\Environment;
use Cielo\API30\Ecommerce\Sale;
use Cielo\API30\Ecommerce\CieloEcommerce;

class Gateway
{
	const TYPE_CREDIT_CARD = "credito";
	const TYPE_DEBIT_CARD = "debito";
	const TYPE_BANK_SLIP = "boleto";

	protected $order;

	protected $environment;
	protected $merchant;

	protected $sale;
	protected $customer;
	protected $payment;

	public function __construct(Order $order)
	{
		$this->order = $order;

		// Configure o ambiente
        if($this->order->app_env=='sandbox')
        	$this->environment = Environment::sandbox();
        else 
        	$this->environment = Environment::production();

        // Configure seu merchant
        $this->merchant = new Merchant($this->order->merchant_id, $this->order->merchant_key);
	}

	public function process_payment() 
	{
		// Crie um a instância de Sale informando o ID do pedido na loja
        $this->sale = new Sale($this->order->id);

        // Crie uma instância de Customer informando o nome do cliente
        $this->customer = $this->sale->customer($this->order->name);

        // Crie uma instância de Payment informando o valor do pagamento
        $this->payment = $this->sale->payment($this->order->value);
	}

	public function check_payment_done($paymentId)
    {
		$this->sale = (new CieloEcommerce($this->merchant, $this->environment))->getSale($paymentId);

		$status = $this->sale->getPayment()->getStatus();
		$returnMessage = $this->sale->getPayment()->getReturnMessage();

		return ["status"=>$status,
				"returnMessage"=>$returnMessage];
    }
}