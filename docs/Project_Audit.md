# Project Audit

Scope: this document records only what is present in the current workspace. It does not infer missing features or intended behavior beyond the code that exists.

## 1. Platform Baseline

| Area | Observed Value |
|---|---|
| PHP | `^8.2` |
| Laravel | `^12.0` |
| Composer package name | `laravel/laravel` |
| Default timezone | `Asia/Jakarta` |
| Default auth guard | `web` |
| Default DB connection | `sqlite` |
| Default queue connection | `database` |
| Default session driver | `database` |
| Default mailer | `log` |
| Default filesystem disk | `local` |

Composer runtime dependencies present in `composer.json`:

- `barryvdh/laravel-dompdf` `3.1.1`
- `intervention/image` `^3.11`
- `laravel/framework` `^12.0`
- `laravel/tinker` `^2.10.1`
- `openspout/openspout` `^4.28`
- `realrashid/sweet-alert` `^7.3`
- `twbs/bootstrap-icons` `^1.13`
- `yajra/laravel-datatables-oracle` `^12.6`

Composer development dependencies present in `composer.json`:

- `fakerphp/faker`
- `laravel/pail`
- `laravel/pint`
- `laravel/sail`
- `mockery/mockery`
- `nunomaduro/collision`
- `pestphp/pest`
- `pestphp/pest-plugin-laravel`

NPM dependencies present in `package.json`:

- `vite`
- `laravel-vite-plugin`
- `tailwindcss` 4
- `@tailwindcss/vite`
- `axios`
- `concurrently`

Scripts present in `package.json`:

- `build` -> `vite build`
- `dev` -> `vite`

Composer scripts present in `composer.json` include setup, dev, test, and the usual post-autoload / post-create hooks.

## 2. Application Architecture

This codebase is a Blade-first Laravel application with server-rendered pages, custom admin-style resource controllers, CSV import workflows, PDF/XLSX export flows, and database-backed session / queue / cache infrastructure.

Observed functional areas:

- Authentication and login/logout
- Dashboard and summary reporting
- Master data CRUD for reference tables
- Internal asset import, editing, attachment handling, locking, and export
- SIMAN import and comparison against internal data
- Invalid import row handling and conversion into internal data
- Activity logging and activity-log export/cleanup
- Backup administration
- Settings management
- PSP PDF generation
- Locked-data review and approval flow

## 3. Folder Structure

Top-level structure present in the workspace:

- `app/`
- `bootstrap/`
- `config/`
- `database/`
- `public/`
- `resources/`
- `routes/`
- `storage/`
- `vendor/`

Key observed subdirectories:

- `app/Console/Commands`
- `app/Helpers`
- `app/Http/Controllers`
- `app/Http/Middleware`
- `app/Models`
- `app/Policies`
- `app/Providers`
- `app/Services`
- `database/migrations`
- `database/seeders`
- `resources/css`
- `resources/js`
- `resources/views`

Notable workspace artifacts:

- `public/build` is present.
- `public/vendor` is present.
- `public/assets/bootstrap-icons` is present.
- `public/storage` symlink target is configured in filesystems.

## 4. Bootstrap and Runtime Wiring

### bootstrap/app.php

- Web routes are loaded from `routes/web.php`.
- Console routes are loaded from `routes/console.php`.
- Health check endpoint is configured at `/up`.
- `ActivityLogMiddleware` is registered globally.
- Middleware aliases present:
  - `role` -> `App\Http\Middleware\Roles`
  - `prevent.duplicate` -> `App\Http\Middleware\PreventDuplicateSubmissions`
- The exception customization block is present but fully commented out.

### bootstrap/providers.php

Registered providers observed:

- `App\Providers\AppServiceProvider`
- `RealRashid\SweetAlert\SweetAlertServiceProvider`

Commented-out provider entries are present for Intervention Image and Maatwebsite Excel.

### AppServiceProvider

The application service provider registers a global view composer for all views. It injects:

- `requestedCount`: count of `DataInternal` rows where `status = locked` and `is_requested = 1`
- `adminPhone`: `Setting` value for `admin_phone` if present, otherwise an empty string

## 5. Routing Surface

### routes/web.php

Observed route groups and endpoints:

- Root path `/` returns the login view.
- `GET login` -> login page.
- `POST actionLogin` -> login submission, throttled to `5,1`.
- Authenticated block protected by `auth` and `prevent.duplicate`.
- Admin-only subgroup protected by `role:administrator`.

Admin-only routes present:

- Backup management under `/admin/backups`
- Backup archive download and delete routes
- `/admin/test-dashboard` and `/admin/test-dashboard/run`
- Resource routes for:
  - `bmn`
  - `satker`
  - `barang`
  - `lokasi`
  - `identitas-kategori`
  - `identitas`
  - `atribut`
  - `unitkerja`
  - `unitteknis`
  - `user`
  - `psp`
- Locked data routes:
  - list view
  - datatable feed
  - lock
  - unlock
  - reject request
- Activity log routes:
  - datatable
  - export
  - cleanup
  - resource route
- Settings routes:
  - index
  - update
- Batch delete routes for SIMAN, internal, and invalid data

Authenticated non-admin routes present:

- `PUT /internal/{id}/requestUnlock`
- resource route for `siman`
- SIMAN datatable route
- resource route for `internal`
- internal datatable, make, category lookup, insert, attachment, document, and BAST download routes
- `identitas` category lookup routes
- resource route for `compare`
- compare datatable and export routes
- resource route for `invalid`
- invalid datatable and export routes

### routes/console.php

- Only the default `inspire` command is defined.

### API routes

- No `routes/api.php` file is present in the workspace.

## 6. Middleware

### ActivityLogMiddleware

Observed behavior:

- Skips requests whose path contains `datatable`.
- Runs the downstream request first.
- Logs request metadata into `ActivityLog` afterward.
- Stored fields include:
  - user ID
  - HTTP method
  - full URI
  - route name
  - route parameters
  - status code
  - response content for error responses
  - IP address
  - user agent

### PreventDuplicateSubmissions

Observed behavior:

- Applies only to `POST`, `PUT`, and `PATCH` requests.
- Builds a cache fingerprint from user/session identity, method, path, payload, uploaded files, and optional submission token.
- Blocks repeated submissions for 5 seconds.
- Returns a JSON `429` response for JSON requests.
- Returns a redirect back with a warning flash message for web requests.
- Excluded route names:
  - `actionLogin`
  - `logout`
  - `test-dashboard.run`
  - `activity-logs.cleanup`
  - `settings.update`
  - `internal.store`
  - `siman.store`
  - `internal.addImage`
  - `internal.addDocument`
  - `psp.download`

### Roles

Observed behavior:

- Requires authentication first.
- Reads the authenticated user’s `level->level_name`.
- Normalizes both user role and allowed roles to lowercase.
- Denies access if the role is not in the allowed list.
- Uses SweetAlert flash messages on denial.

### Helpers

`app/Helpers/helpers.php` defines:

- `submission_token()`
- `submission_token_field()`

These support the duplicate-submission middleware.

## 7. Authentication and Authorization

Observed authentication system:

- Web guard only.
- Eloquent user provider using `App\Models\User`.
- Login form is rendered by `LoginController`.
- Login uses `Auth::attempt()` against email and password.
- Session regeneration occurs after successful login.
- Logout uses the `web` guard and invalidates the session.

Observed authorization implementation:

- Route-level role restriction uses the custom `role` middleware.
- The authenticated user’s `level` relation is the basis for role checks.
- `User::isAdmin()` returns true when `level->level_name` is `administrator`.
- A `DataInternalPolicy` class exists with locked-data admin helpers.
- No explicit policy registration file was found in the workspace.

## 8. Controllers

### Auth and utilities

- `LoginController`
  - renders the login view
  - validates credentials
  - attempts auth using email and password
  - regenerates sessions on success
  - logs failed login attempts through `Log`
  - logs out the current web guard

- `BackupController`
  - lists backup ZIP files from `storage/app/backups`
  - runs `backup:run`
  - runs files-only backups
  - downloads backup archives
  - deletes backup archives

- `SettingController`
  - loads settings as key/value pairs
  - updates `admin_phone`, `biro`, `nip_biro`, `kepada`, `jabatan`, and `lokasi`

- `ActivityLogController`
  - renders the activity log list and detail pages
  - provides a DataTables endpoint
  - exports activity logs to XLSX using OpenSpout
  - deletes logs older than 5 years in cleanup

### Reference/master data controllers

- `BmnController`
- `BarangController`
- `SatkerController`
- `LokasiController`
- `IdentitasKategoriController`
- `IdentitasController`
- `AtributController`
- `UnitKerjaController`
- `UnitTeknisController`
- `UserController`

These controllers implement CRUD for their respective lookup tables.

Observed special behavior in this group:

- `AtributController` prevents deletion when the attribute is still used by identitas rows.
- `UserController` includes level and unit-kerja selection and protects user ID 1 from change/delete operations.
- `IdentitasController` exposes category-based and attribute-based lookup endpoints.

### Operational controllers

- `DashboardController`
  - computes dashboard totals and comparison summaries
  - exposes a JSON endpoint for asset condition counts by unit kerja

- `InternalController`
  - imports internal CSV data with idempotency support
  - validates and normalizes asset fields
  - manages internal attachments, document uploads, and image updates
  - supports manual insert/update and delete workflows
  - serves a DataTables feed
  - exports Excel workbooks
  - generates BAST PDFs
  - exposes category lookup JSON

- `SimanController`
  - imports SIMAN CSV data into `SimanBatch` and `simanData`
  - deduplicates by `kode_register`
  - serves a DataTables feed
  - supports batch deletion

- `CompareController`
  - compares internal data and SIMAN data
  - provides match, internal-only, SIMAN-only, and mismatch export flows
  - serves a DataTables feed

- `InvalidController`
  - manages invalid import rows
  - converts an invalid row into `DataInternal`
  - serves a DataTables feed
  - exports invalid data to Excel
  - supports batch deletion

- `LockedDataController`
  - renders the locked-data dashboard
  - serves locked-only DataTables output
  - locks and unlocks internal records
  - accepts unlock requests
  - rejects unlock requests

- `PspController`
  - renders the PSP page
  - generates a PDF based on selected internal data and settings

### Empty or partially implemented controller surfaces

- `Controller.php` is empty.
- `DashboardController` has empty `update` and `destroy` methods.
- `CompareController` has empty `create`, `store`, `show`, `edit`, `update`, and `destroy` methods.
- `InvalidController` has empty `create`, `store`, `show`, and `edit` methods.
- `PspController` has empty `create`, `store`, `show`, `edit`, `update`, and `destroy` methods.
- `BmnController`, `BarangController`, `SatkerController`, `LokasiController`, `IdentitasKategoriController`, `IdentitasController`, and `UserController` each define an empty `show` method.
- `UnitKerjaController`, `UnitTeknisController`, and `AtributController` do not define a `show` method even though resource routes exist for them.

## 9. Models and Services

### Core operational models

- `DataInternal`
  - main internal asset model
  - casts `tgl_perolehan`, `tgl_bahi`, and `nup`
  - fillable fields cover ownership, asset values, status, documentation links, user/party metadata, location notes, and lock/request flags
  - relations to BMN, satker, barang, unit kerja, photos, documents, user detail, location, data attributes, and identitas
  - helper methods:
    - `isComplete()`
    - `hasPhotos()`
    - `shouldBeLocked()`
    - `autoLock()`
    - `isLocked()`
    - `canBeUpdated()`

- `simanData`
  - SIMAN import row model
  - relates to BMN, satker, SIMAN batch, and barang

- `InvalidData`
  - invalid import row model
  - relates to BMN, satker, barang, and unit kerja

- `ImportRun`
  - tracks import idempotency state
  - stores fingerprint, status, response payload, error message, and timestamps

- `SimanBatch`
  - stores SIMAN batch label and source

- `InternalBatch`
  - simple model with `source` fillable

- `ActivityLog`
  - stores request log metadata
  - casts route parameters to array
  - belongs to `User`

- `Setting`
  - simple `key` / `value` model

### Auth and reference models

- `User`
  - authenticatable model
  - fillable: `name`, `email`, `password`, `level_id`, `unit_kerja_id`
  - hidden: `password`, `remember_token`
  - casts `email_verified_at` to datetime and `password` to hashed
  - relations to `Level` and `UnitKerja`
  - `isAdmin()` helper

- `Level`
  - stores `level_name`
  - defines a `users()` relation

- `UnitKerja`
  - stores `name` and `nameId`

- `UnitTeknis`
  - stores `name` and `slug`

- `Barang`
  - stores `kode_barang`, `nama_barang`, `nup`

- `bmn`
  - stores `name`

- `satker`
  - stores `kode_satker` and `nama_satker`

- `LokasiRuang`
  - stores `unit_kerja_id` and `name`

- `IdentitasKategori`
  - stores `name` and `slug`
  - has many `Identitas`

### Identity and attribute models

- `Identitas`
  - stores `name`, `slug`, and `kategori_id`
  - many-to-many relation to `Atribut` through `identitas_atributs`
  - includes pivot metadata: `is_required`, `sort_order`, `placeholder`, `help_text`

- `Atribut`
  - stores `key`, `label`, `data_type`
  - many-to-many relation to `Identitas`

- `IdentitasAtribut`
  - pivot model for identitas-attribute metadata

- `DataAtribut`
  - stores typed values for a `DataInternal` record
  - casts `value_integer` to integer and `value_date` to date

### Attachment / file-related models

- `FotoInternal`
  - stores `data_internal_id`, `filename`, `path`, `title`, `description`, `is_cover`
  - casts `is_cover` to boolean

- `DocumentInternal`
  - stores `data_internal_id`, `filename`, `path`, `title`, `description`

- `Pengguna`
  - stores `data_internal_id`, `foto`, `nama`, `alamat`

### Service layer

- `ImportIdempotencyService`
  - fingerprints uploaded files plus batch/source/user inputs
  - reserves or resumes `ImportRun` records
  - marks imports completed or failed
  - handles unique constraint retry behavior

## 10. Database Schema

Observed tables from migrations and their purpose:

| Table | Observed Columns / Notes |
|---|---|
| `users` | `name`, `email`, `email_verified_at`, `password`, `remember_token`, timestamps; later adds `level_id` and `unit_kerja_id` |
| `password_reset_tokens` | `email` primary key, `token`, `created_at` |
| `sessions` | `id`, `user_id`, `ip_address`, `user_agent`, `payload`, `last_activity` |
| `cache` | `key`, `value`, `expiration` |
| `cache_locks` | `key`, `owner`, `expiration` |
| `jobs` | queue payload storage columns for the database queue driver |
| `job_batches` | batch tracking columns for queued batches |
| `failed_jobs` | failed job metadata including `uuid`, `connection`, `queue`, `payload`, `exception`, `failed_at` |
| `levels` | `level_name` |
| `unit_kerjas` | `name`, `nameId` |
| `unit_teknis` | `name`, `slug` |
| `bmns` | `name` |
| `satkers` | `kode_satker`, `nama_satker` |
| `barangs` | `kode_barang`, `nama_barang`, `nup` |
| `lokasi_ruangs` | `unit_kerja_id`, `name` |
| `identitas_kategoris` | `name`, `slug` |
| `identitas` | `kategori_id`, `name`, `slug` |
| `atributs` | `key`, `label`, `data_type` |
| `identitas_atributs` | `identitas_id`, `atributs_id`, `is_required`, `sort_order`, `placeholder`, `help_text` |
| `data_internals` | main asset table with asset metadata, ownership info, documentation links, status fields, batch/label fields, and later additions for `status`, `ket_lokasi`, `ket_penugasan`, `ket_unit_teknis`, `is_requested`, `is_borrowed`, `nip_pengguna`, `alamat_pengguna`, `jabatan_pengguna`, `nama_pihak_pertama`, `nip_pihak_pertama`, `jabatan_pihak_pertama`, `alamat_pihak_pertama` |
| `data_atributs` | `data_internal_id`, `atributs_id`, `value_string`, `value_integer`, `value_date` |
| `foto_internals` | `data_internal_id`, `filename`, `path`, `title`, `description`, `is_cover` |
| `document_internals` | `data_internal_id`, `filename`, `path`, `title`, `description` |
| `siman_batches` | `label`, `source` |
| `siman_data` | SIMAN import rows with BMN, satker, batch, barang, NUP, valuation, register, location, user, documentation, and opname columns |
| `invalid_data` | rejected import rows with master-data references, asset fields, user/location notes, status fields, batch/label, and description |
| `activity_logs` | request logging table with user, method, URI, route name, route parameters, status code, response content, IP, user agent, timestamps |
| `settings` | `key`, `value` |
| `import_runs` | `source`, `fingerprint`, `user_id`, `batch_label`, `batch_type`, `batch_id`, `status`, `response_status`, `response_payload`, `error_message`, `started_at`, `finished_at` |

Observed constraints and indexes:

- `users.email` is unique.
- `satkers.kode_satker` is unique.
- `satkers.nama_satker` is unique and nullable.
- `unit_teknis.slug` is unique.
- `unit_kerjas.nameId` is unique and nullable.
- `identitas_kategoris.slug` is unique.
- `identitas.slug` is unique.
- `atributs.key` is unique.
- `identitas_atributs` has a unique pair of `identitas_id` and `atributs_id`.
- `data_internals` has a unique pair of `barang_id` and `nup`.
- `data_atributs` has indexes on `atributs_id` + value columns and a unique pair of `data_internal_id` and `atributs_id`.
- `siman_data.kode_register` is unique and nullable.
- `import_runs.fingerprint` is unique.
- `activity_logs` has additional indexes on `created_at`, `user_id`, and `method`.

## 11. Migrations and Schema Change History

Later alter migrations observed in the workspace:

- `data_internals` gained BAST details:
  - `nip_pengguna`
  - `alamat_pengguna`
  - `jabatan_pengguna`
  - `nama_pihak_pertama`
  - `nip_pihak_pertama`
  - `jabatan_pihak_pertama`
  - `alamat_pihak_pertama`
- `data_internals` gained `status` with enum values `draft`, `locked`, `unlocked`.
- `data_internals` gained `ket_lokasi`, `ket_penugasan`, and `ket_unit_teknis`.
- `data_internals` gained `is_requested`.
- `data_internals` gained `is_borrowed`.
- `users` gained `level_id` and `unit_kerja_id` foreign keys.
- `activity_logs` gained indexes on `created_at`, `user_id`, and `method`.

## 12. Console Commands and Scheduling

### app/Console/Kernel.php

Commands registered manually:

- `App\Console\Commands\BackupDatabaseAndFiles`
- `App\Console\Commands\BackupCheck`
- `App\Console\Commands\DeleteOldActivityLogs`
- `App\Console\Commands\RunTestsCommand`

Scheduled tasks:

- `backup:run` every 3 months on the 1st day at 00:00
- `activity-logs:cleanup` daily at 01:00

### Command inventory

- `BackupDatabaseAndFiles`
  - signature: `backup:run`
  - creates a ZIP archive in `storage/app/backups`
  - attempts a MySQL dump using `mysqldump`
  - archives configured paths from `config/backups.php`
  - prunes older backups by retention count

- `BackupCheck`
  - signature: `backup:check`
  - checks `mysqldump`, backup directory writability, PHP extensions, existing backup files, and next scheduled run

- `DeleteOldActivityLogs`
  - signature: `activity-logs:cleanup`
  - deletes logs older than the selected day count, defaulting to 1825 days

- `RunTestsCommand`
  - signature: `run:tests`
  - shells out to Pest in `tests/Feature`
  - can output JSON and cache test results

- `ScheduledJobCommand`
  - signature: `app:scheduled-job-command`
  - empty `handle()` implementation

### Console route

- `routes/console.php` defines the default `inspire` command only.

## 13. Configuration Files

Observed application config highlights:

- `config/app.php`
  - timezone: `Asia/Jakarta`
  - SweetAlert facade alias is defined as `Alert`
  - `admin_phone` is read from the environment with a fallback value
  - Image and Excel facade aliases are commented out

- `config/auth.php`
  - `web` guard uses session driver and `users` provider
  - auth model is `App\Models\User`

- `config/database.php`
  - default connection is `sqlite`
  - connections are defined for sqlite, mysql, mariadb, pgsql, sqlsrv

- `config/queue.php`
  - default queue connection is `database`

- `config/session.php`
  - default session driver is `database`

- `config/logging.php`
  - default channel is `stack`
  - single-file logs write to `storage/logs/laravel.log`

- `config/filesystems.php`
  - default disk is `local`
  - public disk maps to `storage/app/public`
  - public storage symlink is configured

- `config/mail.php`
  - default mailer is `log`

- `config/backups.php`
  - backup paths: `storage/app` and `public/storage`
  - default retention: `6`

- `config/dompdf.php`
  - A4 portrait is the default paper setup

- `config/sweetalert.php`
  - SweetAlert2 theme, timer, width, and middleware settings are configured

- `config/services.php`
  - placeholders exist for Postmark, Resend, SES, and Slack

- `config/scheduledJob.php`
  - empty array

## 14. Environment Variables Observed

The codebase references these env keys across config and runtime code:

- Application: `APP_NAME`, `APP_ENV`, `APP_DEBUG`, `APP_URL`, `APP_LOCALE`, `APP_FALLBACK_LOCALE`, `APP_FAKER_LOCALE`, `APP_KEY`, `APP_PREVIOUS_KEYS`, `APP_MAINTENANCE_DRIVER`, `APP_MAINTENANCE_STORE`, `ADMIN_PHONE`
- Auth: `AUTH_GUARD`, `AUTH_PASSWORD_BROKER`, `AUTH_MODEL`, `AUTH_PASSWORD_RESET_TOKEN_TABLE`, `AUTH_PASSWORD_TIMEOUT`
- Database: `DB_CONNECTION`, `DB_URL`, `DB_HOST`, `DB_PORT`, `DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD`, `DB_SOCKET`, `DB_CHARSET`, `DB_COLLATION`, `DB_FOREIGN_KEYS`, `MYSQL_ATTR_SSL_CA`
- Redis: `REDIS_CLIENT`, `REDIS_CLUSTER`, `REDIS_PREFIX`, `REDIS_PERSISTENT`, `REDIS_URL`, `REDIS_HOST`, `REDIS_USERNAME`, `REDIS_PASSWORD`, `REDIS_PORT`, `REDIS_DB`, `REDIS_CACHE_DB`, `REDIS_MAX_RETRIES`, `REDIS_BACKOFF_ALGORITHM`, `REDIS_BACKOFF_BASE`, `REDIS_BACKOFF_CAP`
- Queue: `QUEUE_CONNECTION`, `DB_QUEUE_CONNECTION`, `DB_QUEUE_TABLE`, `DB_QUEUE`, `DB_QUEUE_RETRY_AFTER`, `BEANSTALKD_QUEUE_HOST`, `BEANSTALKD_QUEUE`, `BEANSTALKD_QUEUE_RETRY_AFTER`, `SQS_PREFIX`, `SQS_QUEUE`, `SQS_SUFFIX`, `AWS_ACCESS_KEY_ID`, `AWS_SECRET_ACCESS_KEY`, `AWS_DEFAULT_REGION`, `REDIS_QUEUE_CONNECTION`, `REDIS_QUEUE`, `REDIS_QUEUE_RETRY_AFTER`, `QUEUE_FAILED_DRIVER`
- Logging: `LOG_CHANNEL`, `LOG_STACK`, `LOG_LEVEL`, `LOG_DEPRECATIONS_CHANNEL`, `LOG_DEPRECATIONS_TRACE`, `LOG_DAILY_DAYS`, `LOG_SLACK_WEBHOOK_URL`, `LOG_SLACK_USERNAME`, `LOG_SLACK_EMOJI`, `LOG_PAPERTRAIL_HANDLER`, `LOG_STDERR_FORMATTER`, `LOG_SYSLOG_FACILITY`, `LOG_DAILY_DAYS`, `PAPERTRAIL_URL`, `PAPERTRAIL_PORT`
- Session: `SESSION_DRIVER`, `SESSION_LIFETIME`, `SESSION_EXPIRE_ON_CLOSE`, `SESSION_ENCRYPT`, `SESSION_CONNECTION`, `SESSION_TABLE`, `SESSION_STORE`, `SESSION_COOKIE`, `SESSION_PATH`, `SESSION_DOMAIN`, `SESSION_SECURE_COOKIE`, `SESSION_HTTP_ONLY`, `SESSION_SAME_SITE`, `SESSION_PARTITIONED_COOKIE`
- Filesystem: `FILESYSTEM_DISK`, `AWS_BUCKET`, `AWS_URL`, `AWS_ENDPOINT`, `AWS_USE_PATH_STYLE_ENDPOINT`
- Mail: `MAIL_MAILER`, `MAIL_SCHEME`, `MAIL_URL`, `MAIL_HOST`, `MAIL_PORT`, `MAIL_USERNAME`, `MAIL_PASSWORD`, `MAIL_EHLO_DOMAIN`, `MAIL_SENDMAIL_PATH`, `MAIL_LOG_CHANNEL`, `MAIL_FROM_ADDRESS`, `MAIL_FROM_NAME`
- Backups and diagnostics: `BACKUP_RETENTION`, `MYSQL_BIN_PATH`
- SweetAlert: `SWEET_ALERT_THEME`, `SWEET_ALERT_CDN`, `SWEET_ALERT_ALWAYS_LOAD_JS`, `SWEET_ALERT_NEVER_LOAD_JS`, `SWEET_ALERT_TIMER`, `SWEET_ALERT_WIDTH`, `SWEET_ALERT_HEIGHT_AUTO`, `SWEET_ALERT_PADDING`, `SWEET_ALERT_BACKGROUND`, `SWEET_ALERT_ANIMATION_ENABLE`, `SWEET_ALERT_ANIMATECSS`, `SWEET_ALERT_CONFIRM_BUTTON`, `SWEET_ALERT_CLOSE_BUTTON`, `SWEET_ALERT_CONFIRM_BUTTON_TEXT`, `SWEET_ALERT_CANCEL_BUTTON_TEXT`, `SWEET_ALERT_TOAST_POSITION`, `SWEET_ALERT_TIMER_PROGRESS_BAR`, and the middleware/custom class keys defined in the config
- Third-party service placeholders: `POSTMARK_API_KEY`, `RESEND_API_KEY`, `SLACK_BOT_USER_OAUTH_TOKEN`, `SLACK_BOT_USER_DEFAULT_CHANNEL`

## 15. Storage, Logging, Queue, and Files

Observed storage usage:

- Logs are written under `storage/logs`.
- Activity-log export is written to `storage/app/activity_logs_export.xlsx`.
- Backups are written to `storage/app/backups`.
- Public uploads are exposed through the `public` filesystem disk and the `public/storage` symlink.
- The backup command also archives `public/storage` and `storage/app`.
- `storage/pest_output.txt` exists in the workspace.

Observed logging implementation:

- Default log channel stack resolves to `single` by default.
- The activity log middleware persists request records to the database.
- Failed login attempts are written through `Log::warning()`.
- Backup and scheduler commands log success and failure through `Log` or console output.

Observed queue implementation:

- Queue connection default is `database`.
- Queue tables exist in migrations.
- No job classes were found in `app/Jobs`.

Observed mail implementation:

- Mail defaults to the `log` mailer.
- No mail classes were found in `app/Mail`.

Observed filesystem implementation:

- The `local` disk stores private files in `storage/app/private`.
- The `public` disk stores files in `storage/app/public` and exposes them through `/storage`.

## 16. Frontend Stack

Observed frontend setup:

- `resources/js/app.js` only imports `bootstrap.js`.
- `resources/js/bootstrap.js` loads Axios and sets the `X-Requested-With` header.
- `resources/css/app.css` uses Tailwind CSS 4 with `@source` directives for Blade and JS scanning.
- The CSS file includes a custom rule for DataTables headers to prevent wrapping and reserve space for sort icons.

Observed UI dependencies and assets:

- SweetAlert is used throughout the controller layer and has a vendor Blade override in `resources/views/vendor/sweetalert/alert.blade.php`.
- Blade templates are used for pages, partials, PDF templates, and error screens.
- `resources/views/welcome.blade.php` exists, but the app routes use the login flow as the entry point.

## 17. Views

The workspace contains 62 Blade files across these observed areas:

- Auth and entry pages:
  - `Login.blade.php`
  - `welcome.blade.php`
- Shared layout and partials:
  - `app.blade.php`
  - `inc/header.blade.php`
  - `inc/footer.blade.php`
  - `inc/sidebar.blade.php`
- Error pages:
  - `errors/validation.blade.php`
  - `errors/session_expired.blade.php`
  - `errors/not_found.blade.php`
  - `errors/general.blade.php`
- Master data views:
  - `bmn/*`
  - `barang/*`
  - `satker/*`
  - `lokasi/*`
  - `unitkerja/*`
  - `unitteknis/*`
  - `atribut/*`
  - `identitas/*`
  - `identitaskategori/*`
  - `user/*`
- Operational views:
  - `dashboard/index.blade.php`
  - `internal/*`
  - `invalid/*`
  - `compare/index.blade.php`
  - `simanData/*`
  - `psp/index.blade.php`
  - `backups/index.blade.php`
  - `settings/index.blade.php`
  - `activity_logs/*`
- PDF templates:
  - `pdf/psp.blade.php`
  - `pdf/bast.blade.php`

## 18. Third-Party Integrations

Observed package-level integrations:

- DomPDF for PDF generation.
- OpenSpout for XLSX export.
- Yajra DataTables for server-side table feeds.
- RealRashid SweetAlert for UI alerts.
- Tailwind CSS 4 and Vite for frontend asset building.
- Axios for request handling on the frontend.
- Bootstrap Icons are installed as an asset package.

Packages present but not bootstrapped through a provider in `bootstrap/providers.php`:

- Intervention Image
- Maatwebsite Excel

## 19. Testing State

Observed testing posture:

- `composer.json` defines a `test` script that clears config and runs `php artisan test`.
- `RunTestsCommand` assumes Pest test files in `tests/Feature`.
- No `tests/` directory exists in the workspace.
- No test classes or Pest files were present in the workspace.
- The workspace does contain `storage/pest_output.txt`, but that is not a source test suite.

## 20. Explicitly Absent Surfaces

The following were not present in the workspace at audit time:

- `routes/api.php`
- `app/Http/Requests`
- `app/Events`
- `app/Listeners`
- `app/Notifications`
- `app/Mail`
- `app/Jobs`
- `tests/`

## 21. Audit Notes

- This is a Laravel 12 application with a strong internal asset management workflow centered on `DataInternal`.
- The codebase is organized around Blade pages, server-side datatables, CSV importers, and document generation rather than an API-first architecture.
- Several controller resource methods are intentionally empty or not implemented.
- The project already contains operational tooling for backups, activity-log cleanup, and test execution, even though the test suite itself is absent from the workspace.
