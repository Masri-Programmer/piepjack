#!/bin/bash
set -e

UBERSPACE_USER="piepjack"

PROJECT_DIR="api.piepjack-clothing.de"

GIT_BRANCH="main"

PROJECT_PATH="/var/www/virtual/$UBERSPACE_USER/$PROJECT_DIR"

# --- Deployment Steps ---
echo "🚀  Starting deployment for $PROJECT_DIR..."

cd "$PROJECT_PATH" || { echo "❌ ERROR: Project directory not found at $PROJECT_PATH!"; exit 1; }
echo "✅  Changed directory to project folder."

# Activate maintenance mode
php artisan down
echo "✅  Application is now in maintenance mode."

# Pull the latest changes from the git repository
echo "🔄  Pulling latest changes from branch '$GIT_BRANCH'..."
git pull origin "$GIT_BRANCH"
echo "✅  Git pull complete."

# Install Composer dependencies (for production)
echo "📦  Installing Composer dependencies..."
composer install --no-interaction --prefer-dist --optimize-autoloader --no-dev
echo "✅  Composer dependencies installed."

# Install NPM dependencies and build your Vue/Tailwind assets
echo "🎨  Installing NPM dependencies and building assets..."
npm install
npm run build
echo "✅  NPM assets built."

# Run database migrations
echo "🗄️  Running database migrations..."
php artisan migrate --force
echo "✅  Database migrations complete."

# Clear caches to ensure new code is used
echo "🧹  Clearing application caches..."
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear
echo "✅  Caches cleared."

# Cache configuration for performance
echo "⚡  Caching configuration, routes, and views..."
php artisan config:cache
php artisan route:cache
php artisan view:cache
echo "✅  Configuration cached."

# Restart queue workers if you use them
# If you don't use Laravel Queues, you can safely remove this line.
echo "🔄  Restarting queue workers..."
php artisan queue:restart
echo "✅  Queue workers restarted."

# Exit maintenance mode
php artisan up
echo "✅  Application is now live."

echo "🎉  Deployment finished successfully!"

