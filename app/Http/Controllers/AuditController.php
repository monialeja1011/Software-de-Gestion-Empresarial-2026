<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Spatie\Activitylog\Models\Activity;

class AuditController extends Controller
{
    public function index(Request $request): View
    {
        $filters = $request->validate([
            'module' => ['nullable', 'string', 'max:100'],
            'event' => ['nullable', 'in:created,updated,deleted,restored'],
            'user' => ['nullable', 'integer', 'exists:users,id'],
            'from' => ['nullable', 'date'],
            'to' => ['nullable', 'date', 'after_or_equal:from'],
        ]);

        $modules = Activity::query()
            ->whereNotNull('log_name')
            ->where('log_name', '<>', '')
            ->distinct()
            ->orderBy('log_name')
            ->pluck('log_name');

        $events = [
            'created' => 'Creado',
            'updated' => 'Actualizado',
            'deleted' => 'Eliminado',
            'restored' => 'Restaurado',
        ];

        $users = User::orderBy('name')->get(['id', 'name']);

        $activities = Activity::with(['causer', 'subject'])
            ->when($filters['module'] ?? null, fn ($query, $module) => $query->where('log_name', $module))
            ->when($filters['event'] ?? null, fn ($query, $event) => $query->where('event', $event))
            ->when($filters['user'] ?? null, fn ($query, $userId) => $query
                ->where('causer_type', User::class)
                ->where('causer_id', $userId))
            ->when($filters['from'] ?? null, fn ($query, $date) => $query->whereDate('created_at', '>=', $date))
            ->when($filters['to'] ?? null, fn ($query, $date) => $query->whereDate('created_at', '<=', $date))
            ->latest()
            ->paginate(30)
            ->withQueryString();

        return view('audit.index', compact('activities', 'modules', 'events', 'users', 'filters'));
    }
}
