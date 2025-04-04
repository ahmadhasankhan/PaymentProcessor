<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Payment Form</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
</head>
<body class="bg-gray-100 flex justify-center items-center min-h-screen">
<div class="bg-white shadow-lg rounded-lg p-8 w-full max-w-lg">
    @if(session('success'))
        <div class="text-green-600">{{ session('success') }}</div>
    @endif

    @if(session('error'))
        <div class="text-red-600">{{ session('error') }}</div>
    @endif

    @if($errors->any())
        <div class="text-red-600">
            <ul>
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <h2 class="text-2xl font-semibold text-center mb-6">Secure Payment Form</h2>
    <form action="{{ route('process.payment') }}" method="POST" id="payment-form" class="space-y-4">
        @csrf

        <div class="grid grid-cols-2 gap-4">
            <input name="first_name" type="text" placeholder="First Name" class="border p-2 rounded w-full" required>
            <input name="last_name" type="text" placeholder="Last Name" class="border p-2 rounded w-full" required>
        </div>

        <input name="address" type="text" placeholder="Address" class="border p-2 rounded w-full" required>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <input name="zip_code" type="text" placeholder="Zip Code" class="border p-2 rounded w-full" required>
            <input name="country" type="text" placeholder="Country" class="border p-2 rounded w-full" required>
        </div>

        <input name="amount" type="number" step="0.01" placeholder="Amount" class="border p-2 rounded w-full" required>
        <div>
            <label class="block text-gray-700 font-medium">Transaction Type:</label>
            <select name="transaction_type" required class="border p-2 rounded w-full">
                <option value="">Select Transaction Type</option>
                <option value="Deposit">Deposit</option>
                <option value="Withdrawal">Withdrawal</option>
            </select>
        </div>

        <div>
            <label class="block text-gray-700 font-medium">Payment Mode:</label>
            <select name="merchant" id="merchant" required class="border p-2 rounded w-full">
                <option value="">Select Payment Method</option>
                @foreach($merchants as $merchant)
                    <option value="{{ $merchant->name }}" data-type="{{ $merchant->name }}">
                        {{ $merchant->name }}</option>
                @endforeach
            </select>
        </div>
        <!-- Card Fields -->
        <div id="card-fields" class="hidden">
            <label class="block text-gray-700 font-medium">Card Details:</label>

            <input name="card_number" type="text" placeholder="Card Number"
                   class="border p-3 rounded w-full bg-gray-100">
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <input name="expiry_date" type="text" placeholder="MM/YY" class="border p-2 rounded w-full bg-gray-100">
                <input name="cvv" type="text" placeholder="CVV" class="border p-2 rounded w-full bg-gray-100">
            </div>

        </div>

        <!-- Crypto Fields -->
        <div id="crypto-fields" class="hidden">
            <input name="wallet_address" type="text" placeholder="Wallet Address" class="border p-2 rounded w-full">
            <input name="transaction_hash" type="text" placeholder="Transaction Hash" class="border p-2 rounded w-full">
        </div>

        <button type="submit"
                class="w-full bg-blue-600 text-white font-semibold py-2 rounded hover:bg-blue-700 transition">
            Submit Payment
        </button>
    </form>
</div>

<script>
    const merchantSelect = document.getElementById('merchant');
    const cardFields = document.getElementById('card-fields');
    const cryptoFields = document.getElementById('crypto-fields');

    merchantSelect.addEventListener('change', function () {
        const value = this.value;
        cardFields.classList.add('hidden');
        cryptoFields.classList.add('hidden');

        if (value === 'VISA' || value === 'MasterCard') {
            cardFields.classList.remove('hidden');
        } else if (value === 'USDT' || value === 'Bitcoin' || value === 'Litecoin') {
            cryptoFields.classList.remove('hidden');
        }
    });
</script>

<style>
    .input {
        @apply w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500;
    }
</style>
</body>
</html>
