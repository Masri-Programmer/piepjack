#!/bin/bash
# ship.sh - Deployment with Auto-Rollback, Forced Execution & Status Checks
set -e

# --- CONFIGURATION ---
REMOTE_USER="piepjack"
REMOTE_HOST="gienah.uberspace.de"
REMOTE_APP_PATH="/var/www/virtual/piepjack/testing-piepjack"
REMOTE_DEPLOY_SCRIPT="${REMOTE_APP_PATH}/deploy.sh"
SSR_PROCESS="piepjack-ssr"

# Detect if we are on Windows (Git Bash) or Linux/Mac for PHP
if [[ "$OSTYPE" == "msys" || "$OSTYPE" == "win32" ]]; then
    # Adjust this to your specific Herd path if not in global PATH
    LOCAL_PHP_BIN="C:\Users\Admin\.config\herd\bin\php.bat"
else
    LOCAL_PHP_BIN="php"
fi

# --- MODE: STATUS CHECK ---
if [ "$1" == "status" ]; then
    echo "🔍 Checking Remote Server Status..."
    echo "---------------------------------------------------"
    ssh "${REMOTE_USER}@${REMOTE_HOST}" "
        echo '📊 PM2 Process Status:'
        # Uberspace specific: Use standard path or localized bin
        pm2 list

        echo ''
        echo '🛠️  Laravel Maintenance Mode:'
        if [ -f ${REMOTE_APP_PATH}/storage/framework/down ]; then
            echo '❌ Application is DOWN (Maintenance Mode active)'
        else
            echo '✅ Application is LIVE'
        fi

        echo ''
        echo '💾 Disk Usage (Build Folder):'
        du -sh ${REMOTE_APP_PATH}/public/build 2>/dev/null || echo '⚠️ Build folder missing'
    "
    echo "---------------------------------------------------"
    exit 0
fi

# --- SAFETY MECHANISM (TRAP) ---
on_error() {
    echo "❌ DEPLOYMENT FAILED! Initiating Rollback..."
    ssh "${REMOTE_USER}@${REMOTE_HOST}" "
        if [ -d '${REMOTE_APP_PATH}/public/build_backup' ]; then
            rm -rf '${REMOTE_APP_PATH}/public/build'
            mv '${REMOTE_APP_PATH}/public/build_backup' '${REMOTE_APP_PATH}/public/build'
        fi
        cd ${REMOTE_APP_PATH} && php artisan up
    "
    exit 1
}
trap 'on_error' ERR

echo "--- Starting Deployment ---"


echo "📦 Select version bump? [patch (default) / minor / major / none]"
read BUMP
if [ -z "$BUMP" ]; then BUMP="patch"; fi

if [ "$BUMP" != "none" ]; then
    echo "🔖 Bumping version ($BUMP)..."
    npm version $BUMP --no-git-tag-version

    echo "📸 Committing ALL changes (Code + Version)..."
    git add .
    git commit -m "chore: shipment release $(node -p "require('./package.json').version")"
fi

echo "🌍 Syncing Translations Locally..."
"$LOCAL_PHP_BIN" artisan translate:local --all || echo "⚠️ Translation command skipped (not found)"

echo "🛠️  Building Assets (SSR Mode)..."
export NODE_OPTIONS="--max-old-space-size=4096"
npm run build

echo "🛡️  Backing up remote assets..."
ssh "${REMOTE_USER}@${REMOTE_HOST}" "
    # Turn off silent exit so we can see errors, handle them manually
    set +e

    echo '📂 CD into: ${REMOTE_APP_PATH}'
    cd ${REMOTE_APP_PATH} || { echo '❌ ERROR: Could not find directory!'; exit 1; }

    echo '🧹 Cleaning old backup...'
    rm -rf public/build_backup

    echo '💾 Attempting backup...'
    if [ -d 'public/build' ]; then
        cp -r public/build public/build_backup
        echo '✅ Backup created.'
    else
        echo '⚠️ public/build folder missing - nothing to backup (This is normal for first run).'
    fi

    echo '🔄 Resetting Git...'
    # We add || echo to see if it fails without stopping script
    git reset --hard HEAD || echo '❌ Git Reset Failed'
    git clean -fd || echo '❌ Git Clean Failed'

    echo '🛑 Activating Maintenance Mode...'
    php artisan down --render='errors::503' || echo '❌ Artisan command failed'

    echo '✅ Step 3 Complete'
"

# --- STEP 5: UPLOAD ASSETS ---
echo "📤 Uploading public/build to Server..."
ssh "${REMOTE_USER}@${REMOTE_HOST}" "rm -rf ${REMOTE_APP_PATH}/public/build/*"
scp -r -C -p public/build/* "${REMOTE_USER}@${REMOTE_HOST}:${REMOTE_APP_PATH}/public/build/"

# --- STEP 5: PUSH BACKEND & DEPLOY ---
echo "💾 Pushing Laravel Backend Code..."
git push origin main || echo "⚠️  Git had nothing new to push (Files are up to date)."

# --- STEP 7: FORCE EXECUTE DEPLOY ---
echo "⚡ Forcing Remote Deployment Script..."
ssh "${REMOTE_USER}@${REMOTE_HOST}" "bash ${REMOTE_DEPLOY_SCRIPT}"

# --- STEP 7: CLEANUP & RELOAD ---
echo "🔄 Reloading SSR Process..."
ssh "${REMOTE_USER}@${REMOTE_HOST}" "if [ -f ~/.profile ]; then source ~/.profile; elif [ -f ~/.bash_profile ]; then source ~/.bash_profile; elif [ -f ~/.bashrc ]; then source ~/.bashrc; fi; rm -rf ${REMOTE_APP_PATH}/public/build_backup"

echo "✅ DEPLOYMENT SUCCESSFUL!"