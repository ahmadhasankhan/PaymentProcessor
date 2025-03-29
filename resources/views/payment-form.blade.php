<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Secure Payment</title>
    <script src="https://js.stripe.com/v3/"></script>
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
</head>
<body class="bg-gray-100 flex justify-center items-center min-h-screen">

    <div class="bg-white shadow-lg rounded-lg p-8 w-full max-w-lg">
        <h2 class="text-2xl font-bold text-center mb-6">Payment Form</h2>

        <!-- Flash Messages -->
        @if(session('success'))
            <p class="text-green-600 text-center mb-4">{{ session('success') }}</p>
        @elseif(session('error'))
            <p class="text-red-600 text-center mb-4">{{ session('error') }}</p>
        @elseif(session('info'))
            <p class="text-blue-600 text-center mb-4">{{ session('info') }}</p>
        @endif

        <form action="{{ route('process.payment') }}" method="POST" id="payment-form" class="space-y-4">
            @csrf
            <div class="grid grid-cols-2 gap-4">
                <input type="text" name="first_name" placeholder="First Name" class="border p-2 rounded w-full" required>
                <input type="text" name="last_name" placeholder="Last Name" class="border p-2 rounded w-full" required>
            </div>

            <input type="text" name="address" placeholder="Address" class="border p-2 rounded w-full" required>
            <div class="grid grid-cols-2 gap-4">
                <input type="text" name="zip_code" placeholder="Zip Code" class="border p-2 rounded w-full" required>
                <input type="text" name="country" placeholder="Country" class="border p-2 rounded w-full" required>
            </div>

            <input type="number" name="amount" placeholder="Amount" class="border p-2 rounded w-full" required>

            <div>
                <label class="block text-gray-700 font-medium">Transaction Type:</label>
                <select name="transaction_type" class="border p-2 rounded w-full">
                    <option value="Deposit">Deposit</option>
                    <option value="Withdrawal">Withdrawal</option>
                </select>
            </div>

            <div>
                <label class="block text-gray-700 font-medium">Payment Mode:</label>
                <select name="merchant_id" class="border p-2 rounded w-full" id="merchant" required>
                    <option value="">Select a Payment Mode</option>
                    @foreach($merchants as $merchant)
                        <option value="{{ $merchant->id }}" data-type="{{ $merchant->name }}">
                            {{ $merchant->name }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Card Payment Fields -->
            <div id="card-fields" class="hidden">
                <label class="block text-gray-700 font-medium">Card Details:</label>
                <div id="card-element" class="border p-3 rounded w-full bg-gray-100"></div>
            </div>

            <!-- Crypto Payment Fields -->
            <div id="crypto-fields" class="hidden">
                <input type="text" name="wallet_address" placeholder="Wallet Address" class="border p-2 rounded w-full">
                <input type="text" name="transaction_hash" placeholder="Transaction Hash" class="border p-2 rounded w-full">
            </div>

            <button type="submit" class="bg-blue-600 text-white py-2 px-4 rounded w-full hover:bg-blue-700 transition">
                Pay Now
            </button>
        </form>
    </div>

    <script>
        var stripe = Stripe("{{ env('STRIPE_KEY') }}");
        var elements = stripe.elements();
        var card = elements.create("card");
        card.mount("#card-element");

        document.getElementById("merchant").addEventListener("change", function () {
            let selectedOption = this.options[this.selectedIndex];
            let merchantType = selectedOption.dataset.type; // Get merchant type from data-type attribute

            let cardFields = document.getElementById("card-fields");
            let cryptoFields = document.getElementById("crypto-fields");

            if (merchantType === "VISA" || merchantType === "MasterCard") {
                cardFields.classList.remove("hidden");
                cryptoFields.classList.add("hidden");
            } else {
                cardFields.classList.add("hidden");
                cryptoFields.classList.remove("hidden");
            }
        });

        document.getElementById("payment-form").addEventListener("submit", async function (event) {
            let selectedOption = document.getElementById("merchant").options[document.getElementById("merchant").selectedIndex];
            let merchantType = selectedOption.dataset.type;

            if (merchantType === "VISA" || merchantType === "MasterCard") {
                event.preventDefault(); // Prevent form submission until token is generated

                const {token, error} = await stripe.createToken(card);
                if (error) {
                    alert(error.message);
                    return;
                }

                // Append Stripe token to the form before submitting
                let form = document.getElementById("payment-form");
                let hiddenInput = document.createElement("input");
                hiddenInput.setAttribute("type", "hidden");
                hiddenInput.setAttribute("name", "stripeToken");
                hiddenInput.setAttribute("value", token.id);
                form.appendChild(hiddenInput);

                form.submit(); // Now submit the form
            }
        });

    </script>

</body>
</html>
