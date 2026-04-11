<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('landing_contents', function (Blueprint $table) {
            $table->id();
            $table->string('locale', 5)->unique();
            $table->string('hero_badge')->nullable();
            $table->string('hero_title')->nullable();
            $table->text('hero_text')->nullable();
            $table->string('hero_primary_cta')->nullable();
            $table->string('hero_secondary_cta')->nullable();
            $table->string('hero_microcopy')->nullable();
            $table->string('final_title')->nullable();
            $table->text('final_text')->nullable();
            $table->string('contact_title')->nullable();
            $table->text('contact_text')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('landing_contents');
    }
};
