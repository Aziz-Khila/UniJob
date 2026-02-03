# Run UniJob on Another Machine (Local Setup from Scratch)

Follow these steps **on the new machine** from the very beginning.

---

## 1. Prerequisites (install once)

Install these if not already present:

| Tool      | Version  | Check command       | Install |
|-----------|----------|---------------------|--------|
| **PHP**   | 8.2+     | `php -v`            | [php.net](https://www.php.net/downloads) or XAMPP/Laragon |
| **Composer** | 2.x  | `composer -v`       | [getcomposer.org](https://getcomposer.org/download/) |
| **Node.js** | 18+   | `node -v`           | [nodejs.org](https://nodejs.org/) (LTS) |
| **npm**   | (with Node) | `npm -v`        | Comes with Node.js |

- **Windows:** PHP needs the `sqlite` extension enabled (check `php.ini`: `extension=sqlite` or `extension=pdo_sqlite`).
- **Linux:** e.g. `sudo apt install php8.2 php8.2-sqlite3 composer nodejs npm`

---

## 2. Get the project on the new machine

**Option A – Copy folder**  
Copy the whole UniJob folder (e.g. via USB, cloud, zip).  
Do **not** copy `node_modules`, `vendor`, or `.env` from the old machine (we’ll recreate them).

**Option B – Git**  
If the project is in a Git repo:

```bash
git clone <your-repo-url> UniJob
cd UniJob
```

---

## 3. Install dependencies

In the project root (where `composer.json` and `package.json` are):

```bash
composer install
npm install
```

---

## 4. Environment and app key

Create `.env` from the example and generate a new key:

```bash
# Windows (PowerShell)
if (!(Test-Path .env)) { Copy-Item .env.example .env }

# Linux / macOS
cp .env.example .env

# Generate Laravel app key (required on every new machine)
php artisan key:generate
```

If you **copied** an old `.env`, still run `php artisan key:generate` so this machine has its own `APP_KEY`.

---

## 5. Database (SQLite)

Create the SQLite file and run migrations:

```bash
# Windows (PowerShell)
if (!(Test-Path database/database.sqlite)) { New-Item -ItemType File -Path database/database.sqlite }

# Linux / macOS
touch database/database.sqlite

# Run migrations
php artisan migrate
```

Ensure `.env` has:

```env
DB_CONNECTION=sqlite
DB_DATABASE=database/database.sqlite
```

(Other DB_* lines can stay commented.)

---

## 6. Build frontend assets

**For development (with hot reload):**

```bash
npm run dev
```

Leave this terminal open. In a **second** terminal, start Laravel (step 7).

**For production-like / one-time build:**

```bash
npm run build
```

Then you only need `php artisan serve` (step 7).

---

## 7. Start the app

**If you ran `npm run dev` (development):**  
Open a **second** terminal in the project root:

```bash
php artisan serve
```

**If you only ran `npm run build`:**  
One terminal is enough:

```bash
php artisan serve
```

Open in browser: **http://localhost:8000**

---

## For the end user: start the site (after setup is done)

If someone else did the one-time setup and you only need to **run the website**:

- **Windows:** Double-click **`Start UniJob.bat`** in the project folder. The site will start and your browser will open at http://localhost:8000. Close the black window to stop the server.

---

## 8. Optional

- **File uploads (e.g. CVs, logos):**  
  `php artisan storage:link`

- **Create an admin user (if your app has it):**  
  `php artisan create:admin-user`  
  (or whatever your project’s command is)

- **Seed demo data (if you have seeders):**  
  `php artisan db:seed`

---

## One-time setup summary (copy-paste order)

Run in the project root on the **new** machine:

```bash
composer install
npm install
cp .env.example .env          # or on Windows: Copy-Item .env.example .env
php artisan key:generate
touch database/database.sqlite   # or on Windows: New-Item -ItemType File -Path database/database.sqlite
php artisan migrate
npm run build
php artisan serve
```

Then open **http://localhost:8000**.

---

## If something goes wrong

- **“No application encryption key”** → Run `php artisan key:generate`.
- **Database error** → Check `database/database.sqlite` exists and `.env` has `DB_CONNECTION=sqlite` and `DB_DATABASE=database/database.sqlite`, then run `php artisan migrate`.
- **Blank page or 500** → Run `php artisan config:clear` and `php artisan cache:clear`.
- **Assets missing** → Run `npm run build` (or keep `npm run dev` running in another terminal).
- **Port 8000 in use** → `php artisan serve --port=8001` (then open http://localhost:8001).
