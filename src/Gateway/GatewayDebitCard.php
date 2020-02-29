<?php

/*
Class GatewayDebitCard
Author Camilo da Silveira @ Unius
Site silveiracamilo.com.br
*/

namespace CieloWP\Gateway;

use CieloWP\Gateway\Gateway;
use CieloWP\Log\CieloLog;

use Cielo\API30\Ecommerce\CieloEcommerce;
use Cielo\API30\Ecommerce\Payment;
use Cielo\API30\Ecommerce\Request\CieloRequestException;


class GatewayDebitCard extends Gateway
{
	public function process_payment( $order ) 
	{
		parent::process_payment($order);

		// Defina a URL de retorno para que o cliente possa voltar para a loja
        // após a autenticação do cartão
        $this->payment->setReturnUrl($this->appUrl.'/pagamento/finalizar_debito');
        $this->payment->setCapture(1);
        $this->payment->setAuthenticate(TRUE)
                      ->setType(Payment::PAYMENTTYPE_DEBITCARD);

        // Crie uma instância de Debit Card utilizando os dados de teste
        // esses dados estão disponíveis no manual de integração
        $this->payment->debitCard($order->cvv, $order->brand)
                ->setExpirationDate($order->expiration_date)
                ->setCardNumber($order->card_number)
                ->setHolder($order->name);

        // Crie o pagamento na Cielo
        try {
            // Configure o SDK com seu merchant e o ambiente apropriado para criar a venda
            // $this->sale = (new CieloEcommerce($this->merchant, $this->environment, new CieloLog()))->createSale($this->sale);
            $this->sale = (new CieloEcommerce($this->merchant, $this->environment))->createSale($this->sale);

            $returnCode = $this->sale->getPayment()->getReturnCode();
            $returnMessage = $this->sale->getPayment()->getReturnMessage();
            $returnAuthenticationUrl = $this->sale->getPayment()->getAuthenticationUrl();
            $paymentId = $this->sale->getPayment()->getPaymentId();
            $tid = $this->sale->getPayment()->getTid();
            

            /*echo "returnCode:".$returnCode."<br>";
            echo "returnMessage:".$returnMessage."<br><br>";
            echo "returnAuthenticationUrl:".$returnAuthenticationUrl."<br><br>";
            echo "paymentId:".$paymentId."<br><br>";
            echo "tid:".$tid."<br><br>";
            echo "sale:<br><br>";
            var_export($this->sale);*/

            if(empty($returnAuthenticationUrl) || 
               $returnAuthenticationUrl==NULL){
                return ['error'=>'Error ao autenticar com o banco!', 'success'=>NULL];
            } else {
                return ['success'=>[ 'payment_id'=>$paymentId,
                                     'tid' => $tid,
                                     'returnAuthenticationUrl'=>$returnAuthenticationUrl,
                                     'bank_slip_url'=>NULL,
                                     'card_token' => null ],
                            'error'=>NULL];
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