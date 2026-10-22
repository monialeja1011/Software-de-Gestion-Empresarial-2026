<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->decimal('iva_percentage', 5, 2)
                ->default(0)
                ->after('markup_percentage');
        });

        Schema::table('sale_details', function (Blueprint $table) {
            $table->decimal('iva_percentage', 5, 2)
                ->default(0)
                ->after('subtotal');

            $table->decimal('iva_amount', 12, 2)
                ->default(0)
                ->after('iva_percentage');
        });
    }

    public function down(): void
    {
        Schema::table('sale_details', function (Blueprint $table) {
            $table->dropColumn(['iva_percentage', 'iva_amount']);
        });

        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn('iva_percentage');
        });
    }
};