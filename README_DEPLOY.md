Deployment instructions

1) GitHub Actions (automated build + push)
- On push to `main` or `master`, GitHub Actions will build and push Docker images to GitHub Container Registry (GHCR).
- Image tags: `ghcr.io/<org>/prom-system:latest` and `ghcr.io/<org>/prom-system:<sha>`

2) Local build
- Build locally:

```bash
cd /path/to/prom-system
docker build -t prom-system:latest .
```

- Run with a MySQL container (example):

```bash
# Start a MySQL container
docker run -d --name prom-mysql -e MYSQL_ROOT_PASSWORD=secret -e MYSQL_DATABASE=prom -e MYSQL_USER=promuser -e MYSQL_PASSWORD=prompass mysql:8

# Run the app container
docker run -d --name prom-system -p 8080:8080 \
  -e MYSQLHOST=host.docker.internal -e MYSQLDATABASE=prom -e MYSQLUSER=promuser -e MYSQLPASSWORD=prompass \
  prom-system:latest
```

3) Railway or other hosts
- After GHCR push, provide the registry image URL to your host (Railway, Render, Fly, etc.) and set environment variables:
  - `MYSQLHOST`, `MYSQLDATABASE`, `MYSQLUSER`, `MYSQLPASSWORD`, `MYSQLPORT` (if not 3306)
  - `MOMO_PAYEE_CODE`, `MOMO_PAYEE_NAME` (optional)
  - `ADMIN_PORTAL_PASSWORD` (optional admin initial password)

- For Railway: either use Railway's Dockerfile deployment or set the image to `ghcr.io/<org>/prom-system:latest` in Railway and configure env vars.

4) Post-deploy verification
- Register a ticket, then check Admin → Payment Management to see the pending ticket.
- Upload payment proof and ensure it appears under pending.
- Approve the payment in Admin and verify the ticket status updates to `confirmed` and user sees confirmation on `complete-payment.php`.
