#!/usr/bin/env bash
set -euo pipefail

# Build image locally
docker build -t prom-system:latest .

# Run container (example)
# Ensure you have a MySQL container or adjust env vars for your DB
# Example with local MySQL:
# docker run -d --name prom-mysql -e MYSQL_ROOT_PASSWORD=secret -e MYSQL_DATABASE=prom -e MYSQL_USER=promuser -e MYSQL_PASSWORD=prompass mysql:8

# Run web container (adjust ports/env as needed)
# docker run -d --name prom-system -p 8080:8080 \
#   -e MYSQLHOST=host.docker.internal -e MYSQLDATABASE=prom -e MYSQLUSER=promuser -e MYSQLPASSWORD=prompass \
#   prom-system:latest

echo "Built prom-system:latest. Configure DB env vars and run the container as shown in comments above."