# Security Assessment

Scope: this assessment is based on the repository contents currently present in the workspace. It covers authentication, authorization, CSRF protection, validation, file uploads, session handling, and environment configuration.

## Summary

The application has several strong baseline controls in place, including Laravel session authentication, route-level role checks, request validation on most write endpoints, and session regeneration on login. The main security concerns are around exposed file storage, unsafe backup file path handling, and a default seeded administrator account.

## Findings

| Severity | Area | Finding | Impact | Recommendation |
|---|---|---|---|---|
| High | Authentication | A default administrator account is created by `database/seeders/DatabaseSeeder.php` with the email `admin@email.com` and password `admin123`. | If the seeder is run in a non-local environment or the credentials are not changed immediately, an attacker can gain administrative access with a known password. | Remove hard-coded production credentials from seeders. If a bootstrap admin account is required, generate a temporary password or force password change on first login. Gate seeding so the admin account is only created in local/dev environments. |
| High | File uploads / data exposure | Internal images and documents are stored on the public filesystem disk and rendered through public URLs in `InternalController`. The code writes files to `storage/app/public` and exposes them with `Storage::url(...)` / `asset(...)`. | Sensitive internal documents and images can be retrieved directly if an attacker learns or guesses the path. This creates a confidentiality risk for asset records and uploaded attachments. | Store sensitive uploads on a private disk, serve them through authenticated controller actions, and use authorization checks before streaming or downloading files. Avoid exposing raw storage paths in views or JSON responses. |
| High | File handling | `BackupController::download()` and `BackupController::destroy()` build filesystem paths by concatenating the unvalidated `filename` route parameter into `storage_path('app/backups/' . $filename)`. | A crafted filename can potentially break out of the backup directory, leading to unauthorized file read or deletion depending on how the web server and route parameter handling resolve the path. | Validate the filename against a strict allowlist, resolve the real path, and verify it stays inside the backup directory before downloading or deleting. Consider using stored backup metadata instead of raw route parameters. |
| Medium | File uploads | Uploaded file names and storage paths are assembled from user-controlled values such as `title`, `merk`, `tipe`, and `name` in `InternalController`. | Malformed path segments or unsafe characters can produce unpredictable file paths and make file management harder to reason about. In combination with public storage, this increases the blast radius of any upload handling weakness. | Sanitize every user-controlled path component with a restrictive allowlist before building filenames. Prefer generated identifiers over human-readable components for actual storage paths. |
| Medium | Session handling | The application uses database-backed sessions with `SESSION_ENCRYPT` defaulting to `false` in `config/session.php`. | Session data is stored server-side, but if the session table or database is compromised, session contents remain readable unless transport and database security are strong. | Keep the database and backups protected, enforce HTTPS in production, and consider enabling session encryption if sensitive session data is stored. Ensure `SESSION_SECURE_COOKIE=true` in production. |

## Area Review

### Authentication

Observed controls:

- Login uses Laravel authentication via `Auth::attempt()`.
- The session is regenerated after successful login.
- Logout invalidates the session and regenerates the CSRF token.
- Login requests are throttled at the route level with `throttle:5,1`.

Assessment:

- No high-risk authentication bypass was observed in the login flow itself.
- The seeded admin account is the main authentication risk.

### Authorization

Observed controls:

- Authenticated routes are wrapped in the `auth` middleware.
- Administrator-only routes are protected by the custom `role:administrator` middleware.
- Internal record changes also perform record-level access checks in controller code.

Assessment:

- Route-level authorization is present and broadly consistent.
- The main authorization concern is not a bypass in the middleware, but the exposure created by public file storage and the default seeded admin credentials.

### CSRF Protection

Observed controls:

- The application uses Laravel web routes, which include the framework’s default CSRF protection.
- No custom CSRF exemptions were found in the application code.

Assessment:

- No explicit CSRF bypass was identified in the repository.
- Standard Laravel CSRF protection appears to be in effect for web form submissions.

### Validation

Observed controls:

- Most controllers validate incoming requests with `Request::validate()` or `Validator::make()`.
- File uploads are validated for type and size before storage.
- Several import flows validate row contents and numeric values.

Assessment:

- Validation coverage is generally good.
- The main validation-related issue is the lack of strict allowlisting for path components that are used when building storage filenames.

### File Uploads

Observed controls:

- Internal images are restricted to image types and size limits.
- Internal documents are restricted to PDFs and size limits.
- Profile images are restricted to image types and size limits.

Assessment:

- Content-type validation exists, but storage location and filename construction create the main risk.
- Public storage should be treated as non-sensitive only.

### Session Handling

Observed controls:

- Session driver defaults to `database`.
- Login regenerates the session.
- Logout invalidates the session.
- Cookies are HTTP-only by default and `same_site` defaults to `lax`.

Assessment:

- Session fixation protection is present through regeneration on login.
- Production deployments should force secure cookies over HTTPS and protect the database-backed session store.

### Environment Configuration

Observed controls:

- `APP_DEBUG` defaults to `false`.
- `APP_ENV` defaults to `production` unless overridden.
- `ADMIN_PHONE` has a safe fallback value.
- The application uses environment-driven configuration for mail, queue, database, and storage.

Assessment:

- No insecure debug configuration was found in the code defaults.
- The biggest environment risk is operational: production should explicitly set secure cookie and HTTPS-related settings, and should not run with the seeded default admin credentials.

## Priority Remediation Plan

1. Remove the hard-coded admin bootstrap account or make it local-only.
2. Move internal attachments and document storage off the public disk.
3. Lock down backup download and delete paths with strict filename validation.
4. Sanitize upload-derived filename components and prefer generated storage names.
5. Enforce secure-cookie and HTTPS settings in production.

## Positive Notes

- Login regenerates the session after successful authentication.
- Logout invalidates the session and CSRF token.
- Route-level authorization is present.
- Request validation is broadly used across the application.
- No custom CSRF bypass was identified.
