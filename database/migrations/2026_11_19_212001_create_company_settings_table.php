<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('company_settings', function (Blueprint $table) {
            $table->id();
            $table->string('trade_name', 120);
            $table->string('legal_name', 160)->nullable();
            $table->string('nit', 30)->nullable();
            $table->string('slogan', 160)->nullable();
            $table->string('address', 200)->nullable();
            $table->string('city', 100)->nullable();
            $table->string('phone', 30)->nullable();
            $table->string('email', 150)->nullable();
            $table->string('website', 200)->nullable();
            $table->string('logo_path')->nullable();
            $table->string('brand_color', 7)->default('#db2777');
            $table->timestamps();
        });

        DB::table('company_settings')->insert([
            'trade_name' => 'Sistema de Gestión Empresarial',
            'brand_color' => '#db2777',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('company_settings');
    }
};
