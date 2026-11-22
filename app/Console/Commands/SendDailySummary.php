<?php

namespace App\Console\Commands;

use App\Models\Product;
use App\Models\Purchase;
use App\Models\Sale;
use App\Models\User;
use App\Notifications\DailySummaryNotification;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Notification;

class SendDailySummary extends Command
{
    protected $signature = 'erp:resumen-diario {--fecha=}';

    protected $description = 'Envía el resumen diario a quienes pueden ver reportes';

    public function handle(): int
    {
        $date = $this->option('fecha') ?: now()->toDateString();

        $summary = [
            'date' => $date,
            'sales_count' => Sale::where('status', '!=', 'anulada')
                ->whereDate('sale_date', $date)
                ->count(),
            'sales_total' => (float) Sale::where('status', '!=', 'anulada')
                ->whereDate('sale_date', $date)
                ->sum('total'),
            'purchases_total' => (float) Purchase::where('status', '!=', 'anulada')
                ->whereDate('purchase_date', $date)
                ->sum('total'),
            'pending_purchases' => Purchase::whereIn(
                'status',
                ['enviada', 'parcialmente_recibida']
            )->count(),
            'low_stock' => Product::where('active', true)
                ->where('stock', '<=', config('erp.low_stock_threshold', 5))
                ->orderBy('stock')
                ->pluck('name')
                ->all(),
        ];

        $recipients = User::permission('ver-reportes')->get();

        Notification::send($recipients, new DailySummaryNotification($summary));

        $this->info("Resumen del {$date} encolado para {$recipients->count()} usuario(s).");

        return self::SUCCESS;
    }
}
