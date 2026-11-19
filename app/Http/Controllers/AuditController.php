<?php

namespace App\Http\Controllers;

use Illuminate\View\View;
use Spatie\Activitylog\Models\Activity;

class AuditController extends Controller
{
    public function index(): View
    {
        $activities = Activity::with(['causer', 'subject'])
            ->latest()
            ->paginate(30);

        return view('audit.index', compact('activities'));
    }
}
