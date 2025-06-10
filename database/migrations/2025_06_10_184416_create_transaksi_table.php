<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('transaksi', function (Blueprint $table) {
            $table->id();

            $table->foreignId('murid_id')->constrained('murids')->onDelete('cascade');
            $table->foreignId('orangtua_id')->constrained('users')->onDelete('cascade');
            
            $table->decimal('nominal', 15, 2);
            $table->string('bukti_transfer')->nullable(); // bisa menyimpan path file gambar

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transaksi');
    }
};
