# Budget Tracker Laravel Project

A comprehensive budget tracking application built with Laravel 11 and Tailwind CSS. This application helps you manage your finances by tracking payments, receipts, and maintaining a current balance.

## Features

### 🏦 Balance Management
- Track current balance
- Add funds to your balance
- View total amounts to pay and receive
- Real-time balance updates

### 💰 Transaction Management
- Create payment transactions (money to pay to someone)
- Create receipt transactions (money to get from someone)
- Mark transactions as paid (full or partial)
- Auto-generation of remaining amount transactions for partial payments
- Transaction history with detailed information

### 👥 Person Management
- Add and manage people involved in transactions
- Store contact information (email, phone, address)
- Edit and delete people
- Link people to transactions

### 🎨 Modern UI
- Built with Tailwind CSS for a modern, responsive design
- Clean and intuitive interface
- Mobile-friendly design
- Beautiful cards and data tables

## Installation

1. **Clone the repository:**
   ```bash
   git clone <repository-url>
   cd copilot-budget-project
   ```

2. **Install PHP dependencies:**
   ```bash
   composer install
   ```

3. **Install Node.js dependencies:**
   ```bash
   npm install
   ```

4. **Set up environment file:**
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

5. **Set up database:**
   ```bash
   php artisan migrate
   ```

6. **Seed sample data (optional):**
   ```bash
   php artisan db:seed --class=SampleDataSeeder
   ```

7. **Build frontend assets:**
   ```bash
   npm run build
   ```

8. **Start the development server:**
   ```bash
   php artisan serve
   ```

Visit `http://localhost:8000` to access the application.

## Usage

### Getting Started

1. **Add People**: Start by adding people who will be involved in your transactions
2. **Create Transactions**: Create payment or receipt transactions
3. **Track Payments**: Mark transactions as paid (full or partial)
4. **Monitor Balance**: Keep track of your current balance and pending amounts

### Transaction Types

- **Payment**: Money you need to pay to someone
- **Receipt**: Money you expect to receive from someone

### Partial Payments

When you mark a transaction as partially paid:
- The system updates the paid amount
- Calculates the remaining amount
- Automatically creates a new transaction for the remaining balance
- Updates the transaction status to 'partial'

## Database Schema

### People Table
- `id` - Primary key
- `name` - Person's full name
- `email` - Email address (optional)
- `phone` - Phone number (optional)
- `address` - Physical address (optional)

### Transactions Table
- `id` - Primary key
- `type` - Transaction type (payment/receipt)
- `amount` - Original transaction amount
- `paid_amount` - Amount already paid
- `remaining_amount` - Amount still pending
- `description` - Transaction description
- `status` - Transaction status (pending/partial/completed)
- `from_person_id` - Person sending money
- `to_person_id` - Person receiving money
- `parent_transaction_id` - Links to parent transaction (for partial payments)

### Balances Table
- `id` - Primary key
- `current_balance` - Current available balance
- `total_to_pay` - Total amount to pay
- `total_to_receive` - Total amount to receive

## Technology Stack

- **Backend**: Laravel 11
- **Frontend**: Blade Templates with Tailwind CSS
- **Database**: SQLite (default)
- **Build Tool**: Vite
- **Package Manager**: Composer (PHP), npm (JavaScript)

## Contributing

1. Fork the repository
2. Create a feature branch
3. Make your changes
4. Submit a pull request

## License

This project is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).

Laravel is a web application framework with expressive, elegant syntax. We believe development must be an enjoyable and creative experience to be truly fulfilling. Laravel takes the pain out of development by easing common tasks used in many web projects, such as:

- [Simple, fast routing engine](https://laravel.com/docs/routing).
- [Powerful dependency injection container](https://laravel.com/docs/container).
- Multiple back-ends for [session](https://laravel.com/docs/session) and [cache](https://laravel.com/docs/cache) storage.
- Expressive, intuitive [database ORM](https://laravel.com/docs/eloquent).
- Database agnostic [schema migrations](https://laravel.com/docs/migrations).
- [Robust background job processing](https://laravel.com/docs/queues).
- [Real-time event broadcasting](https://laravel.com/docs/broadcasting).

Laravel is accessible, powerful, and provides tools required for large, robust applications.

## Learning Laravel

Laravel has the most extensive and thorough [documentation](https://laravel.com/docs) and video tutorial library of all modern web application frameworks, making it a breeze to get started with the framework.

You may also try the [Laravel Bootcamp](https://bootcamp.laravel.com), where you will be guided through building a modern Laravel application from scratch.

If you don't feel like reading, [Laracasts](https://laracasts.com) can help. Laracasts contains thousands of video tutorials on a range of topics including Laravel, modern PHP, unit testing, and JavaScript. Boost your skills by digging into our comprehensive video library.

## Laravel Sponsors

We would like to extend our thanks to the following sponsors for funding Laravel development. If you are interested in becoming a sponsor, please visit the [Laravel Partners program](https://partners.laravel.com).

### Premium Partners

- **[Vehikl](https://vehikl.com)**
- **[Tighten Co.](https://tighten.co)**
- **[Kirschbaum Development Group](https://kirschbaumdevelopment.com)**
- **[64 Robots](https://64robots.com)**
- **[Curotec](https://www.curotec.com/services/technologies/laravel)**
- **[DevSquad](https://devsquad.com/hire-laravel-developers)**
- **[Redberry](https://redberry.international/laravel-development)**
- **[Active Logic](https://activelogic.com)**

## Contributing

Thank you for considering contributing to the Laravel framework! The contribution guide can be found in the [Laravel documentation](https://laravel.com/docs/contributions).

## Code of Conduct

In order to ensure that the Laravel community is welcoming to all, please review and abide by the [Code of Conduct](https://laravel.com/docs/contributions#code-of-conduct).

## Security Vulnerabilities

If you discover a security vulnerability within Laravel, please send an e-mail to Taylor Otwell via [taylor@laravel.com](mailto:taylor@laravel.com). All security vulnerabilities will be promptly addressed.

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
