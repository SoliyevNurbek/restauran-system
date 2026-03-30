# Restoran Boshqaruv Tizimi (Laravel 13)

Zamonaviy restoran boshqaruv paneli:
- Laravel 13
- Blade templating
- Tailwind CSS (CDN)
- MySQL

## Asosiy imkoniyatlar

- Custom autentifikatsiya (`username` + `password`)
- Admin panel (sidebar + navbar)
- Yorug'/qorong'i rejim (`localStorage` bilan)
- Dashboard statistikasi va Chart.js grafiklari
- CRUD modullar:
  - Kategoriyalar
  - Menyu (taomlar)
  - Buyurtmalar (bir nechta item bilan)
  - Stollar
  - Mijozlar
  - Xodimlar
  - Hisobotlar
  - Sozlamalar
- Logo yuklash
- Delete tasdiqlash modali
- Loading holatli form tugmalari

## Talablar

- PHP 8.3+
- Composer
- MySQL (yoki MariaDB)
- XAMPP/LAMP (ixtiyoriy)

## O'rnatish

1. Loyihaga kiring:
```bash
cd /opt/lampp/htdocs/restauran-system
```

2. Bog'liqliklarni o'rnating:
```bash
composer install
```

3. Muhit faylini tekshiring (`.env`):
```env
APP_NAME="Restoran Boshqaruv Tizimi"
APP_ENV=local
APP_KEY=
APP_DEBUG=true
APP_URL=http://localhost

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=restaurandb
DB_USERNAME=root
DB_PASSWORD=
```

4. App key yarating:
```bash
php artisan key:generate
```

5. Storage link yarating:
```bash
php artisan storage:link
```

6. MySQL ishga tushganini tekshiring va migratsiya + seed:
```bash
php artisan migrate --seed
```

## Demo login

- Login: `admin`
- Parol: `password123`

## Ishga tushirish

```bash
php artisan serve
```

So'ng oching: `http://127.0.0.1:8000/login`

## Muhim eslatma

Agar bazada eski jadvallar qolgan bo'lsa:
- migratsiyalar idempotent qilingan (mavjud jadvalni qayta yaratmaydi)
- lekin toza muhit uchun yangi DB tavsiya etiladi.

## Loyiha tuzilmasi (qisqa)

- `app/Http/Controllers` — modul controllerlar
- `app/Models` — model va aloqalar
- `database/migrations` — schema
- `database/seeders` — demo ma'lumotlar
- `resources/views` — Blade sahifalar
- `routes/web.php` — web yo'nalishlar

## Litsenziya

MIT
