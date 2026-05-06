# Church CMS

Church Management and Reporting System built with Laravel, PostgreSQL, and Redis.

## Run With Docker

### Prerequisites

- Docker Engine
- Docker Compose plugin (`docker compose`)

### 1) Start services

```bash
docker compose up --build -d
```

This starts:

- `web` (Nginx) on `http://localhost:8080`
- `app` (PHP-FPM Laravel app)
- `postgres` (PostgreSQL)
- `redis` (Redis cache/queue)
- `worker` (queue worker)
- `scheduler` (Laravel scheduler loop)

### 2) Seed initial data

```bash
docker compose exec app php artisan db:seed
```

Default admin credentials:

- Email: `admin@church.org`
- Password: `Admin@1234`

### 3) Access app

Open `http://localhost:8080`.

## Useful Docker Commands

```bash
# See service logs
docker compose logs -f

# Run one-off artisan commands
docker compose exec app php artisan about

# Stop everything
docker compose down

# Stop and remove volumes (wipes database/cache data)
docker compose down -v
```

## Environment Notes

- Docker runtime variables are set in `docker-compose.yml`.
- `.env.example` is configured for Docker hostnames (`postgres`, `redis`).
- The app container auto-generates `APP_KEY` if missing and runs migrations on startup.
