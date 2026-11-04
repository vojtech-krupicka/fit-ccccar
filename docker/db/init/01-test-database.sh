#!/bin/sh
# Runs once, when the MySQL data volume is created (see docker-compose.yml).
# MYSQL_USER only gets MYSQL_DATABASE, so the test database <MYSQL_DATABASE>_test and its grant
# are created here. Existing volume: run the two statements once as the MySQL root user.
set -e
mysql -uroot -p"$MYSQL_ROOT_PASSWORD" <<SQL
CREATE DATABASE IF NOT EXISTS \`${MYSQL_DATABASE}_test\` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
GRANT ALL PRIVILEGES ON \`${MYSQL_DATABASE}_test\`.* TO '${MYSQL_USER}'@'%';
SQL
