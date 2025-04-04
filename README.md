## About Payment Processor

It's a simple payment processor application which can process your card and Crypto Payment

## Prerequisites

Make sure your have PHP 8.4, MySql and Laravel dependencies installed in your machine.

## 1. Clone the Project & Install Dependencies

```aiignore
1. git clone git@github.com:ahmadhasankhan/PaymentProcessor.git
2. cd PaymentProcessor
3. composer install
3. php artisan db:seed --class=MerchantSeeder
3. cp .env.example .env
```

## Step 2: Configure Environment Variables
In .env file: and provide the DB details

```aiignore
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=your_db_name
DB_USERNAME=your_db_user
DB_PASSWORD=your_db_pass
```

## Step 3: Set Up Database

```aiignore
php artisan migrate --seed
```
This will create DB, migrate and populate seed data 


## Step 4: Start the Server
```aiignore
php artisan serve
```
Once the server has stated successfully you should be able to access payment page by visiting to http://127.0.0.1:8000

## Step 5: Payment Page
Fill below details and submit
```aiignore
* First Name (required, text input)
* Last Name (required, text input)
* Address (required, text input)
* Zip code (required, text input)
* Country (required, text input)
* Amount (required, numeric input)
* Transaction Type (required, dropdown):
* Select Merchant (required, dropdown):
* Enter Payment details
```
You Should see the response in the json format

### Step 6: Test the APIs (Postman / cURL)

Create Transaction
```aiignore
POST /transaction
Content-Type: application/json

{
  "first_name": "John",
  "last_name": "Doe",
  "address": "123 Street",
  "zip_code": "12345",
  "country": "India",
  "amount": 500,
  "transaction_type": "Deposit",
  "merchant_id": 1,
  "card_number": "4111111111111111",
  "expiry_date": "12/29",
  "cvv": "123"
}
```

Complete Crypto Transaction
```aiignore
POST /transaction/1/complete
Content-Type: application/json

{
  "wallet_address": "test_wallet_1",
  "transaction_hash": "hash_abc123"
}
```

Simulate Card Payment
```aiignore
POST /merchant/card
Content-Type: application/json

{
  "card_number": "4111111111111111",
  "expiry_date": "12/29",
  "cvv": "123"
}
```

Simulate Crypto Payment
```aiignore
POST /merchant/crypto
Content-Type: application/json

{
  "wallet_address": "test_wallet_1",
  "transaction_hash": "hash_abc123"
}
```

You can find postman collection here https://www.postman.com/orange-shadow-985617/paymentprocessor/collection/zj93ii0/transactions?action=share&creator=515277 


## Run Test suite
```aiignore
php artisan test
```
