<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verify Two-Factor Authentication</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>
<body>
    <h1>Two-Factor Authentication</h1>
    <form>
        <label for="one_time_password">Authentication Code</label>
        <input type="password" name="one_time_password" id="one_time_password" required>
        <button type="submit">Verify</button>
    </form>
    @include('sweetalert::alert')

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const form = document.querySelector('form');
            const otpInput = document.querySelector('#one_time_password');

            // Tetapkan metode form secara dinamis
            form.method = 'POST';

            // Tetapkan `action` form secara dinamis
            form.action = "{{ route('admin.verify-2fa.submit') }}";

            // Tambahkan CSRF token ke form
            const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
            const csrfInput = document.createElement('input');
            csrfInput.type = 'hidden';
            csrfInput.name = '_token';
            csrfInput.value = csrfToken;
            form.appendChild(csrfInput);

            // Batasi pengiriman form terlalu sering
            let lastSubmitTime = 0;
            const submitInterval = 5000; // 5 detik
            form.addEventListener('submit', function (e) {
                const currentTime = new Date().getTime();
                if (currentTime - lastSubmitTime < submitInterval) {
                    e.preventDefault();
                    alert('Please wait a few seconds before submitting again.');
                    return;
                }
                lastSubmitTime = currentTime;

                // Validasi input: Harus berupa angka 6 digit
                const otpValue = otpInput.value.trim();
                if (!/^\d{6}$/.test(otpValue)) {
                    e.preventDefault();
                    alert('Invalid authentication code. Please enter a 6-digit number.');
                }
            });

            // Masking input untuk OTP
            const togglePassword = document.createElement('button');
            togglePassword.type = 'button';
            togglePassword.textContent = 'Show';
            otpInput.insertAdjacentElement('afterend', togglePassword);

            togglePassword.addEventListener('click', function () {
                if (otpInput.type === 'password') {
                    otpInput.type = 'text';
                    togglePassword.textContent = 'Hide';
                } else {
                    otpInput.type = 'password';
                    togglePassword.textContent = 'Show';
                }
            });
        });
    </script>
</body>
</html>
