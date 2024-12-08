<!DOCTYPE html>
<html lang="en">
<head>
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
    <title>Enable Two-Factor Authentication</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>
<body>
    <h1>Enable Two-Factor Authentication</h1>
    <p>Scan the QR Code below using your Google Authenticator app:</p>
    <div>
        {!! $qrCodeSvg !!}
    </div>
    <p>After scanning the QR Code, click the "Confirm" button to enable Two-Factor Authentication.</p>
    <form>
        <button type="submit">Confirm</button>
    </form>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const form = document.querySelector('form');
            const confirmButton = form.querySelector('button[type="submit"]');

            // Tetapkan metode form secara dinamis
            form.method = 'POST';

            // Tetapkan `action` form secara dinamis
            form.action = "{{ route('admin.confirm-2fa') }}";

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
            });

            // Validasi tambahan: Pastikan QR Code sudah dipindai (opsional)
            confirmButton.addEventListener('click', function (e) {
                const scanned = confirm('Have you scanned the QR Code with your Google Authenticator app?');
                if (!scanned) {
                    e.preventDefault();
                }
            });
        });
    </script>
</body>
</html>
