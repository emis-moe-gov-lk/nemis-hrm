# Docker Build And Share

This Laravel app should be run with Docker Compose because the app image runs `php-fpm` on port `9000`, while the HTTP server is the separate `nginx` service on port `80`.

If someone builds and runs only the `Dockerfile` image directly, they can get `502 Bad Gateway` because there is no HTTP server in that container.

## Build Locally

```bash
docker compose build --no-cache
```

## Start Locally

```bash
docker compose up -d
```

Open:

```text
http://localhost:8080
```

Check logs if the page does not load:

```bash
docker compose logs -f app nginx mysql
```

## Send The Image To Someone Else

Build the app image:

```bash
docker compose build app
```

Save it as a tar file:

```bash
docker save cemis-app:latest -o cemis-app.tar
```

Send these files to your friend:

```text
cemis-app.tar
the full project folder
docker-compose.yml
.env.docker
docker/nginx/default.conf
docker/entrypoint.sh
```

The full project folder is required because `docker-compose.yml` bind-mounts the local project into both the `app` and `nginx` containers. If your friend runs Compose from a folder that does not contain the Laravel project files, the bind mount will hide the app files from the image.

On your friend's machine, load the image:

```bash
docker load -i cemis-app.tar
```

Then start the full stack from the folder containing `docker-compose.yml`:

```bash
docker compose up -d
```

## Common Bad Gateway Cause

Do not run this as the only container:

```bash
docker run -p 8080:9000 cemis-app:latest
```

That exposes PHP-FPM directly, not a web server. Use `docker compose up -d` so Nginx can proxy requests to PHP-FPM correctly.
