<?php

/*
Class GatewayCreditCard
Author Camilo da Silveira @ Unius
Site silveiracamilo.com.br
*/

namespace CieloWP\Gateway;

use CieloWP\Gateway\Gateway;
use Cielo\API30\Ecommerce\CieloEcommerce;
use Cielo\API30\Ecommerce\Request\CieloRequestException;

class GatewayCreditCard extends Gateway
{
	public function process_payment( $order ) 
	{
		parent::process_payment($order);
        
		// Crie uma instância de Credit Card utilizando os dados de teste
        // esses dados estão disponíveis no manual de integração
        $this->payment->creditCard($order->cvv, $order->brand)
                ->setExpirationDate($order->expiration_date)
                ->setCardNumber($order->card_number)
                ->setHolder($order->name);
                //->setSaveCard(true);

        // Crie o pagamento na Cielo
        try {
            // Configure o SDK com seu merchant e o ambiente apropriado para criar a venda
            // $this->sale = (new CieloEcommerce($this->merchant, $this->environment, new CieloLog()))->createSale($this->sale);
            $this->sale = (new CieloEcommerce($this->merchant, $this->environment))->createSale($this->sale);

            $returnCode = $this->sale->getPayment()->getReturnCode();
            $returnMessage = $this->sale->getPayment()->getReturnMessage();
            $status = $this->sale->getPayment()->getStatus();

            // echo "status:".$status."<br><br>";
            // echo "returnCode:".$returnCode."<br>";
            // echo "returnMessage:".$returnMessage."<br><br>";
            // echo "sale:<br><br>";
            // var_export($this->sale);

            if($status==0 || $status==1 || $status==2)
            {
                // Com a venda criada na Cielo, já temos o ID do pagamento, TID e demais
                // dados retornados pela Cielo
                $paymentId = $this->sale->getPayment()->getPaymentId();
                $tid = $this->sale->getPayment()->getTid();
                
                // echo "paymentId:".$paymentId."<br><br>";
                // echo "tid:".$tid."<br><br>";
                
                
	            // O token gerado pode ser armazenado em banco de dados para vendar futuras
	            //$token = $this->sale->getPayment()->getCreditCard()->getCardToken();
                $token = "";
                //echo "token:".$token."<br><br>";

	            // Com o ID do pagamento, podemos fazer sua captura, se ela não tiver sido capturada ainda
	            // $this->sale = (new CieloEcommerce($this->merchant, $this->environment, new CieloLog()))->captureSale($paymentId, $order->value, 0);
	            $this->sale = (new CieloEcommerce($this->merchant, $this->environment))->captureSale($paymentId, $order->value, 0);

	            // echo "captured sale:<br><br>";
                // var_export($this->sale);
                
	            return ['success'=>[ 'payment_id'=>$paymentId,
		                            'tid' => $tid,
                                    'bank_slip_url'=>NULL,
		                            'card_token' => $token],
                        'error'=>NULL];
            } else {
                if(!empty($this->error_msgs[$returnCode])) {
                    $error_msg = $this->error_msgs[$returnCode]; 
                } else {
                    $error_msg = $returnMessage;
                }
                // echo $error_msg;

                return ['error'=>$error_msg, 'success'=>NULL];
            }
        } catch (CieloRequestException $e) {
            // Em caso de erros de integração, podemos tratar o erro aqui.
            // os códigos de erro estão todos disponíveis no manual de integração.
            $cieloError = $e->getCieloError();
            $error = $cieloError ? $cieloError->getMessage() : $e->getMessage();

            // echo "error:".$error;

            return ['error'=>$error, 'success'=>NULL];
        }
	}
}