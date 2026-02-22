# SIMAN App Deployment & Backup Setup Guide

This guide covers deploying the SIMAN app on Linux or Windows with automatic 3-month database + file backups.

---

## Overview

Your backup system includes:
- **Command**: `php artisan backup:run` — creates a ZIP with MySQL dump + project files
- **Schedule**: Runs automatically on the **1st of every 3rd month** (Jan 1, Apr 1, Jul 1, Oct 1) at 00:00
- **Retention**: Keeps the last 6 backups; older ones auto-delete
- **Backups stored**: `storage/app/backups/` (on the server)

The scheduler needs to be **activated separately** on your server.

---

## Deployment Checklist

### 1. General Setup (Both Linux & Windows)

- [ ] Clone/deploy the SIMAN app to your server
- [ ] Install dependencies: `composer install`
- [ ] Set up `.env` file with database credentials and app config
- [ ] Ensure `storage/app/backups/` directory exists and is writable (`chmod 755` on Linux)
- [ ] Add or verify `MYSQL_BIN_PATH` in `.env` if `mysqldump` is not on system PATH
- [ ] Run `php artisan backup:check` to verify all prerequisites

### 2. Platform-Specific Scheduler Setup

---

## Linux Deployment

### Prerequisites
```bash
# Ensure required packages are installed
sudo apt-get install -y php php-cli php-zip php-pdo php-mysql mysql-client

# Verify mysqldump is available
which mysqldump
# Output: /usr/bin/mysqldump
```

### Setup Scheduler (Cron)

Add a cron job to run the scheduler every 5 minutes. This allows Laravel to execute the 3-month backup job automatically.

1. **Open crontab editor** (as the app user, e.g., `www-data`):
```bash
sudo crontab -e -u www-data
```

2. **Add this line** at the end:
```cron
*/5 * * * * cd /var/www/SIMAN-app && /usr/bin/php artisan schedule:run >> /var/log/laravel-schedule.log 2>&1
```

**Explanation**:
- `*/5 * * * *` — runs every 5 minutes
- `cd /var/www/SIMAN-app` — change to your app directory (adjust path)
- `/usr/bin/php artisan schedule:run` — runs Laravel's scheduler
- `>> /var/log/laravel-schedule.log 2>&1` — logs output for debugging

3. **Save and exit** (Ctrl+X, then Y, Enter in nano)

4. **Verify cron was added**:
```bash
sudo crontab -l -u www-data
```

### Test the Scheduler

```bash
# Run a manual backup immediately
cd /var/www/SIMAN-app
php artisan backup:run

# Check diagnostic
php artisan backup:check

# View logs (if logging output)
tail -f /var/log/laravel-schedule.log
```

### Backup Directory Permissions

Ensure the backup directory is writable:
```bash
sudo chown www-data:www-data /var/www/SIMAN-app/storage/app/backups
sudo chmod 755 /var/www/SIMAN-app/storage/app/backups
```

### Restore from Backup (Linux)

```bash
# List available backups
ls -lh /var/www/SIMAN-app/storage/app/backups/

# Extract backup
cd /var/www/SIMAN-app
unzip storage/app/backups/backup_20260401_000000.zip

# Restore the database dump
mysql -h 127.0.0.1 -u root -p database_name < db_20260401_000000.sql
```

---

## Windows Deployment

### Prerequisites

- PHP CLI with `zip`, `pdo`, `pdo_mysql` extensions enabled
- MySQL/MariaDB with `mysqldump.exe` available
- Task Scheduler (built into Windows)

### Verify mysqldump Path

Open PowerShell and check:
```powershell
# Direct path (XAMPP)
Test-Path "C:\xampp\mysql\bin\mysqldump.exe"
# Should return True

# Or if installed elsewhere
Get-Command mysqldump
```

If `mysqldump` is not found, add to `.env`:
```env
MYSQL_BIN_PATH="C:\\xampp\\mysql\\bin\\mysqldump.exe"
```

### Setup Scheduler (Task Scheduler)

**Option A: PowerShell Command (Recommended)**

Run as Administrator:
```powershell
schtasks /Create /SC MINUTE /MO 5 /TN "LaravelSchedule" /TR "\"C:\xampp\php\php.exe\" \"C:\xampp\htdocs\SIMAN\SIMAN-app\artisan\" schedule:run" /F
```

**Adjust paths** if needed:
- `C:\xampp\php\php.exe` — your PHP CLI executable
- `C:\xampp\htdocs\SIMAN\SIMAN-app\artisan` — your project's artisan file

**Option B: Task Scheduler GUI**

1. Open **Task Scheduler** (search "Task Scheduler" in Start)
2. Click **Create Basic Task**
3. **Name**: `LaravelSchedule`
4. **Description**: "Run Laravel scheduler for SIMAN backups"
5. **Trigger**:
   - Select "Recurring"
   - Set to repeat every **5 minutes**
6. **Action**:
   - **Program/script**: `C:\xampp\php\php.exe`
   - **Add arguments**: `C:\xampp\htdocs\SIMAN\SIMAN-app\artisan schedule:run`
   - **Start in**: `C:\xampp\htdocs\SIMAN\SIMAN-app`
7. Click **Finish**
8. Right-click the task → **Properties** → Check "Run with highest privileges"

### Test the Scheduler

```powershell
# Run a manual backup immediately
cd C:\xampp\htdocs\SIMAN\SIMAN-app
php artisan backup:run

# Check diagnostic
php artisan backup:check

# View Task Scheduler logs
# Task Scheduler > select task > right-click > View Log
```

### Backup Directory Permissions

Ensure `storage/app/backups` is writable:
```powershell
# Check current permissions
Get-Acl "C:\xampp\htdocs\SIMAN\SIMAN-app\storage\app\backups"

# If needed, grant permissions to SYSTEM or IIS_IUSRS user
```

### Restore from Backup (Windows)

```powershell
# List available backups
Get-ChildItem "C:\xampp\htdocs\SIMAN\SIMAN-app\storage\app\backups\backup_*.zip"

# Extract backup (use 7-Zip, WinRAR, or PowerShell)
Expand-Archive -Path "C:\xampp\htdocs\SIMAN\SIMAN-app\storage\app\backups\backup_20260401_000000.zip" -DestinationPath "C:\restore"

# Restore the database dump using MySQL CLI
mysql -h 127.0.0.1 -u root -p database_name < C:\restore\db_20260401_000000.sql
```

---

## Environment Configuration

### Required .env Variables

```env
# Database
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=siman_db
DB_USERNAME=root
DB_PASSWORD=your_password

# MySQL binary path (if not on system PATH)
MYSQL_BIN_PATH="C:\\xampp\\mysql\\bin\\mysqldump.exe"    # Windows
# or
MYSQL_BIN_PATH=/usr/bin/mysqldump                        # Linux

# Backup retention (number of backups to keep)
BACKUP_RETENTION=6
```

### Optional: Customize Backup Paths

Edit `config/backups.php` to include additional directories:

```php
'paths' => [
    'storage/app',           // App storage (uploads, etc.)
    'public/storage',        // Public-facing uploads
    'resources/views',       // Blade templates (optional)
    'config',                # Configuration files
],
```

---

## Monitoring & Maintenance

### Verify Scheduler is Running

```bash
# Linux
sudo crontab -l -u www-data
# Should show: */5 * * * * cd /var/www/SIMAN-app && /usr/bin/php artisan schedule:run >> /var/log/laravel-schedule.log 2>&1

# Windows (PowerShell as Admin)
schtasks /Query /TN "LaravelSchedule"
# Should show: Status: Ready
```

### Check Backup Status Anytime

```bash
php artisan backup:check
```

Example output:
```
=== Backup System Diagnostic ===

1. Checking mysqldump...
   ✓ mysqldump found: /usr/bin/mysqldump

2. Checking backup directory...
   ✓ Backup directory writable: /var/www/SIMAN-app/storage/app/backups

3. Checking PHP extensions...
   ✓ zip
   ✓ pdo
   ✓ pdo_mysql

4. Existing backups:
   • backup_20260210_092954.zip (1.9 MB) — 2026-02-10 09:29:56

5. Scheduler status:
   Cron Expression: 0 0 1 */3 * (1st of every 3rd month at 00:00)
   Retention (keep): 6 backups
   Next scheduled run: 2026-04-01 00:00:00
   ✓ Scheduler appears to be running.

=== Summary ===
✓ All prerequisites met. Backup system ready.
```

### Manual Backup Commands

```bash
# Backup immediately
php artisan backup:run

# Backup with custom retention
php artisan backup:run --retention=12

# Backup files only (skip DB dump)
php artisan backup:run --only-files

# Check diagnostics
php artisan backup:check
```

---

## Troubleshooting

### "mysqldump is not recognized"
- **Windows**: Add `MYSQL_BIN_PATH` to `.env` with the full path to `mysqldump.exe`
- **Linux**: Verify `mysql-client` package is installed: `sudo apt-get install mysql-client`

### Backups not being created
1. Run `php artisan backup:check` to diagnose
2. Verify scheduler is active:
   - **Linux**: Check cron with `sudo crontab -l -u www-data`
   - **Windows**: Check Task Scheduler status: `schtasks /Query /TN "LaravelSchedule"`
3. Check backup directory permissions (must be writable)

### Low disk space
- Review `config/backups.php` and reduce `retention` value
- Or compress and archive old backups to external storage
- Use `php artisan backup:run --only-files` to skip DB dumps temporarily

### Restore Issues
- Ensure database user has `CREATE`, `DROP`, `ALTER` privileges
- Close any open connections to the database before restoring
- Verify file permissions on restored files

---

## Advanced: Offsite Backup Storage

To upload backups to S3, Google Drive, or FTP, consider:

1. **Add Laravel backup package**: `composer require spatie/laravel-backup`
2. **Configure remote disk** in `config/filesystems.php` (S3, FTP, etc.)
3. **Update backup config** to upload archives after creation

Example (not included by default):
```php
// After creating backup_*.zip, upload to S3
Storage::disk('s3')->put('backups/' . basename($zipFile), file_get_contents($zipFile));
```

---

## Summary

| Platform | Scheduler | Setup Time | Command |
|----------|-----------|------------|---------|
| **Linux** | Cron | 2 min | `sudo crontab -e -u www-data` + add cron line |
| **Windows** | Task Scheduler | 3 min | PowerShell: `schtasks /Create ...` or GUI |
| **Both** | Manual | 1 min | `php artisan backup:run` |

After deployment:
1. ✓ Set up scheduler (Linux cron or Windows Task Scheduler)
2. ✓ Run `php artisan backup:check`
3. ✓ Verify first backup appears in `storage/app/backups/`
4. ✓ Monitor with `php artisan backup:check` periodically

**Backups will run automatically every 3 months!**
