# Developer Cielo API-3.0-PHP

SDK API-3.0 PHP

## Principais recursos

* [x] Pagamentos por cartão de crédito.
* [x] Pagamentos por cartão de débito.

## Limitações

Por envolver a interface de usuário da aplicação, o SDK funciona apenas como um framework para criação das transações. Nos casos onde a autorização é direta, não há limitação; mas nos casos onde é necessário a autenticação ou qualquer tipo de redirecionamento do usuário, o desenvolvedor deverá utilizar o SDK para gerar o pagamento e, com o link retornado pela Cielo, providenciar o redirecionamento do usuário.

## Dependências

* PHP >= 5.6

## Instalando o SDK

Se já possui um arquivo `composer.json`, basta adicionar a seguinte dependência ao seu projeto:

```json
"require": {
    "cielowp/api-3.0-php": "^1.0"
}
```

Com a dependência adicionada ao `composer.json`, basta executar:

```
composer install
```

Alternativamente, você pode executar diretamente em seu terminal:

```
composer require "developercielo/api-3.0-php"
```

## Produtos e Bandeiras suportadas e suas constantes

```php
<?php
require 'vendor/autoload.php';

use Cielo\API30\Ecommerce\CreditCard;
```

| Bandeira         | Constante              | Crédito à vista | Crédito parcelado Loja | Débito | Voucher |
|------------------|------------------------|-----------------|------------------------|--------|---------|
| Visa             | CreditCard::VISA       | Sim             | Sim                    | Sim    | *Não*   |
| Master Card      | CreditCard::MASTERCARD | Sim             | Sim                    | Sim    | *Não*   |
| American Express | CreditCard::AMEX       | Sim             | Sim                    | *Não*  | *Não*   |
| Elo              | CreditCard::ELO        | Sim             | Sim                    | *Não*  | *Não*   |
| Diners Club      | CreditCard::DINERS     | Sim             | Sim                    | *Não*  | *Não*   |
| Discover         | CreditCard::DISCOVER   | Sim             | *Não*                  | *Não*  | *Não*   |
| JCB              | CreditCard::JCB        | Sim             | Sim                    | *Não*  | *Não*   |
| Aura             | CreditCard::AURA       | Sim             | Sim                    | *Não*  | *Não*   |

## Utilizando o SDK

Para criar um pagamento simples com cartão de crédito com o SDK, basta fazer:

### Criando um pagamento com cartão de crédito

```php
<?php
require 'vendor/autoload.php';

use CieloWP\CieloWP;
use CieloWP\Gateway\Gateway;
use CieloWP\Order\Order;

$order_cielo = new Order($this->get_app_env(), $this->merchant_id, $this->merchant_key,
						 $order->get_total(), $order_id, Gateway::TYPE_CREDIT_CARD);
$returnCielo = CieloWP::process_payment($order_cielo);

//return
//$returnCielo['success']
//$returnCielo['error']
```

### Criando um pagamento com cartão de crédito

```php
<?php
require 'vendor/autoload.php';

use CieloWP\CieloWP;
use CieloWP\Gateway\Gateway;
use CieloWP\Order\Order;

$order_cielo = new Order($this->get_app_env(), $this->merchant_id, $this->merchant_key, $order->get_total(),
                         $order_id, Gateway::TYPE_DEBIT_CARD, $this->get_api_return_url($order));
$returnCielo = CieloWP::process_payment($order_cielo);

//returno
//$returnCielo['success']
//$returnCielo['error']
```