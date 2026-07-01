# Moleraven

A web app for managing a Mongoose Traveller 2nd Edition (MGT2) RPG campaign. Tracks player characters, NPCs, organizations, animals, planets, diary entries, and party notes.

## Requirements

- PHP 8.4
- Node.js 20+
- [Laravel Herd](https://herd.laravel.com/) (recommended) or any web server that can serve Laravel apps
- SQLite (default) or MySQL/PostgreSQL

## Local Development with Herd

1. **Clone and install dependencies**

   ```bash
   git clone <repo-url> moleraven
   cd moleraven
   composer install
   npm install
   ```

2. **Configure the environment**

   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

   The default config uses SQLite — no database server needed. The database file will be created at `database/database.sqlite`.

3. **Create the database and run migrations**

   ```bash
   touch database/database.sqlite
   php artisan migrate
   ```

4. **Build frontend assets**

   For development (with hot reload):
   ```bash
   npm run dev
   ```

   For a one-time production build:
   ```bash
   npm run build
   ```

5. **Serve the site**

   If using Laravel Herd, add the project directory as a site. It will be available at `https://moleraven.test`.

   Otherwise, run:
   ```bash
   php artisan serve
   ```

6. **Register an account**

   Visit the app and register. All routes require authentication.

## Planet Data

Planet names and images are fetched from the [TravellerMap API](https://travellermap.com/) and cached locally for 24 hours. An internet connection is required the first time a planet in a given sector is loaded; subsequent loads use the cache.

## Deployment

### Laravel Cloud (recommended)

1. Push to a Git repository.
2. Create a new project at [cloud.laravel.com](https://cloud.laravel.com/) and connect the repository.
3. Set the following environment variables in the Cloud dashboard:
   - `APP_KEY` — generate with `php artisan key:generate --show`
   - `APP_ENV=production`
   - `APP_DEBUG=false`
   - `APP_URL` — your production URL
4. Laravel Cloud handles migrations, asset building, and SSL automatically.

### Manual Server Deployment

1. **Upload files** to your server (via Git, rsync, or similar).

2. **Install dependencies**

   ```bash
   composer install --no-dev --optimize-autoloader
   npm ci && npm run build
   ```

3. **Configure environment**

   ```bash
   cp .env.example .env
   # Edit .env with production values
   php artisan key:generate
   ```

   Key `.env` values to set:
   ```
   APP_ENV=production
   APP_DEBUG=false
   APP_URL=https://your-domain.com
   DB_CONNECTION=sqlite        # or mysql/pgsql with credentials
   SESSION_DRIVER=database
   CACHE_STORE=database
   QUEUE_CONNECTION=database
   ```

4. **Run migrations**

   ```bash
   php artisan migrate --force
   ```

5. **Set permissions**

   ```bash
   chmod -R 775 storage bootstrap/cache
   chown -R www-data:www-data storage bootstrap/cache
   ```

6. **Point your web server** document root to the `public/` directory.

7. **Optimize for production**

   ```bash
   php artisan config:cache
   php artisan route:cache
   php artisan view:cache
   ```

## Common Commands

| Command | Description |
|---|---|
| `php artisan migrate` | Run pending database migrations |
| `php artisan migrate:fresh --seed` | Wipe and re-seed the database |
| `php artisan test --compact` | Run the test suite |
| `npm run dev` | Start Vite dev server with hot reload |
| `npm run build` | Build production assets |
| `vendor/bin/pint` | Fix PHP code style |
