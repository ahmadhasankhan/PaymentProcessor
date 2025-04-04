document.addEventListener("DOMContentLoaded", function () {
    const merchantSelect = document.getElementById('merchant');
    const cardFields = document.getElementById('card-fields');
    const cryptoFields = document.getElementById('crypto-fields');
    const expiryInput = document.querySelector('input[name="expiry_date"]');

    if (merchantSelect) {
        merchantSelect.addEventListener('change', function () {
            const value = this.value;
            cardFields.classList.add('hidden');
            cryptoFields.classList.add('hidden');

            if (value === 'VISA' || value === 'MasterCard') {
                cardFields.classList.remove('hidden');
            } else if (['USDT', 'Bitcoin', 'Litecoin'].includes(value)) {
                cryptoFields.classList.remove('hidden');
            }
        });
    }

    // Auto-insert slash and validate expiry date
    if (expiryInput) {
        expiryInput.addEventListener("input", function (e) {
            let value = e.target.value.replace(/\D/g, "");
            if (value.length >= 3) {
                value = value.substring(0, 2) + "/" + value.substring(2, 4);
            }
            e.target.value = value;
        });

        expiryInput.addEventListener("blur", function () {
            const [month, year] = expiryInput.value.split('/');
            const now = new Date();
            const inputMonth = parseInt(month);
            const inputYear = parseInt("20" + year);

            if (!month || !year || inputMonth < 1 || inputMonth > 12 || isNaN(inputYear)) {
                alert("Invalid expiry date");
                return;
            }

            const expiryDate = new Date(inputYear, inputMonth - 1);
            const currentDate = new Date(now.getFullYear(), now.getMonth());

            if (expiryDate < currentDate) {
                alert("Card expiry date is in the past.");
            }
        });
    }
});
