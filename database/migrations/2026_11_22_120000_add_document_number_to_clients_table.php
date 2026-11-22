<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('clients', function (Blueprint $table) {
            // Nullable: los clientes que ya existen quedan sin cédula hasta que se edite su ficha.
            // Único: MySQL permite varios NULL, pero no dos cédulas iguales.
            $table->string('document_number', 20)->nullable()->unique()->after('name');
        });
    }

    public function down(): void
    {
        Schema::table('clients', function (Blueprint $table) {
            $table->dropUnique(['document_number']);
            $table->dropColumn('document_number');
        });
    }
};
