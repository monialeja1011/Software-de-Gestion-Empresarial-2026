<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('purchase_details', function (Blueprint $table) {
            $table->decimal('iva_percentage', 5, 2)->default(0);
            $table->boolean('iva_included')->default(false);
            $table->decimal('base_subtotal', 12, 2)->default(0);
            $table->decimal('iva_amount', 12, 2)->default(0);
        });

        // Las órdenes anteriores se conservan sin IVA desglosado.
        DB::table('purchase_details')->update([
            'base_subtotal' => DB::raw('subtotal'),
        ]);
    }

    public function down(): void
    {
        Schema::table('purchase_details', function (Blueprint $table) {
            $table->dropColumn([
                'iva_percentage',
                'iva_included',
                'base_subtotal',
                'iva_amount',
            ]);
        });
    }
};