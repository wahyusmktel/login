<!DOCTYPE html>
<html>
<head>
    <title>Admin Dashboard</title>
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
    
    <h1>Welcome, Admin!</h1>
    <form action="{{ route('admin.logout') }}" method="POST">
        @csrf
        <button type="submit">Logout</button>
    </form>
    @include('sweetalert::alert')
</body>
</html>
