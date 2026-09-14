# Norix Security

Laravel + Filament platform for **Norix Security**: SEO-ready marketing site, careers with document uploads, candidate status dashboard, and full operations admin.

## Stack

- Laravel 13 + Blade + Tailwind
- Filament admin (`/admin`)
- SQLite locally / PostgreSQL on Render
- Candidate auth guard for application tracking

## Quick start

```bash
composer install
cp .env.example .env   # if needed
php artisan key:generate
touch database/database.sqlite
php artisan migrate --seed
npm install && npm run build
php artisan serve
```

- Website: http://localhost:8000  
- Admin: http://localhost:8000/admin — `admin@norixsecurity.co.uk` / `admin123`  
- Candidate login: http://localhost:8000/candidate/login (created on apply)

## Demo staff

| Email | Password | Role |
|-------|----------|------|
| admin@norixsecurity.co.uk | admin123 | admin |
| ops@norixsecurity.co.uk | ops123 | operations |
| careers@norixsecurity.co.uk | recruit123 | recruiter |
| finance@norixsecurity.co.uk | finance123 | finance |

## Deploy (Render)

See `render.yaml`. Connect the GitHub repo to [Render](https://render.com), create the free web service + Postgres, set `APP_URL`, run migrations/seed.

## Legacy

The previous Next.js/Firebase prototype is archived in `_legacy-next/`.
