# Railway deploy qo'llanma

Bu loyiha Laravel app sifatida Railway'da `php-fpm + Caddy` bilan ishlaydi. Repo ichidagi `railway/*.sh` fayllari app, worker va cron servislarini production uchun tayyorlashga mo'ljallangan.

## 1. Railway loyihasini yaratish

1. Railway'da yangi project oching.
2. GitHub repo ni ulang yoki CLI orqali deploy qiling.
3. Shu project ichida alohida `MySQL` service yarating.
4. Zarurat bo'lsa keyin `worker` va `cron` uchun alohida servislar qo'shing.

## 2. App service sozlamalari

- Build Command: `npm run build`
- Pre-Deploy Command: `chmod +x ./railway/init-app.sh && sh ./railway/init-app.sh`
- Start Command: bo'sh qoldirish mumkin, Railway Laravel'ni o'zi aniqlaydi

## 3. Majburiy environment variables

Quyidagi qiymatlarni app service ga kiriting:

```env
APP_NAME="MyRestaurant_SN"
APP_ENV=production
APP_DEBUG=false
APP_URL=https://myrestoran.uz
APP_KEY=base64:...

APP_FORCE_HTTPS=true

LOG_CHANNEL=stderr
LOG_STACK=stderr
LOG_LEVEL=info

DB_CONNECTION=mysql
DB_HOST=${{MySQL.MYSQLHOST}}
DB_PORT=${{MySQL.MYSQLPORT}}
DB_DATABASE=${{MySQL.MYSQLDATABASE}}
DB_USERNAME=${{MySQL.MYSQLUSER}}
DB_PASSWORD=${{MySQL.MYSQLPASSWORD}}

SESSION_DRIVER=database
SESSION_SECURE_COOKIE=true
SESSION_SAME_SITE=lax

CACHE_STORE=database
QUEUE_CONNECTION=database
```

Izoh:

- Logo va favicon kabi media fayllar bazada saqlanadi.
- Payment proof va ayrim private eksportlar diskka yoziladi; ular saqlanib qolishi kerak bo'lsa, Railway `Volume` ulang yoki storage qatlamini `s3` diskka ko'chiring.
- `APP_KEY` ni lokalda `php artisan key:generate --show` bilan oling.
- `APP_URL` deploy yakunlangach `https://myrestoran.uz` bo'lishi kerak.

## 4. Worker service

Queue ishlatish kerak bo'lsa alohida service oching:

- Source Repo: shu repo
- Build Command: `npm run build`
- Start Command: `chmod +x ./railway/run-worker.sh && sh ./railway/run-worker.sh`

Environment variables app service bilan bir xil bo'lishi kerak.

## 5. Cron service

Scheduler ishlashi uchun alohida service oching:

- Source Repo: shu repo
- Build Command: `npm run build`
- Start Command: `chmod +x ./railway/run-cron.sh && sh ./railway/run-cron.sh`

Environment variables app service bilan bir xil bo'lishi kerak.

## 6. OpenServer'dan bazani ko'chirish

Lokal OpenServer MySQL'dan dump oling:

```bash
mysqldump -u root -p --single-transaction --routines --triggers --default-character-set=utf8mb4 myrestaurant_sn > myrestaurant_sn.sql
```

Keyin Railway MySQL'ga import qiling. Railway MySQL service ichida berilgan public TCP proxy host/port yoki Railway shell orqali ulanishingiz mumkin:

```bash
mysql -h <railway-host> -P <railway-port> -u <railway-user> -p <railway-db> < myrestaurant_sn.sql
```

Importdan keyin:

1. `migrations` jadvali to'g'ri to'lganini tekshiring.
2. `users`, `settings`, `media_files`, `media_assets` jadvallarida ma'lumotlar borligini tekshiring.
3. `php artisan migrate --force` ni bir marta ishlatib, qolgan migrationlar qo'llanganini tasdiqlang.

## 7. Domain ulash

1. Avval Railway app service uchun vaqtinchalik `*.up.railway.app` domain yarating va test qiling.
2. So'ng `myrestoran.uz` ni shu service ga ulang.
3. Agar domain Railway ichidan sotib olingan bo'lsa, DNS avtomatik boshqariladi.
4. Agar boshqa registratordan olingan bo'lsa, Railway ko'rsatgan DNS yozuvlarini qo'shing.
5. SSL sertifikat chiqqach `APP_URL=https://myrestoran.uz` qilib deployni qayta ishga tushiring.

## 8. Muhim production eslatmalar

- `storage/app/private` ichidagi fayllar Railway'da persistent emas.
- Payment proof yoki private export fayllari yo'qolmasligi uchun `Volume` yoki S3-compatible storage kerak.
- Telegram webhook ishlatsa, production URL ga yangilang:

```text
POST https://myrestoran.uz/telegram/webhook
```
