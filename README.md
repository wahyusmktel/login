
# Admin Two-Factor Authentication Project

This project is a secure two-factor authentication system built for admin users. It enhances security by requiring a one-time authentication code in addition to the standard login credentials. The system is built using the Laravel framework and integrates with Google Authenticator for generating and verifying codes.

## Features
- **Two-Factor Authentication (2FA)**: Enable/disable 2FA for admin accounts.
- **Secure Login**: Implements rate-limiting, input validation, and HTTPS for enhanced security.
- **User-Friendly UI**: Simple and intuitive interface for enabling and verifying 2FA.
- **SweetAlert Notifications**: Provides interactive alerts for various actions (success, error, etc.).
- **SEO Optimized**: Meta tags and structured data for better search engine visibility.

## Technologies Used
- Laravel Framework
- PragmaRX Google2FA for authentication
- BaconQrCode for generating QR codes
- SweetAlert for alerts and notifications
- HTML, CSS, JavaScript for the user interface

## Installation
1. Clone the repository:
   ```bash
   git clone https://github.com/your-repository.git
   ```
2. Navigate to the project directory:
   ```bash
   cd your-project-directory
   ```
3. Install dependencies:
   ```bash
   composer install
   npm install
   npm run dev
   ```
4. Configure environment variables:
   - Copy `.env.example` to `.env`:
     ```bash
     cp .env.example .env
     ```
   - Set up database credentials and application keys:
     ```bash
     php artisan key:generate
     ```
5. Run migrations and seeders:
   ```bash
   php artisan migrate --seed
   ```
6. Start the development server:
   ```bash
   php artisan serve
   ```

<!-- ## Screenshots
![Login Page](https://via.placeholder.com/800x400.png?text=Login+Page)
![Enable 2FA](https://via.placeholder.com/800x400.png?text=Enable+2FA)
![Verify 2FA](https://via.placeholder.com/800x400.png?text=Verify+2FA) -->

## Author
Hi! I am **Wahyu Rahmat**, a passionate developer from **SMK Tel**. This project is a reflection of my enthusiasm for building secure and user-friendly systems. Feel free to connect with me!

### Social Media
- **Instagram**: [@wahyurahmat55](https://www.instagram.com/wahyurahmat55/)

## License
This project is licensed under the [MIT License](https://opensource.org/licenses/MIT).
