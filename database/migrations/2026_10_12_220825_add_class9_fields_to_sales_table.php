<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('sales', function (Blueprint $table) {
            $table->string('invoice_number', 20)->nullable()->unique()->after('id');
            $table->foreignId('user_id')
                ->nullable()
                ->after('client_id')
                ->constrained()
                ->nullOnDelete();

            $table->decimal('subtotal', 14, 2)->default(0)->after('sale_date');
            $table->decimal('tax', 14, 2)->default(0)->after('subtotal');

            $table->enum('status', ['pendiente', 'pagada', 'anulada'])
                ->default('pagada')
                ->after('active');

            $table->text('notes')->nullable()->after('status');
            $table->softDeletes();
        });

        // Conserva los datos de las ventas que ya existían.
        foreach (DB::table('sales')->orderBy('id')->get(['id', 'total', 'active']) as $sale) {
            DB::table('sales')
                ->where('id', $sale->id)
                ->update([
                    'invoice_number' => 'FV-'.str_pad((string) $sale->id, 5, '0', STR_PAD_LEFT),
                    'subtotal' => $sale->total,
                    'tax' => 0,
                    'status' => $sale->active ? 'pagada' : 'anulada',
                ]);
        }
    }

    public function down(): void
    {
        Schema::table('sales', function (Blueprint $table) {
            $table->dropConstrainedForeignId('user_id');
            $table->dropUnique(['invoice_number']);

            $table->dropColumn([
                'invoice_number',
                'subtotal',
                'tax',
                'status',
                'notes',
                'deleted_at',
            ]);
        });
    }
};