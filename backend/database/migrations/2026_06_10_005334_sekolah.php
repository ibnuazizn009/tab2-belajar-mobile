<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class Sekolah extends Migration
{
    public function up(): void
    {
        Schema::create('sekolah', function (Blueprint $table) {
            $table->id();
            $table->string('npsn', 8)->unique();
            $table->string('nama_sekolah');
            $table->enum('status', ['NEGERI', 'SWASTA']);
            $table->string('alamat')->nullable();
            
            // Relasi Foreign Key ke tabel kota
            $table->foreignId('kota_id')->constrained('kota')->onDelete('cascade');
            
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sekolah');
    }
}
