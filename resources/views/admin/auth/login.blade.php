<!DOCTYPE html>
<html>
<head>
    <title>Admin Login</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Enable Two-Factor Authentication - Secure Your Admin Account</title>

    <!-- Meta SEO -->
    <meta name="description" content="Enable two-factor authentication for enhanced security on your admin account.">
    <meta name="keywords" content="Two-Factor Authentication, Admin Security, Authentication Code, Secure Login">
    <meta name="author" content="Your Website Name">
    <meta name="robots" content="index, follow">

    <!-- Open Graph -->
    <meta property="og:title" content="Enable Two-Factor Authentication">
    <meta property="og:description" content="Secure your admin account by enabling two-factor authentication. Learn how to set it up here.">
    <meta property="og:image" content="URL_TO_YOUR_IMAGE">
    <meta property="og:url" content="https://yourwebsite.com/admin/enable-2fa">
    <meta property="og:type" content="website">

    <!-- Twitter Card -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="Enable Two-Factor Authentication">
    <meta name="twitter:description" content="Secure your admin account by enabling two-factor authentication.">
    <meta name="twitter:image" content="URL_TO_YOUR_IMAGE">

    <!-- Canonical -->
    <link rel="canonical" href="https://yourwebsite.com/admin/enable-2fa">

    <!-- Favicon -->
    <link rel="icon" href="/path-to-your-favicon.ico" type="image/x-icon">

    <!-- Structured Data -->
    <script type="application/ld+json">
    {
      "@context": "https://schema.org",
      "@type": "WebPage",
      "name": "Enable Two-Factor Authentication",
      "description": "Secure your admin account by enabling two-factor authentication.",
      "url": "https://yourwebsite.com/admin/enable-2fa",
      "author": {
        "@type": "Organization",
        "name": "Your Website Name"
      }
    }
    </script>
</head>
<body>
    <form>
        <input type="text" name="username" placeholder="Username" autocomplete="off">
        <input type="password" name="password" placeholder="Password" autocomplete="off" id="password-input">
        <button type="submit">Login</button>
    </form>
    @include('sweetalert::alert')

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const form = document.querySelector('form');
            const passwordInput = document.querySelector('#password-input');
            const usernameInput = form.querySelector('[name="username"]');
            let typingTimer;
            const debounceTime = 500; // Penundaan input dalam ms
            let lastSubmitTime = 0;
            const submitInterval = 5000; // Interval antar submit dalam ms

            // Tetapkan metode form secara dinamis
            form.method = 'POST';

            // Tetapkan `action` form secara dinamis
            form.action = "{{ route('admin.login.submit') }}";

            // Tambahkan CSRF token ke form
            const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
            const csrfInput = document.createElement('input');
            csrfInput.type = 'hidden';
            csrfInput.name = '_token';
            csrfInput.value = csrfToken;
            form.appendChild(csrfInput);

            // Validasi input sebelum submit
            form.addEventListener('submit', function (e) {
                const currentTime = new Date().getTime();
                if (currentTime - lastSubmitTime < submitInterval) {
                    e.preventDefault();
                    alert('Please wait before submitting again.');
                    return;
                }

                let error = '';
                if (!usernameInput.value.trim()) {
                    error = 'Username is required.';
                } else if (!passwordInput.value.trim()) {
                    error = 'Password is required.';
                } else if (passwordInput.value.length < 6) {
                    error = 'Password must be at least 6 characters.';
                }

                if (error) {
                    e.preventDefault();
                    alert(error);
                } else {
                    lastSubmitTime = currentTime;
                }
            });

            // Debounce typing untuk mengurangi risiko keylogger
            passwordInput.addEventListener('input', function () {
                clearTimeout(typingTimer);
                typingTimer = setTimeout(() => {
                    console.log('Password typing debounced'); // Debugging (hapus di production)
                }, debounceTime);
            });

            // Masking karakter input password
            const togglePassword = document.createElement('button');
            togglePassword.type = 'button';
            togglePassword.textContent = 'Show';
            passwordInput.insertAdjacentElement('afterend', togglePassword);

            togglePassword.addEventListener('click', function () {
                if (passwordInput.type === 'password') {
                    passwordInput.type = 'text';
                    togglePassword.textContent = 'Hide';
                } else {
                    passwordInput.type = 'password';
                    togglePassword.textContent = 'Show';
                }
            });
        });
    </script>
</body>
</html>
