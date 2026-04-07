# Deployment

Piepjackclothing uses a automated shipping script for deployments to the production environment (Uberspace).

## Production Environment

- **Host:** `barnard.uberspace.de`
- **User:** `piepjack`
- **Path:** `/var/www/virtual/piepjack/testing-piepjack`
- **Process Manager:** PM2 (for SSR)

## Shipping Process

The `ship.sh` script in the root directory handles the entire deployment workflow, including asset building and remote synchronization.

### 1. Check Status

Before shipping, you can check the remote server status:

```bash
bash ship.sh status
```

### 2. Deploy Updates

To deploy the latest changes:

```bash
bash ship.sh
```

**What the script does:**

1.  **Version Bump:** Prompts for an NPM version bump (patch/minor/major).
2.  **Commit:** Automatically commits all changes with the new version number.
3.  **Translations:** Syncs local translations.
4.  **Build:** Runs `npm run build` locally.
5.  **Backup:** Backs up the current remote build folder.
6.  **Maintenance:** Puts the remote application into maintenance mode (`php artisan down`).
7.  **Upload:** Uploads the new `public/build` assets via SCP.
8.  **Sync:** Pushes the backend code to the remote repository.
9.  **Remote Deploy:** Executes the remote `deploy.sh` script to run migrations, clear cache, and bring the app back up.
10. **Reload:** Restarts the PM2 SSR process.

## Safety & Rollback

The `ship.sh` script includes a safety trap. If any step fails during the upload or synchronization, it will automatically:

- Attempt to restore the previous `public/build` from the backup.
- Disable maintenance mode (`php artisan up`) to ensure the site remains live.

## Manual Maintenance

If you need to manually put the site in maintenance mode on production:

```bash
ssh piepjack@barnard.uberspace.de "cd /var/www/virtual/piepjack/testing-piepjack && php artisan down"
```

And to bring it back up:

```bash
ssh piepjack@barnard.uberspace.de "cd /var/www/virtual/piepjack/testing-piepjack && php artisan up"
```
