# SIMAN App

SIMAN App is a Laravel 12 application for managing internal asset data, SIMAN import data, invalid rows, comparison reports, locked records, activity logs, settings, and PDF/XLSX exports. The codebase is Blade-driven and uses server-rendered pages backed by custom controllers, middleware, and database-backed sessions and queues.

## Project Overview

The application centers on `DataInternal`, `simanData`, and `InvalidData` records. It supports login-based access, administrator-only master data management, internal asset import and editing, SIMAN import, comparison workflows, locked-record review, activity logging, backup administration, and PSP/BAST document generation.

The repository is built with:

- PHP 8.2
- Laravel 12
- Vite + Tailwind CSS 4 for frontend assets
- Blade templates for UI rendering
- database-backed session and queue storage

## Features

- Authentication with login and logout flows.
- Role-based access control through a custom `role` middleware.
- Duplicate-submission protection on write requests.
- Dashboard summaries for internal, SIMAN, and invalid data.
- CRUD management for reference data such as BMN, barang, satker, lokasi, identitas category, identitas, atribut, unit kerja, unit teknis, and users.
- Internal asset import, update, attachment handling, BAST export, and Excel export.
- SIMAN CSV import with batch tracking and deduplication by register code.
- Data comparison between internal and SIMAN records with multiple export variants.
- Invalid-row management and conversion into internal data.
- Locked-data review, lock, unlock, unlock-request, and rejection flows.
- Activity log capture, filtering, cleanup, and export.
- Backup listing, backup generation, download, and deletion.
- Settings management for values such as admin phone and document metadata.
- PSP PDF generation from selected internal records.

## Requirements

- PHP `^8.2`
- Composer
- Node.js and npm
- A database supported by the project configuration
- `zip` and `pdo_mysql` extensions if you plan to use the backup commands
- `mysqldump` available on the server if you plan to use `backup:run`

The application defaults to:

- `sqlite` for the database connection
- `database` sessions
- `database` queues
- `log` mail delivery
- `local` filesystem storage

## Installation

1. Install PHP dependencies.

```bash
composer install
```

2. Install frontend dependencies.

```bash
npm install
```

3. Create and configure your environment file.

```bash
copy .env.example .env
```

Update the key settings in `.env`, especially:

- `APP_NAME`
- `APP_URL`
- `APP_DEBUG`
- `DB_CONNECTION`
- `DB_HOST`
- `DB_PORT`
- `DB_DATABASE`
- `DB_USERNAME`
- `DB_PASSWORD`
- `FILESYSTEM_DISK`
- `SESSION_DRIVER`
- `QUEUE_CONNECTION`
- `MAIL_MAILER`
- `BACKUP_RETENTION`
- `MYSQL_BIN_PATH`

4. Generate the application key.

```bash
php artisan key:generate
```

5. Run the database migrations.

```bash
php artisan migrate
```

6. Seed the default data if needed.

```bash
php artisan db:seed
```

The seeder creates an administrator level record and an initial admin user with the email `admin@email.com` and password `admin123`.

7. Create the storage symlink for public files.

```bash
php artisan storage:link
```

8. Build frontend assets for production.

```bash
npm run build
```

You can also run the bundled setup script from `composer.json`:

```bash
composer run setup
```

## Configuration

The main configuration surfaces are:

- `config/app.php` for app name, timezone, SweetAlert alias, and admin phone fallback.
- `config/auth.php` for the web guard and `User` model.
- `config/database.php` for SQLite, MySQL, MariaDB, PostgreSQL, and SQL Server connections.
- `config/session.php` for database-backed sessions.
- `config/queue.php` for database-backed queues.
- `config/filesystems.php` for local and public file storage.
- `config/logging.php` for stack/single-file logging.
- `config/mail.php` for log-based mail delivery.
- `config/backups.php` for backup paths and retention.
- `config/dompdf.php` for PDF rendering defaults.
- `config/sweetalert.php` for alert UI behavior.
- `config/services.php` for external service credentials.

Important environment variables used by the code include:

- Application: `APP_NAME`, `APP_URL`, `APP_DEBUG`, `ADMIN_PHONE`
- Auth: `AUTH_GUARD`, `AUTH_MODEL`
- Database: `DB_CONNECTION`, `DB_HOST`, `DB_PORT`, `DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD`
- Storage: `FILESYSTEM_DISK`
- Sessions and queues: `SESSION_DRIVER`, `QUEUE_CONNECTION`
- Backup tooling: `BACKUP_RETENTION`, `MYSQL_BIN_PATH`
- Mail and logging: `MAIL_MAILER`, `LOG_CHANNEL`
- SweetAlert: `SWEET_ALERT_*`

## Usage

### Local development

Run the combined development stack:

```bash
composer run dev
```

This starts:

- `php artisan serve`
- `php artisan queue:listen --tries=1`
- `npm run dev`

### Common Artisan commands

The project defines several operational commands:

```bash
php artisan backup:check
php artisan backup:run
php artisan activity-logs:cleanup
php artisan run:tests
```

### Application workflow

- Open the login page and authenticate through the `login` route.
- Use the dashboard to review asset summaries and condition counts.
- Manage reference data only as an administrator.
- Import internal data or SIMAN data through their respective controllers and pages.
- Review comparison results, invalid rows, and locked records from the protected area.
- Use backup and activity-log screens from the admin section.

## Folder Structure

The most relevant project folders are:

```text
app/
	Console/Commands/        Custom Artisan commands for backup, cleanup, and tests
	Helpers/                 Submission token helper functions
	Http/Controllers/        Login, dashboard, import, compare, backup, and admin controllers
	Http/Middleware/         Role checks, duplicate submission protection, and request logging
	Models/                  Core Eloquent models for assets, imports, settings, logs, and lookups
	Policies/                DataInternal authorization policy
	Providers/               Application service provider and view composer setup
	Services/                Import idempotency service
bootstrap/                 Framework bootstrap and provider registration
config/                    Application, auth, database, filesystem, queue, session, mail, backup, and alert settings
database/migrations/       Schema definitions and later table updates
database/seeders/          Default admin-level and settings seeders
public/                    Public web root, compiled assets, and storage symlink
resources/css/             Tailwind CSS entry styles
resources/js/              Minimal Vite bootstrap and Axios setup
resources/views/           Blade views for login, dashboard, master data, internal data, compare, logs, backups, PSP, PDFs, and errors
routes/                    Web and console routes
storage/                   Logs, backups, generated exports, and framework caches
```

## Deployment

Before deploying to production, make sure the target server provides the requirements listed above and can run the backup tooling if you intend to use it.

Recommended deployment steps:

```bash
composer install --no-dev --optimize-autoloader
php artisan migrate --force
npm install
npm run build
php artisan storage:link
```

Operational checks for deployment:

- Ensure `storage/` and `bootstrap/cache/` are writable.
- Ensure the `public/storage` symlink exists.
- Configure the scheduler to run `php artisan schedule:run`.
- Keep a queue worker available if queued work is introduced or enabled in production.
- If backups are enabled, install `zip`, `pdo_mysql`, and `mysqldump`, and configure `MYSQL_BIN_PATH`.

The scheduled jobs defined in the codebase are:

- `backup:run` every three months
- `activity-logs:cleanup` daily at 01:00

## Notes

- This repository does not include an `app/Http/Requests` directory, `routes/api.php`, `app/Events`, `app/Listeners`, `app/Notifications`, `app/Mail`, `app/Jobs`, or a `tests/` directory.
- The project uses Blade views and server-side DataTables rather than an API-first or SPA architecture.
- The default Laravel scaffold README has been replaced with project-specific documentation based on the audited codebase.
