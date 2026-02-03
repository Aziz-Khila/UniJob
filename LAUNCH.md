# How to Launch UniJob Website

## Prerequisites
- PHP 8.1+ (✅ You have PHP 8.3.29)
- Composer (✅ You have Composer 2.9.2)
- Node.js 18+ (✅ You have Node.js 24.11.1)
- npm (comes with Node.js)

## Quick Start (First Time Setup)

### 1. Install Dependencies

```bash
# Install PHP dependencies
composer install

# Install Node.js dependencies
npm install
```

### 2. Environment Setup

```bash
# Copy .env.example to .env if it doesn't exist
if (!(Test-Path .env)) { Copy-Item .env.example .env }

# Generate application key
php artisan key:generate
```

### 3. Database Setup

```bash
# Create SQLite database file (if using SQLite)
if (!(Test-Path database/database.sqlite)) { New-Item -ItemType File -Path database/database.sqlite }

# Run migrations
php artisan migrate
```

### 4. Build Assets (Choose one)

**For Development (with hot reload):**
```bash
npm run dev
```

**For Production (one-time build):**
```bash
npm run build
```

### 5. Start Laravel Server

**In a new terminal window/tab:**
```bash
php artisan serve
```

The website will be available at: **http://localhost:8000**

---

## Quick Launch (After Initial Setup)

If you've already set everything up, just run:

### Option 1: Manual (Two Terminals)

**Terminal 1:**
```bash
npm run dev
```

**Terminal 2:**
```bash
php artisan serve
```

### Option 2: All-in-One (Recommended for Development)

```bash
composer run dev
```

This runs everything concurrently:
- Laravel server (http://localhost:8000)
- Vite dev server (with hot reload)
- Queue worker
- Logs viewer

---

## Common Commands

### Clear Cache
```bash
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear
```

### Run Migrations
```bash
php artisan migrate
```

### Create Storage Link (for file uploads)
```bash
php artisan storage:link
```

### Reset Everything
```bash
php artisan migrate:fresh
php artisan db:seed  # if you have seeders
```

---

## Troubleshooting

### Port Already in Use
If port 8000 is busy, use a different port:
```bash
php artisan serve --port=8001
```

### Assets Not Loading
1. Make sure Vite dev server is running: `npm run dev`
2. Or rebuild assets: `npm run build`

### Database Errors
1. Check if database file exists: `database/database.sqlite`
2. Run migrations: `php artisan migrate`
3. Check `.env` file has correct database settings

### Permission Issues (Linux/Mac)
```bash
chmod -R 775 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache
```

