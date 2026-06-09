<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class Kota extends Migration
{
    public function up(): void
    {
        Schema::create('kota', function (Blueprint $table) {
            $table->id();
            $table->string('nama_kota'); // Contoh: "Kota Bogor", "Kabupaten Bogor", "Kota Bandung"
            $table->string('provinsi')->default('Jawa Barat');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kota');
    }
}
