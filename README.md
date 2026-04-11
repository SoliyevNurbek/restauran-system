# MyRestaurant_SN

Laravel 13.2 + PHP 8.3 asosidagi ko'p-tenant SaaS boshqaruv tizimi. Loyiha restoran va to'yxona bizneslari uchun tenant panel, superadmin nazorati, approval flow, SaaS subscription billing, hamda Telegram orqali manual payment workflow ni o'z ichiga oladi.

## Stack

- Laravel 13.2
- PHP 8.3
- Blade
- Tailwind CSS
- Vite
- MySQL / MariaDB

## Asosiy modullar

- Landing page va public content management
- Register, login, approval va business onboarding flow
- Tenant/admin panel
- Superadmin control center
- SaaS plans, subscriptions va payments
- Telegram manual payment workflow
- Audit log va admin notifications

## Telegram arxitekturasi

Loyiha bitta umumiy Telegram bot bilan ishlaydi:

- `bot_token` - bitta global bot token
- `admin_chat_id` - faqat superadmin alertlari uchun
- `telegram_chat_id` - har bir biznes uchun alohida chat target

Routing qoidasi:

- tenant billing va tenant alertlar -> faqat biznesning `telegram_chat_id`
- system va superadmin alertlar -> faqat `admin_chat_id`

## Muhim xavfsizlik qoidalari

- `.env` faylini hech qachon GitHub ga yuklamang
- production `APP_KEY`, DB credentials, mail credentials va boshqa maxfiy ma'lumotlarni repo ichida saqlamang
- Telegram bot token faqat panel orqali secure storage da saqlanadi
- payment proof fayllari private storage da saqlanadi

## Talablar

- PHP 8.3+
- Composer
- Node.js 20+
- npm
- MySQL 8+ yoki MariaDB

## O'rnatish

```bash
git clone <your-repository-url>
cd restauran-system
composer install
npm install
copy .env.example .env
php artisan key:generate
php artisan migrate
npm run build
```

## Lokal development

```bash
php artisan serve
npm run dev
```

Brauzer:

```text
http://127.0.0.1:8000
```

## Tavsiya etilgan .env sozlamalari

Boshlang'ich konfiguratsiya uchun `.env.example` dan foydalaning.

Asosiy maydonlar:

- `APP_URL`
- `DB_CONNECTION`, `DB_HOST`, `DB_PORT`, `DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD`
- `MAIL_*`
- `QUEUE_CONNECTION`
- `SESSION_*`

## Production checklist

1. `APP_ENV=production`
2. `APP_DEBUG=false`
3. `APP_FORCE_HTTPS=true`
4. Production database credentials kiriting
5. Queue, session va cache jadvallarini tayyorlang
6. `php artisan migrate --force`
7. `php artisan optimize`
8. `npm run build`
9. Public HTTPS domain ulang
10. Telegram webhook ni `POST /telegram/webhook` ga ulang

## Foydali buyruqlar

```bash
php artisan migrate
php artisan test
php artisan view:cache
php artisan route:list
npm run build
```

## GitHub uchun tavsiya

Repo ga quyidagilar kirmasligi kerak:

- `.env`
- `vendor/`
- `node_modules/`
- `storage/logs/*`
- build va cache fayllari

Shu sabab `.gitignore` production-safe ko'rinishda yangilangan.

## Litsenziya

Private / commercial project. Zarurat bo'lsa keyinroq alohida license policy qo'shiladi.
