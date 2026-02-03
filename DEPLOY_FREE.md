# Host UniJob for Free (from zero)

This guide gets your UniJob site live for free using **Render** (app + PostgreSQL). The free tier spins down after ~15 minutes of no traffic; the first visit after that may take 30–60 seconds to wake up.

---

## What you need

- A **GitHub** account  
- A **Render** account (free at [render.com](https://render.com))  
- Your UniJob code in a **Git repository** (GitHub; GitLab also works on Render)

---

## Step 1: Put the project on GitHub

1. Create a new repository on GitHub (e.g. `UniJob`). Do **not** add a README if you already have the project locally.
2. On your machine, in the UniJob folder, run:

   ```bash
   git init
   git add .
   git commit -m "Initial commit"
   git branch -M main
   git remote add origin https://github.com/YOUR_USERNAME/UniJob.git
   git push -u origin main
   ```

   Replace `YOUR_USERNAME` with your GitHub username. If the repo already exists and has commits, just ensure everything is pushed:

   ```bash
   git add .
   git commit -m "Add Docker and deploy config"
   git push
   ```

---

## Step 2: Create a free PostgreSQL database on Render

1. Go to [dashboard.render.com](https://dashboard.render.com) and sign in (or sign up with GitHub).
2. Click **New +** → **PostgreSQL**.
3. Choose a **Name** (e.g. `unijob-db`).
4. **Region**: pick one close to you.
5. Leave **PostgreSQL Version** as default.
6. Under **Plan**, select **Free**.
7. Click **Create Database**.
8. Wait until the DB is **Available**. Open it and copy the **Internal Database URL** (starts with `postgres://`). You’ll use it in the next step.  
   - Use **Internal**, not External, so the app and DB talk inside Render’s network.

---

## Step 3: Create a free Web Service on Render (the app)

1. In the Render dashboard, click **New +** → **Web Service**.
2. **Connect** your GitHub account if needed, then select the **UniJob** repository.
3. Configure the service:
   - **Name**: e.g. `unijob`
   - **Region**: same as the database.
   - **Branch**: `main`.
   - **Runtime**: **Docker** (required for this setup).
   - **Plan**: **Free**.

4. **Build & deploy** (leave these as-is unless you use a different structure):
   - **Dockerfile Path**: `Dockerfile` (root of the repo).
   - **Docker Context**: leave blank (root).

5. **Environment variables** (click **Advanced** → **Environment**), then add:

   | Key             | Value |
   |-----------------|--------|
   | `APP_KEY`       | Run locally: `php artisan key:generate --show` and paste the output (e.g. `base64:...`). |
   | `APP_ENV`       | `production` |
   | `APP_DEBUG`     | `false` |
   | `APP_URL`       | Leave empty for now; after first deploy you’ll set it to your Render URL, e.g. `https://unijob.onrender.com`. |
   | `DB_CONNECTION`  | `pgsql` |
   | `DATABASE_URL`   | The **Internal Database URL** you copied from the PostgreSQL service (starts with `postgres://`). |
   | `LOG_CHANNEL`   | `stderr` |

   Optional but recommended:

   | Key               | Value |
   |-------------------|--------|
   | `SESSION_DRIVER`   | `database` |
   | `CACHE_STORE`     | `database` |
   | `QUEUE_CONNECTION`| `database` |

6. Click **Create Web Service**. Render will build the Docker image and deploy. The first deploy can take several minutes.

7. When the service is **Live**, open its URL (e.g. `https://unijob.onrender.com`). Set **APP_URL** in the service’s **Environment** to this URL (with `https://`) and **Save**; trigger a redeploy if needed so the app uses the correct URL.

---

## Step 4: Create an admin user (after first deploy)

The app uses the database on Render; there are no users yet. Create an admin from your **local** project using the **same** database URL once, or add a deploy hook/script later.

**Option A – From your PC (one-time, with Render’s DB)**  
Temporarily in your local `.env` set:

- `DB_CONNECTION=pgsql`  
- `DATABASE_URL=<paste the External Database URL from Render>`

Then run:

```bash
php artisan admin:create --email=admin@example.com --password=admin123
```

Then remove or revert those from `.env` so you don’t use the live DB locally by mistake.

**Option B – After you add a “create admin” route or script**  
You could add a one-time artisan command or a protected route that creates the admin on first run; that’s optional and not covered here.

---

## Step 5: Keep the free service awake (optional)

On the free plan, the Web Service spins down after about 15 minutes of no requests. The next request can be slow (cold start).

To reduce that:

- Use [UptimeRobot](https://uptimerobot.com) (free): add a monitor for `https://your-app.onrender.com` with a 5–10 minute check interval.  
- Or use [Cron-job.org](https://cron-job.org): create a job that hits your app URL every 10–15 minutes.

---

## Summary checklist

| Step | Action |
|------|--------|
| 1 | Push UniJob to GitHub (with Dockerfile, .dockerignore, and code). |
| 2 | Create a **PostgreSQL** database on Render (Free), copy **Internal** URL. |
| 3 | Create a **Web Service** on Render (Docker, Free), add env vars (APP_KEY, APP_URL, DATABASE_URL, DB_CONNECTION=pgsql, etc.). |
| 4 | Set **APP_URL** to your live URL after first deploy; create admin user (e.g. locally with Render DB URL once). |
| 5 | (Optional) Use UptimeRobot or Cron-job.org to ping the app every 10–15 minutes. |

---

## If something goes wrong

- **Build fails**  
  Check the **Logs** tab on the Web Service. Typical issues: missing PHP extension (we use Alpine and install pdo_pgsql, zip, gd, etc. in the Dockerfile), or Node/npm errors in the frontend build stage.

- **“No application encryption key”**  
  Set **APP_KEY** in Render to the output of `php artisan key:generate --show`.

- **Database connection error**  
  Use the **Internal Database URL** (not External) and ensure **DB_CONNECTION** is `pgsql` and **DATABASE_URL** is set. Wait for the DB to be **Available** before deploying the app.

- **Blank or 500 page**  
  Set **APP_DEBUG** to `true` temporarily, redeploy, and check the **Logs** tab for the error. Then set **APP_DEBUG** back to `false`.

- **Assets (CSS/JS) missing**  
  The Dockerfile runs `npm run build` and copies `public/build` into the image. If you changed frontend code, push and let Render rebuild. Ensure **APP_URL** is correct (HTTPS) so asset URLs are correct.

- **Port or URL**  
  Render sets **PORT**; the Dockerfile uses it. Don’t change the CMD port logic unless you know what you’re doing.

---

## Cost

- **Render Web Service (Free)** and **PostgreSQL (Free)** are enough to run UniJob.
- Optional: UptimeRobot / Cron-job.org free tiers to ping the app and reduce cold starts.

---

## File uploads (CVs, profile pictures)

On Render’s free tier, the app’s disk is **ephemeral**: files in `storage/app/public` are lost on every redeploy or when the service restarts. So uploaded CVs, identity/student cards, and profile pictures will not persist. The database (users, jobs, etc.) **does** persist. To keep uploads permanently you’d need a persistent disk (paid) or an external store (e.g. S3). For a demo or light use, the free setup is still fine.

---

You now have the steps from zero to host the website for free.
