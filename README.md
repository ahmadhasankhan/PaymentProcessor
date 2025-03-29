## About Payment Processor

It's a simple payment processor application which can process your card and Crypto Payment

## Prerequisites

Make sure your have PHP 8.4, MySql and Laravel dependencies installed in your machine.

## How to run
1. `$ php artisan migrate`
2. `php artisan db:seed --class=MerchantSeeder`
3. `php artisan serve`

Once dev server is up, you should be able to access the app on http://127.0.0.1:8000

On the homepage you will see the **Payment Form**, fill the basic details and choose the payment mode:

#### For Visa and Master card your payment will be processed by Stripe.
1. Go to http://127.0.0.1:8000 and test with:
2. Enter Basic details
3. For cards, use Stripe test cards:
```aiignore
4111111111111111, Exp: 12/30, CVV: 123

4242424242424242, Exp: 02/30, CVV: 123
```
Feel free to pick any card from here https://docs.stripe.com/testing?locale=en-GB#cards

4. For Crypto use Fake Wallet & Hash

```aiignore
 Wallet: 0xSuccessUSDTAddressERC20
 Hash: crypto_tx_123456
```




