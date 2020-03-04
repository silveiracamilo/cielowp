<?php

/*
Class GatewayCreditCard
Author Camilo da Silveira
Site silveiracamilo.com.br
*/

namespace CieloWP\Gateway;

use CieloWP\Gateway\Gateway;
use Cielo\API30\Ecommerce\CieloEcommerce;
use Cielo\API30\Ecommerce\Request\CieloRequestException;

class GatewayCreditCard extends Gateway
{
	public function process_payment() 
	{
		parent::process_payment();
        
		// Crie uma instância de Credit Card utilizando os dados de teste
        // esses dados estão disponíveis no manual de integração
        $this->payment->creditCard($this->order->cvv, $this->order->brand)
                ->setExpirationDate($this->order->expiration_date)
                ->setCardNumber($this->order->card_number)
                ->setHolder($this->order->name);
                //->setSaveCard(true);

        // Crie o pagamento na Cielo
        try {
            // Configure o SDK com seu merchant e o ambiente apropriado para criar a venda
            // $this->sale = (new CieloEcommerce($this->merchant, $this->environment, new CieloLog()))->createSale($this->sale);
            $this->sale = (new CieloEcommerce($this->merchant, $this->environment))->createSale($this->sale);

            // $returnCode = $this->sale->getPayment()->getReturnCode();
            $returnMessage = $this->sale->getPayment()->getReturnMessage();
            $status = $this->sale->getPayment()->getStatus();

            if($status==0 || $status==1 || $status==2)
            {
                // Com a venda criada na Cielo, já temos o ID do pagamento, TID e demais
                // dados retornados pela Cielo
                $paymentId = $this->sale->getPayment()->getPaymentId();
                $tid = $this->sale->getPayment()->getTid();                

	            // Com o ID do pagamento, podemos fazer sua captura, se ela não tiver sido capturada ainda
	            // $this->sale = (new CieloEcommerce($this->merchant, $this->environment, new CieloLog()))->captureSale($paymentId, $this->order->value, 0);
	            $this->sale = (new CieloEcommerce($this->merchant, $this->environment))->captureSale($paymentId, $this->order->value, 0);
                
	            return ['success'=>[ 'payment_id'=>$paymentId,
		                            'tid' => $tid,
                                    'bank_slip_url'=>NULL],
                        'error'=>NULL];
            } else {
                return ['error'=>$returnMessage, 'success'=>NULL];
            }
        } catch (CieloRequestException $e) {
            // Em caso de erros de integração, podemos tratar o erro aqui.
            // os códigos de erro estão todos disponíveis no manual de integração.
            $cieloError = $e->getCieloError();
            $error = $cieloError ? $cieloError->getMessage() : $e->getMessage();

            return ['error'=>$error, 'success'=>NULL];
        }
	}
}