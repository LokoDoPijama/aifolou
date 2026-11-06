Run on local network normally:
docker run --rm -p 127.0.0.1:8080:10000 -e PORT=10000 aifolou

Run on local network no Docker:
php Aifolou/server.php 8080