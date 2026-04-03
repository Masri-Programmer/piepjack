# Getting Started

Follow these steps to set up the Piepjackclothing development environment.

## Requirements

- **PHP:** 8.5+
- **Node.js:** 23+ (Check `.nvmrc`)
- **Database:** MySQL 8.0+
- **Server:** Laravel Herd (Recommended) or Laravel Sail

## Installation

### 1. Clone the Repository

```bash
git clone <repository-url>
cd piepjack
```

### 2. Backend Setup

Install Composer dependencies:

```bash
composer install
```

Copy the environment file and generate the application key:

```bash
cp .env.example .env
php artisan key:generate
```

### 3. Frontend Setup

Install NPM dependencies:

```bash
npm install
```

### 4. Database Configuration

Update your `.env` file with your database credentials:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=piepjack
DB_USERNAME=root
DB_PASSWORD=
```

Run migrations and seed the database:

```bash
php artisan migrate --seed
```

### 5. Payment Setup (Stripe)

Configure your Stripe keys in the `.env` file:

```env
STRIPE_KEY=pk_test_...
STRIPE_SECRET=sk_test_...
STRIPE_WEBHOOK_SECRET=whsec_...
```

## Running the Application

### Using Laravel Herd

If you use Laravel Herd, the app is automatically served at `https://piepjack.test`.

### Development Servers

Run the following command to start both the Vite development server and the Laravel worker (if needed):

```bash
npm run dev
```

The application will be available at your local development URL.
