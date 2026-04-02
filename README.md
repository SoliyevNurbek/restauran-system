# &#127869;&#65039; Restoran Boshqaruv Tizimi

<div align="center">

![Laravel](https://img.shields.io/badge/Laravel_13-FF2D20?style=for-the-badge&logo=laravel&logoColor=white)
![PHP](https://img.shields.io/badge/PHP_8.3+-777BB4?style=for-the-badge&logo=php&logoColor=white)
![MySQL](https://img.shields.io/badge/MySQL-4479A1?style=for-the-badge&logo=mysql&logoColor=white)
![Vite](https://img.shields.io/badge/Vite-646CFF?style=for-the-badge&logo=vite&logoColor=white)
![Tailwind CSS](https://img.shields.io/badge/Tailwind_CSS_4-06B6D4?style=for-the-badge&logo=tailwindcss&logoColor=white)
![License](https://img.shields.io/badge/License-MIT-111827?style=for-the-badge)

<h3>Restoran jarayonlarini yagona boshqaruv paneli orqali nazorat qilish uchun zamonaviy Laravel yechimi</h3>

<p>
  <a href="#-asosiy-imkoniyatlar"><img src="https://img.shields.io/badge/Ko'rish-Imkoniyatlar-16A34A?style=for-the-badge" alt="Imkoniyatlar"></a>
  <a href="#-ornatish"><img src="https://img.shields.io/badge/Boshlash-O'rnatish-2563EB?style=for-the-badge" alt="O'rnatish"></a>
  <a href="#-ishga-tushirish"><img src="https://img.shields.io/badge/Start-Ishga_tushirish-F59E0B?style=for-the-badge" alt="Ishga tushirish"></a>
</p>

</div>

## &#128204; Loyiha haqida

Ushbu loyiha restoran faoliyatini markazlashgan tarzda boshqarish uchun ishlab chiqilgan. Tizim administrator va boshqaruv xodimlariga menyu, buyurtmalar, stollar, mijozlar, xodimlar va hisobotlarni qulay interfeys orqali nazorat qilish imkonini beradi.

**Loyiha boshlig'i:** Soliyev Nurbek

## &#128640; Asosiy imkoniyatlar

- &#128274; `username` va `password` asosidagi custom autentifikatsiya
- &#129517; Sidebar va navbar bilan professional admin panel
- &#127767; Yorug' va qorong'i rejim (`localStorage` orqali)
- &#128202; Dashboard statistikasi va vizual grafiklar
- &#127836; Menyu va taomlar boshqaruvi
- &#128450;&#65039; Kategoriyalar CRUD moduli
- &#129534; Bir nechta item bilan buyurtma yaratish va boshqarish
- &#129681; Stollar boshqaruvi
- &#128101; Mijozlar bazasi
- &#128104;&#8205;&#127859; Xodimlar boshqaruvi
- &#128200; Hisobotlar va tahliliy ko'rsatkichlar
- &#9881;&#65039; Tizim sozlamalari
- &#128444;&#65039; Logo yuklash funksiyasi
- &#9989; Delete tasdiqlash modali
- &#9203; Loading holatli form tugmalari

## &#128736;&#65039; Texnologiyalar

- **Backend:** Laravel 13, PHP 8.3+
- **Frontend:** Blade, Tailwind CSS 4, Vite
- **Ma'lumotlar bazasi:** MySQL / MariaDB
- **Vizualizatsiya:** Chart.js

## &#128194; Loyiha tuzilmasi

- `app/Http/Controllers` - modul controllerlari
- `app/Models` - model va aloqalar
- `database/migrations` - jadval strukturasi
- `database/seeders` - demo ma'lumotlar
- `resources/views` - Blade sahifalar
- `routes/web.php` - web route'lar

## &#128203; Talablar

- PHP 8.3 yoki undan yuqori
- Composer
- Node.js va npm
- MySQL yoki MariaDB
- Open Server / XAMPP / Laragon / LAMP kabi lokal muhit

## &#9881;&#65039; O'rnatish

1. Loyihani lokal muhitga joylashtiring:

```bash
cd D:\OSPanel\home\restauran-system
```

2. PHP paketlarini o'rnating:

```bash
composer install
```

3. Frontend paketlarini o'rnating:

```bash
npm install
```

4. Muhit faylini tayyorlang:

```bash
copy .env.example .env
```

5. Ilova kalitini yarating:

```bash
php artisan key:generate
```

6. Storage link yarating:

```bash
php artisan storage:link
```

7. Ma'lumotlar bazasi sozlamalarini `.env` faylda kiriting:

```env
APP_NAME="Restoran Boshqaruv Tizimi"
APP_ENV=local
APP_DEBUG=true
APP_URL=http://127.0.0.1:8000

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=restaurandb
DB_USERNAME=root
DB_PASSWORD=
```

8. Migratsiya va seed ishga tushiring:

```bash
php artisan migrate --seed
```

## &#128273; Demo login

- **Login:** `admin`
- **Parol:** `password123`

## &#9654;&#65039; Ishga tushirish

Backend server:

```bash
php artisan serve
```

Frontend build/dev server:

```bash
npm run dev
```

Brauzer orqali oching:

```text
http://127.0.0.1:8000/login
```

## &#129514; Foydali buyruqlar

```bash
php artisan test
```

```bash
npm run build
```

```bash
composer run dev
```

## &#8505;&#65039; Muhim eslatma

Agar bazada eski jadvallar mavjud bo'lsa, toza muhit va yangi ma'lumotlar bazasi bilan ishlash tavsiya etiladi. Migratsiyalar loyiha strukturasi bilan mos holda ishga tushirilishi kerak.

## &#128196; Litsenziya

Ushbu loyiha **MIT** litsenziyasi asosida tarqatiladi.
