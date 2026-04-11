<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pages', function (Blueprint $table) {
            $table->id();
            $table->string('slug');
            $table->string('title');
            $table->longText('content');
            $table->unsignedInteger('version');
            $table->timestamp('published_at')->nullable();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->unique(['slug', 'version']);
            $table->index(['slug', 'published_at']);
        });

        DB::table('pages')->insert([
            [
                'slug' => 'terms-of-use',
                'title' => 'Foydalanish shartlari',
                'content' => "Ushbu tizimdan foydalanish orqali siz platformadan qonuniy va halol maqsadlarda foydalanishga rozilik bildirasiz.\n\nAkkaunt ma'lumotlarini himoya qilish foydalanuvchi zimmasida. Ruxsatsiz foydalanish aniqlansa, kirish vaqtincha cheklanishi mumkin.\n\nXizmat funksiyalari vaqt o'tishi bilan yangilanadi. Muhim o'zgarishlar yangi versiya sifatida e'lon qilinadi.",
                'version' => 1,
                'published_at' => now(),
                'updated_by' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'slug' => 'privacy-policy',
                'title' => 'Maxfiylik siyosati',
                'content' => "Ro'yxatdan o'tishda yuborilgan ism, telefon, username va obyekt nomi kabi ma'lumotlar tizimga ulanish jarayonini tashkil qilish uchun ishlatiladi.\n\nMa'lumotlar faqat xizmat ko'rsatish, xavfsizlik nazorati va aloqa uchun qayta ishlanadi. Ular ruxsatsiz uchinchi tomonga berilmaydi, qonun talab qilgan holatlar bundan mustasno.\n\nSiyosat yangilanganda yangi versiya nashr qilinadi va oxirgi yangilangan sana sahifada ko'rsatiladi.",
                'version' => 1,
                'published_at' => now(),
                'updated_by' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('pages');
    }
};
