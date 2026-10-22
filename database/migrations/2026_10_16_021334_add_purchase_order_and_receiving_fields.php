<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('purchases', function (Blueprint $table) {
            $table->string('order_number')->nullable()->unique();
            $table->string('status', 30)->default('recibida');
            $table->date('expected_delivery_date')->nullable();
            $table->unsignedSmallInteger('payment_term_days')->default(0);
            $table->date('due_date')->nullable();
            $table->timestamp('received_at')->nullable();
            $table->text('observations')->nullable();
            $table->string('payment_status', 20)->nullable();
        });

        // Las compras anteriores ya aumentaban el stock al registrarse;
        // se conservan como recibidas y se les asigna un número de OC.
        DB::table('purchases')
            ->orderBy('id')
            ->chunkById(100, function ($purchases): void {
                foreach ($purchases as $purchase) {
                    DB::table('purchases')
                        ->where('id', $purchase->id)
                        ->update([
                            'order_number' => 'OC-'.str_pad((string) $purchase->id, 6, '0', STR_PAD_LEFT),
                            'status' => $purchase->active ? 'recibida' : 'anulada',
                            'received_at' => $purchase->active
                                ? $purchase->purchase_date.' 00:00:00'
                                : null,
                        ]);
                }
            });

        Schema::table('purchase_details', function (Blueprint $table) {
            $table->unsignedInteger('received_quantity')->default(0)->after('quantity');
        });

        // El stock de las compras históricas ya fue sumado al inventario.
        DB::table('purchase_details')->update([
            'received_quantity' => DB::raw('quantity'),
        ]);

        Schema::table('products', function (Blueprint $table) {
            $table->decimal('cost_price', 12, 2)->nullable()->after('price');
            $table->decimal('markup_percentage', 5, 2)->default(0)->after('cost_price');
        });

        Schema::create('purchase_receipts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('purchase_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->timestamp('received_at');
            $table->text('observations')->nullable();
            $table->timestamps();
        });

        Schema::create('purchase_receipt_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('purchase_receipt_id')
                ->constrained('purchase_receipts')
                ->cascadeOnDelete();
            $table->foreignId('purchase_detail_id')
                ->constrained('purchase_details')
                ->cascadeOnDelete();
            $table->unsignedInteger('quantity');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('purchase_receipt_details');
        Schema::dropIfExists('purchase_receipts');

        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn(['cost_price', 'markup_percentage']);
        });

        Schema::table('purchase_details', function (Blueprint $table) {
            $table->dropColumn('received_quantity');
        });

        Schema::table('purchases', function (Blueprint $table) {
            $table->dropUnique('purchases_order_number_unique');
            $table->dropColumn([
                'order_number',
                'status',
                'expected_delivery_date',
                'payment_term_days',
                'due_date',
                'received_at',
                'observations',
                'payment_status',
            ]);
        });
    }
};