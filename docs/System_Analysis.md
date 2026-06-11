# System Overview

SIMAN App is a Blade-first Laravel 12 application for internal asset administration. The source code shows a system centered on three main operational areas: internal asset records, SIMAN import/comparison workflows, and administrative maintenance tasks such as backups, activity logging, and settings management.

The application is server-rendered and uses standard web routes rather than a dedicated API layer. Core evidence for this is in [routes/web.php](../routes/web.php), [bootstrap/app.php](../bootstrap/app.php), [app/Http/Controllers/InternalController.php](../app/Http/Controllers/InternalController.php), [app/Http/Controllers/SimanController.php](../app/Http/Controllers/SimanController.php), and [app/Http/Controllers/CompareController.php](../app/Http/Controllers/CompareController.php).

## User Roles

### Authenticated User

Authenticated users can access the protected web area, including the dashboard, internal data, SIMAN data, comparison pages, invalid data, request-unlock actions, and exports. Evidence: [routes/web.php](../routes/web.php), [app/Http/Controllers/LoginController.php](../app/Http/Controllers/LoginController.php), [app/Http/Controllers/DashboardController.php](../app/Http/Controllers/DashboardController.php).

### Administrator

The code defines an administrator role through the custom `role:administrator` middleware and the `User::isAdmin()` helper. Administrator-only routes include backups, master data CRUD, activity log maintenance, settings, locked-data management, PSP generation, and batch deletion routes. Evidence: [app/Http/Middleware/Roles.php](../app/Http/Middleware/Roles.php), [app/Models/User.php](../app/Models/User.php), [routes/web.php](../routes/web.php).

### Guest / Unauthenticated User

Unauthenticated users can only reach the login page and the login submission endpoint. Evidence: [routes/web.php](../routes/web.php), [app/Http/Controllers/LoginController.php](../app/Http/Controllers/LoginController.php).

### Not Determined From Source Code

No separate business-owner, reviewer, approver, or auditor role class was found outside the administrator and authenticated-user behavior above. Evidence inspected: [app/Models/User.php](../app/Models/User.php), [app/Http/Middleware/Roles.php](../app/Http/Middleware/Roles.php), [routes/web.php](../routes/web.php).

## Business Processes

### Authentication and Session Start

Users submit email and password through the login form. The login controller validates the input, calls `Auth::attempt()`, regenerates the session on success, and logs failed attempts. Logout invalidates the session and regenerates the CSRF token. Evidence: [app/Http/Controllers/LoginController.php](../app/Http/Controllers/LoginController.php), [resources/views/login.blade.php](../resources/views/login.blade.php), [config/auth.php](../config/auth.php), [config/session.php](../config/session.php).

### Internal Asset Management

The internal asset workflow supports listing, creating, editing, inserting, deleting, datatable feeds, photo uploads, document uploads, BAST PDF download, Excel export, and batch deletion. The controller also normalizes numeric/date input, checks duplicates during CSV import, and reserves imports through the idempotency service. Evidence: [app/Http/Controllers/InternalController.php](../app/Http/Controllers/InternalController.php), [app/Services/ImportIdempotencyService.php](../app/Services/ImportIdempotencyService.php), [app/Models/DataInternal.php](../app/Models/DataInternal.php), [routes/web.php](../routes/web.php).

### SIMAN Import

SIMAN data is imported from CSV, validated, de-duplicated by `kode_register`, grouped into batches, and stored as `siman_data` rows. The import path also uses the idempotency service so repeated uploads can be recognized. Evidence: [app/Http/Controllers/SimanController.php](../app/Http/Controllers/SimanController.php), [app/Services/ImportIdempotencyService.php](../app/Services/ImportIdempotencyService.php), [app/Models/simanData.php](../app/Models/simanData.php), [app/Models/SimanBatch.php](../app/Models/SimanBatch.php).

### Comparison and Reporting

The comparison workflow joins internal data and SIMAN data to classify rows as match, internal-only, SIMAN-only, or mismatch variants. The dashboard summarizes these counts and exposes export links. Evidence: [app/Http/Controllers/CompareController.php](../app/Http/Controllers/CompareController.php), [app/Http/Controllers/DashboardController.php](../app/Http/Controllers/DashboardController.php), [resources/views/dashboard/index.blade.php](../resources/views/dashboard/index.blade.php).

### Invalid Data Correction

Rows that are rejected or otherwise invalid can be reviewed, updated, converted into internal data, or deleted in batch. Evidence: [app/Http/Controllers/InvalidController.php](../app/Http/Controllers/InvalidController.php), [app/Models/InvalidData.php](../app/Models/InvalidData.php), [resources/views/invalid/index.blade.php](../resources/views/invalid/index.blade.php).

### Locked Data Workflow

Internal data can be locked, unlocked, and marked for unlock request. The locked-data page shows only rows with `status = locked` and includes request state filtering. Evidence: [app/Http/Controllers/LockedDataController.php](../app/Http/Controllers/LockedDataController.php), [app/Models/DataInternal.php](../app/Models/DataInternal.php), [routes/web.php](../routes/web.php), [resources/views/internal/locked.blade.php](../resources/views/internal/locked.blade.php).

### Administrative Maintenance

Administrators can manage backups, activity logs, settings, master data, PSP documents, and batch deletions. Evidence: [app/Http/Controllers/BackupController.php](../app/Http/Controllers/BackupController.php), [app/Http/Controllers/ActivityLogController.php](../app/Http/Controllers/ActivityLogController.php), [app/Http/Controllers/SettingController.php](../app/Http/Controllers/SettingController.php), [app/Http/Controllers/PspController.php](../app/Http/Controllers/PspController.php), [routes/web.php](../routes/web.php).

## Feature Inventory

- Login and logout.
- Role-based route protection.
- Dashboard summary cards and status counts.
- Internal asset CRUD and imports.
- Internal photo and document management.
- Internal BAST PDF generation.
- SIMAN CSV import and SIMAN listing.
- Internal versus SIMAN comparison views.
- Export flows for Excel and PDF outputs.
- Invalid data review and conversion.
- Locked-data review, lock, unlock, and unlock request handling.
- Activity logging and activity-log export.
- Backup creation, listing, download, and deletion.
- Settings management for PSP/output metadata.
- Master data CRUD for BMN, satker, barang, lokasi, identitas kategori, identitas, atribut, unit kerja, unit teknis, and users.

Evidence: [routes/web.php](../routes/web.php), [app/Http/Controllers/DashboardController.php](../app/Http/Controllers/DashboardController.php), [app/Http/Controllers/InternalController.php](../app/Http/Controllers/InternalController.php), [app/Http/Controllers/SimanController.php](../app/Http/Controllers/SimanController.php), [app/Http/Controllers/CompareController.php](../app/Http/Controllers/CompareController.php), [app/Http/Controllers/InvalidController.php](../app/Http/Controllers/InvalidController.php), [app/Http/Controllers/LockedDataController.php](../app/Http/Controllers/LockedDataController.php), [app/Http/Controllers/BackupController.php](../app/Http/Controllers/BackupController.php), [app/Http/Controllers/ActivityLogController.php](../app/Http/Controllers/ActivityLogController.php), [app/Http/Controllers/SettingController.php](../app/Http/Controllers/SettingController.php), [app/Http/Controllers/PspController.php](../app/Http/Controllers/PspController.php).

## Page Inventory

### Entry and Authentication Pages

- Login page: [resources/views/login.blade.php](../resources/views/login.blade.php) and [resources/views/Login.blade.php](../resources/views/Login.blade.php).
- Public welcome page: [resources/views/welcome.blade.php](../resources/views/welcome.blade.php).
- Error pages: [resources/views/errors/validation.blade.php](../resources/views/errors/validation.blade.php), [resources/views/errors/session_expired.blade.php](../resources/views/errors/session_expired.blade.php), [resources/views/errors/not_found.blade.php](../resources/views/errors/not_found.blade.php), [resources/views/errors/general.blade.php](../resources/views/errors/general.blade.php).

### Dashboard and Operational Pages

- Dashboard: [resources/views/dashboard/index.blade.php](../resources/views/dashboard/index.blade.php).
- Internal asset pages: [resources/views/internal/index.blade.php](../resources/views/internal/index.blade.php), [resources/views/internal/create.blade.php](../resources/views/internal/create.blade.php), [resources/views/internal/edit.blade.php](../resources/views/internal/edit.blade.php), [resources/views/internal/view.blade.php](../resources/views/internal/view.blade.php), [resources/views/internal/make.blade.php](../resources/views/internal/make.blade.php), [resources/views/internal/locked.blade.php](../resources/views/internal/locked.blade.php).
- SIMAN pages: [resources/views/simanData/index.blade.php](../resources/views/simanData/index.blade.php), [resources/views/simanData/create.blade.php](../resources/views/simanData/create.blade.php).
- Comparison page: [resources/views/compare/index.blade.php](../resources/views/compare/index.blade.php).
- Invalid data page: [resources/views/invalid/index.blade.php](../resources/views/invalid/index.blade.php).
- PSP page: [resources/views/psp/index.blade.php](../resources/views/psp/index.blade.php).
- Backups page: [resources/views/backups/index.blade.php](../resources/views/backups/index.blade.php).
- Settings page: [resources/views/settings/index.blade.php](../resources/views/settings/index.blade.php).
- Activity log pages: [resources/views/activity_logs/index.blade.php](../resources/views/activity_logs/index.blade.php), [resources/views/activity_logs/show.blade.php](../resources/views/activity_logs/show.blade.php).

### Master Data Pages

- BMN: [resources/views/bmn/index.blade.php](../resources/views/bmn/index.blade.php), [resources/views/bmn/create.blade.php](../resources/views/bmn/create.blade.php), [resources/views/bmn/edit.blade.php](../resources/views/bmn/edit.blade.php).
- Barang: [resources/views/barang/index.blade.php](../resources/views/barang/index.blade.php), [resources/views/barang/create.blade.php](../resources/views/barang/create.blade.php), [resources/views/barang/edit.blade.php](../resources/views/barang/edit.blade.php).
- Satker: [resources/views/satker/index.blade.php](../resources/views/satker/index.blade.php), [resources/views/satker/create.blade.php](../resources/views/satker/create.blade.php), [resources/views/satker/edit.blade.php](../resources/views/satker/edit.blade.php).
- Lokasi: [resources/views/lokasi/index.blade.php](../resources/views/lokasi/index.blade.php), [resources/views/lokasi/create.blade.php](../resources/views/lokasi/create.blade.php), [resources/views/lokasi/edit.blade.php](../resources/views/lokasi/edit.blade.php).
- Identitas kategori: [resources/views/identitaskategori/index.blade.php](../resources/views/identitaskategori/index.blade.php), [resources/views/identitaskategori/create.blade.php](../resources/views/identitaskategori/create.blade.php), [resources/views/identitaskategori/edit.blade.php](../resources/views/identitaskategori/edit.blade.php).
- Identitas: [resources/views/identitas/index.blade.php](../resources/views/identitas/index.blade.php), [resources/views/identitas/create.blade.php](../resources/views/identitas/create.blade.php), [resources/views/identitas/edit.blade.php](../resources/views/identitas/edit.blade.php).
- Atribut: [resources/views/atribut/index.blade.php](../resources/views/atribut/index.blade.php), [resources/views/atribut/create.blade.php](../resources/views/atribut/create.blade.php), [resources/views/atribut/edit.blade.php](../resources/views/atribut/edit.blade.php).
- Unit kerja: [resources/views/unitkerja/index.blade.php](../resources/views/unitkerja/index.blade.php), [resources/views/unitkerja/create.blade.php](../resources/views/unitkerja/create.blade.php), [resources/views/unitkerja/edit.blade.php](../resources/views/unitkerja/edit.blade.php).
- Unit teknis: [resources/views/unitteknis/index.blade.php](../resources/views/unitteknis/index.blade.php), [resources/views/unitteknis/create.blade.php](../resources/views/unitteknis/create.blade.php), [resources/views/unitteknis/edit.blade.php](../resources/views/unitteknis/edit.blade.php).
- Users: [resources/views/user/index.blade.php](../resources/views/user/index.blade.php), [resources/views/user/create.blade.php](../resources/views/user/create.blade.php), [resources/views/user/edit.blade.php](../resources/views/user/edit.blade.php).

### Shared and Partial Pages

- Main layout: [resources/views/app.blade.php](../resources/views/app.blade.php).
- Shared header/footer/sidebar: [resources/views/inc/header.blade.php](../resources/views/inc/header.blade.php), [resources/views/inc/footer.blade.php](../resources/views/inc/footer.blade.php), [resources/views/inc/sidebar.blade.php](../resources/views/inc/sidebar.blade.php).
- SweetAlert override: [resources/views/vendor/sweetalert/alert.blade.php](../resources/views/vendor/sweetalert/alert.blade.php).
- PDF templates: [resources/views/pdf/psp.blade.php](../resources/views/pdf/psp.blade.php), [resources/views/pdf/bast.blade.php](../resources/views/pdf/bast.blade.php).

### Not Determined From Source Code

The views confirm many pages and templates, but no dedicated frontend router or SPA page inventory exists. The application appears to rely on Blade templates rendered by controllers. Evidence: [resources/views](../resources/views), [routes/web.php](../routes/web.php).

## Route Inventory

The application exposes its endpoint surface through [routes/web.php](../routes/web.php). No [routes/api.php](../routes/api.php) file is present in the repository.

### Authentication Routes

- `GET /` returns the login view.
- `GET /login` shows the login page.
- `POST /actionLogin` submits login credentials.
- `POST /logout` logs out the current user.

### Dashboard Routes

- `GET /dashboard` resource route.
- `GET /dashboard/kondisi-barang-status` JSON status counts.

### Administrator Routes

- Backup dashboard, backup run, backup download, and backup delete routes.
- Test dashboard routes.
- Resource routes for BMN, satker, barang, lokasi, identitas kategori, identitas, atribut, unit kerja, unit teknis, user, and PSP.
- Locked-data routes for listing, datatable, lock, unlock, and reject-request.
- Activity-log datatable, export, cleanup, and resource routes.
- Settings index and update routes.
- Batch-delete routes for SIMAN, internal, and invalid data.

### Operational Routes

- `PUT /internal/{id}/requestUnlock`
- Resource route for SIMAN data.
- SIMAN datatable route.
- Resource route for internal data.
- Internal datatable, make, category lookup, insert, image, document, BAST, and export routes.
- Identitas lookup routes.
- Resource route for compare.
- Comparison datatable and export routes.
- Resource route for invalid data.
- Invalid datatable and export routes.

Evidence: [routes/web.php](../routes/web.php).

## Database Structure

The schema is built around internal asset management, SIMAN import batches, invalid rows, activity logs, settings, and import idempotency. The database defaults are defined in [config/database.php](../config/database.php), [config/session.php](../config/session.php), [config/filesystems.php](../config/filesystems.php), and [database/migrations](../database/migrations).

### Main Tables

- Framework tables: users, password reset tokens, sessions, cache, cache locks, jobs, job batches, failed jobs.
- Reference tables: levels, unit_kerjas, unit_teknis, bmns, satkers, barangs, lokasi_ruangs, settings.
- Identity tables: identitas_kategoris, identitas, atributs, identitas_atributs, data_atributs.
- Internal asset tables: data_internals, foto_internals, document_internals.
- SIMAN tables: siman_batches, siman_data.
- Error/queue/import tables: invalid_data, activity_logs, import_runs.

### Observed Relationships

- `User` belongs to `Level` and `UnitKerja`.
- `DataInternal` belongs to satker, barang, unit kerja, lokasi ruang, identitas, and unit teknis, and has many photos, documents, and data attributes.
- `Identitas` belongs to `IdentitasKategori` and relates to `Atribut` through `identitas_atributs`.
- `simanData` belongs to bmn, satker, barang, and siman batch.
- `InvalidData` belongs to bmn, satker, barang, and unit kerja.

Evidence: [app/Models/User.php](../app/Models/User.php), [app/Models/DataInternal.php](../app/Models/DataInternal.php), [app/Models/ActivityLog.php](../app/Models/ActivityLog.php), [app/Models/ImportRun.php](../app/Models/ImportRun.php), [database/migrations](../database/migrations).

## Technical Architecture

### Framework and Runtime

The application is configured in [bootstrap/app.php](../bootstrap/app.php) with global activity logging middleware and aliases for role checks and duplicate-submission prevention. Providers are registered in [bootstrap/providers.php](../bootstrap/providers.php).

### Authentication and Authorization Stack

- Authentication uses the `web` guard and the `User` Eloquent model.
- Sessions are database-backed by default.
- Route authorization is handled by the custom `Roles` middleware.
- Duplicate form submissions are blocked by `PreventDuplicateSubmissions`.
- Request activity is recorded by `ActivityLogMiddleware`.

### Storage and Export Stack

- Filesystem defaults to the local disk.
- Public uploads are exposed through the `public` disk and `public/storage` symlink.
- PDF generation uses DomPDF.
- XLSX generation uses OpenSpout.
- DataTables JSON endpoints use Yajra DataTables.
- Alerts use RealRashid SweetAlert.

### Scheduling and Commands

The console kernel registers backup and cleanup jobs. Scheduled tasks run `backup:run` every three months and `activity-logs:cleanup` daily. Evidence: [app/Console/Kernel.php](../app/Console/Kernel.php).

### Configuration Evidence

- [config/auth.php](../config/auth.php)
- [config/session.php](../config/session.php)
- [config/database.php](../config/database.php)
- [config/filesystems.php](../config/filesystems.php)
- [config/backups.php](../config/backups.php)
- [config/app.php](../config/app.php)

## Authentication Flow

1. The guest opens the login page at `/login`.
2. The login form posts email and password to `actionLogin`.
3. `LoginController` validates the inputs and calls `Auth::attempt()`.
4. On success, the session is regenerated and the user is redirected to the dashboard.
5. On failure, the controller writes a warning log and returns validation errors.
6. Logout invalidates the session and regenerates the CSRF token.

Evidence: [app/Http/Controllers/LoginController.php](../app/Http/Controllers/LoginController.php), [resources/views/login.blade.php](../resources/views/login.blade.php), [config/auth.php](../config/auth.php), [config/session.php](../config/session.php).

## Authorization Flow

1. Authenticated routes are wrapped in the `auth` and `prevent.duplicate` middleware group.
2. Administrator routes are additionally protected by `role:administrator`.
3. `Roles` checks `Auth::user()->level->level_name` and blocks non-matching roles.
4. Some data operations also apply controller-level state checks, such as locked-data transitions.

Evidence: [app/Http/Middleware/Roles.php](../app/Http/Middleware/Roles.php), [routes/web.php](../routes/web.php), [app/Models/User.php](../app/Models/User.php), [app/Http/Controllers/LockedDataController.php](../app/Http/Controllers/LockedDataController.php).

## Important Notes

- The application is intentionally Blade-driven and not API-first.
- There is no dedicated `routes/api.php` file.
- No `tests/` directory was found in the source tree.
- No `app/Mail`, `app/Jobs`, `app/Events`, `app/Listeners`, `app/Notifications`, or `app/Http/Requests` directory was found in the source tree.
- Public file storage is used for uploads and generated files, so the codebase should be treated as file-heavy rather than database-only.
- The most important workflow surfaces are internal assets, SIMAN imports, comparison reports, locked data, backups, and activity logging.
- Where a behavior was not visible in source code, this document uses the label Not Determined From Source Code instead of inferring intent.

Evidence: [routes/web.php](../routes/web.php), [bootstrap/app.php](../bootstrap/app.php), [resources/views](../resources/views), [database/migrations](../database/migrations), [app/Http/Controllers](../app/Http/Controllers).
