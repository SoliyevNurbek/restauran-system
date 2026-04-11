<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Schema;
use Illuminate\View\View;

class AuditLogController extends Controller
{
    public function __invoke(): View
    {
        return view('superadmin.audit.index', [
            'pageTitle' => 'Audit loglari',
            'pageSubtitle' => 'Muhim superadmin harakatlari va before/after izlari.',
            'logs' => Schema::hasTable('audit_logs')
                ? AuditLog::query()->with('actor')->latest()->paginate(20)
                : new LengthAwarePaginator(collect(), 0, 20, 1, ['path' => request()->url(), 'query' => request()->query()]),
        ]);
    }
}
