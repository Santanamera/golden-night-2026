Railway deployment steps — Golden Night 2026

1) Push this repository to GitHub (if not already).

2) Create a Railway project
   - Go to https://railway.app and sign in
   - Create new project -> Deploy from GitHub
   - Connect the repo containing this project

3) Add a MySQL plugin
   - In Railway project: Add Plugin -> MySQL
   - Note the generated connection variables (host, user, password, database, port)

4) Environment variables (set in Railway project Settings -> Variables)
   - APP_URL=https://yourdomain-or-railway-url
   - MYSQLHOST, MYSQLUSER, MYSQLPASSWORD, MYSQLDATABASE, MYSQLPORT
   - MOMO_SUB_KEY (from momodeveloper)
   - MOMO_API_USER (API user UUID)
   - MOMO_API_KEY (API key)
   - MOMO_ENV=sandbox
   - MOMO_BASE_URL=https://sandbox.momodeveloper.mtn.com
   - MOMO_CALLBACK=https://<your-domain>/public/momo_callback.php
   - MOMO_PAYEE_CODE=11111
   - MOMO_PAYEE_NAME="Kenny"

5) Deployment config
   - `railway.json` and `nixpacks.toml` are included and should work with Railway's Nixpacks builder.
   - Start command is `php -S 0.0.0.0:$PORT -t /app` (already in `railway.json`).

6) Initialize the database
   Option A: Use Railway's SQL console and run `database/schema.sql` contents.
   Option B: Open Railway project > Deployments > Console, then run:

```bash
# From project root (after code is deployed)
php bin/init_db.php
```

7) Test endpoints
   - Visit: `https://<your-railway-url>/public/buy-ticket.php`
   - Create a test ticket (use sandbox MoMo credentials or upload proof)

8) MTN MoMo sandbox setup
   - Configure MTN sandbox API credentials securely and set `MOMO_SUB_KEY`, `MOMO_API_USER`, and `MOMO_API_KEY` as environment variables.
   - Do not leave helper credential-generation scripts or setup pages in production.

9) Notes
   - InfinityFree blocks outbound requests — Railway does not.
   - Keep `setup_momo.php` and any credential-printing scripts removed after use.

If you want, provide a Railway project invite or API key and I can finish the var configuration and run the DB init for you.